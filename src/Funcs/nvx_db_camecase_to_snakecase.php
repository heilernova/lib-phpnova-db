<?php

function nvx_db_camecase_to_snakecase(string $string) {
    return preg_replace_callback('/[A-Z]/', subject: $string, callback: function($item){
        return "_" . strtolower($item[0]);
    });
}