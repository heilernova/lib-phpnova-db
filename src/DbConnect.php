<?php
namespace PHPNova\Db;

use PDO;

class DbConnect
{
    public function mysql(string $host, string $user, $password, string $dbname, $port = null): PDO {
        try {
            return new PDO("mysql:host=$host; dbname=$dbname;" . ($port ? " port=$port;" : ''), $user, $password);

        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function pgsql(string $host, string $user, $password, string $dbname, $port = null): PDO {
        try {
            return new PDO("pgsql:host=$host; dbname=$dbname;" . ($port ? " port=$port;" : ''), $user, $password);
        } catch (\Throwable $th) {
            throw new DbException($th->getMessage(), $th->getCode());
        }
    }

    public function access(){

    }
}