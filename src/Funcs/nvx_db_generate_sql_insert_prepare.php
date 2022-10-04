<?php

function nvx_db_generate_sql_insert_prepare(object|array $values, string $table): string {
    $fields = "";
    $values_string = "";

    foreach($values as $key => $val){

        $fields .= ", `$key`";

        if (is_bool($val)){
            $values_string .= ", " . ($val ? 'TRUE' : 'FALSE');
        }else {
            $values_string .= ", :$key";
        }
    }

    return "INSERT INTO $table($fields) VALUES($values_string);";
}