<?php
namespace PHPNova\Db;

use Exception;

class DbException extends Exception
{
    public function __construct(string $message,int $code = 0)
    {
        $this->message = $message;
        $this->code = $code;

        $backtrace = debug_backtrace()[1] ?? null;

        if ($backtrace){
            $this->file = $backtrace['file'];
            $this->line = $backtrace['line'];
        }
    }
}