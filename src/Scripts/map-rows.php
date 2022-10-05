<?php

/** @var PDOStatement */
$stmt = $stmt;
$dnsType = $config['dns-type'];
// $config = $config;
return $stmt->fetchAll(PDO::FETCH_FUNC, function() use ($stmt, $dnsType, $config) {
    $arguments = func_get_args();
    $parmas = [];
    foreach ($arguments as $key => $value) {
        $column = $stmt->getColumnMeta($key);
        $native_type = $column['native_type'];
        switch($dnsType) {
            case 'mysql':
                
                if ($native_type == 'NEWDECIMAL'){
                    $value = (float)$value;
                } else if ($native_type == 'BLOB' || $native_type == 'VAR_STRING'){
                    if (is_string($value)){
                        if (preg_match('/^\{?.+\}/', $value) || preg_match('/^\[?.+\]/', $value)){
                            $json = json_decode($value);
                            if (json_last_error() == JSON_ERROR_NONE){
                                $value = $json;
                            }
                        }
                    }
                }

                break;
            
            case 'pgsql':

                if ($native_type == 'numeric'){
                    $value = (float)$value;
                }

                if ($native_type == 'json'){
                    $value = json_decode($value);
                }
                break;
        }

        $name = $column['name'];

        if (array_key_exists('writing-style-result', $config)){
         
            if ($config['writing-style-result'] == 'snakecase-camelcase'){
                require_once __DIR__ . '/../Funcs/nvx_db_snakecase_to_camecase.php';
                $name = nvx_db_snakeccase_to_camecase($name);
            }
           
        }
        
        $parmas[$name] = $value;
    }

    return (object)$parmas;
});