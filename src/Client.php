<?php
namespace PHPNova\Db;

use Exception;
use PDO;
use PDOStatement;

class Client
{
    private readonly PDO $pdo;

    private array $_config = [];
    private string $defaultTable;

    public function __construct(PDO $pdo = null, array $config = [])
    {
        try {
            if ($pdo){
                $this->pdo = $pdo;
            }else{
                if (array_key_exists('pdo', $_ENV['nvx-db'])){
                    $this->pdo = $_ENV['nvx-db']['pdo'];
                }else{
                    throw new Exception("No se ha definido el objeto PDO por dafault");
                }
            }

            $timezone = $_ENV['nvx-db']['timezone'];

            if (array_key_exists('timezonte', $config)){
                $timezone = $config['timezonte'];
            }

            if ($timezone){
                $this->setTimezone($timezone);
            }

            # writing style
            if ($_ENV['nvx-db']['writing-style']['send']){
                $config['writing-style-send'] = $_ENV['nvx-db']['writing-style']['send'];
            }

            if ($_ENV['nvx-db']['writing-style']['result']){
                $config['writing-style-result'] = $_ENV['nvx-db']['writing-style']['result'];
            }

            $config['dns-type'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

            $this->_config = $config;

        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function setDefaultTable(string $name): void {
        $this->defaultTable = $name;
    }

    public function setTimezone(string $timezone): void {
        try {
            $dns = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($dns == 'mysql'){
                $this->pdo->exec("SET time_zone = '$timezone'");
            }

            if ($dns == 'pgsql'){
                $this->pdo->exec("SET TIME ZONE '$timezone'");
            }
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage());
        }
    }

    public function setHandleRow(): void {

    }

    public function setParceExecuteCamelCaseToSnakeCase(): void {
        $config['writing-style-execute'] = "snakecase";
    }
    public function setParceExecuteSnakeCaseToCamelCase(): void {
        $config['writing-style-execute'] = "snakecase";
    }

    public function setParceResultSnakeCaseToCamelCase(): void {
        $config['writing-style-result'] = "camelcase";
    }

    public function setParceResultCamelCaseToSnakeCase(): void {
        $config['writing-style-result'] = "snakecase";
    }

    private function execute(string $sql, ?array $params = null): PDOStatement|false {

        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($params);

            return $stmt;
        } catch (\Throwable $th) {
            $message = "Error al ejecutar el comando SQL\n\n";
            $message .= $th->getMessage() . "\n\n";
            $message .= "SQL: $sql";
            if ($params){
                $message .= "\nParams";
                foreach ($params as $key => $val){
                    $message .= "\n - $key:" . gettype($val) ." = " . $val;
                }

            }
            throw new DbException($message, (int)$th->getCode());
        }

        return false;
    }

    private function mapTableName(?string $table):string{
        try{
            return $table ? $table : $this->defaultTable;
        } catch(\Throwable $e){
            throw new Exception("No se a definido el nombre de la tabla por defecto en: " . Client::class . "->setDefaultTable");
        }
    }

    /**
     * @param array $params 
     */
    public function executeCommand(string $sql, array $params = null): Result {
        try {
            $stmt = $this->execute($sql, $params);
            return new Result($stmt, $this->_config);
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeInsert(array|object $values, string|null $table = null, array|string|null $returning = null): Result|false {
        try {
            $table = $this->mapTableName($table);
            $fields = "";
            $values_string = "";
            $params = [];
        
            foreach($values as $key => $val){
                if ($_ENV['nvx-db']['writing-style']['send']){
                    $x = $_ENV['nvx-db']['writing-style']['send'];
                    if ($x == "camelcase-snakecase"){
                        require_once __DIR__ . '/Funcs/nvx_db_camecase_to_snakecase.php';
                        $key = nvx_db_camecase_to_snakecase($key);
                    }
                }
                $fields .= ", `$key`";
                if (is_bool($val)){
                    $values_string .= ", " . ($val ? 'TRUE' : 'FALSE');
                }else{
                    $values_string .= ", :$key";
                    $params[$key] = $val;
                }
            }

            $fields = ltrim($fields, ', ');
            $values_string = ltrim($values_string, ', ');

            $sql_returning="";
            if ($returning){
                if (is_string($returning)){
                    $sql_returning = $returning;
                }else{
                    foreach ($returning as $val){
                        $sql_returning .= ", $val";
                    }
                    $sql_returning = ltrim($sql_returning, ', ');
                }
                $sql_returning = " RETURNING $sql_returning";
            }
        
            $stmt = $this->execute("INSERT INTO $table($fields) VALUES($values_string)$sql_returning;", $params);
            return $stmt ? new Result($stmt, $this->_config) : false;
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeUpdate(array|object $values, string $condition, ?array $conditionParams = null, ?string $table = null): void {
        try {
            $table = $this->mapTableName($table);
            $sql_values = "";
            $value_params = [];


            foreach($values as $key => $val){
                if (is_bool($val)){
                    $sql_values .= ", `$key` = " . ($val ? 'TRUE' : 'FALSE');
                }else {
                    $sql_values .= ", `$key` = :$key";
                    $value_params[$key] = $val;
                }
            }
            $sql_values = ltrim($sql_values, ', ');
            
            # Armamos la condición
            $sql_condition_params = [];
            $sql_condition = '';
            if ($condition){
                $sql_condition = $condition;

                if (str_contains($condition, '?')){
                    $index = -1;
                    $sql_condition = preg_replace_callback("/\?/", function($matches) use (&$index){
                        $index++;
                        return ":$index";
                    }, $condition);

                }

                $sql_condition = str_replace(':', ':pw_', $sql_condition);
                
                foreach($conditionParams as $key => $val){
                    $sql_condition_params["pw_$key"] = $val;
                }
            }else{
                if ($conditionParams){
                    throw new DbException("Falta ingresar el parametro [condition] en al función");
                }
            }

            # Ejecutamos la consulta SQL
            $stmt = $this->execute("UPDATE `$table` SET $sql_values WHERE $sql_condition", [... $value_params, ...$sql_condition_params]);
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeSelect(string|array $fields = '*', ?string $condition = null, ?array $params = null, ?string $table = null): Result|false {
        try {
            $table = $this->mapTableName($table);
            if ( is_array($fields) ){
                $tempo = "";
                foreach ( $fields as $value ){
                    $tempo .= ", `$value`";
                }
                $fields = ltrim($tempo, ', ');

            }
            $stmt = $this->execute("SELECT $fields FROM `$table`" . ($condition ? " WHERE $condition" : ''), $params);
            return $stmt ? new Result($stmt, $this->dirverName, $this->_handdleRowValue) : false;
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeDelete(string $condition, ?array $params = null, ?string $table = null): Result|false {
        try {
            $table = $this->mapTableName($table);
            $stmt = $this->execute("DELETE FROM `$table` WHERE $condition", $params);
            return $stmt ? new Result($stmt, $this->dirverName, $this->_config) : false;
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeProcedure(string $name, ?array $params = null): Result|false {
        try {
            $values = $params ? str_repeat(', ?', count($params)) : '';
            $stmt = $this->execute("CALL $name($values)");
            return $stmt ? new Result($stmt, $this->dirverName, $this->_config) : false;
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function executeFunction(string $name, ?array $params = null): Result|false {
        try {
            $values = $params ? str_repeat(', ?', count($params)) : '';
            $stmt = $this->execute("SELECT $name($values)");
            return $stmt ? new ResultFunction($stmt, $this->dirverName) : false;
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

}