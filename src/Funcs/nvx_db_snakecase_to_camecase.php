<?php

function nvx_db_snakeccase_to_camecase(string $string) {
    return preg_replace_callback('/_\w/', subject: $string, callback: function($item){
        return strtoupper(str_replace('_', '', $item[0]));
    });
}