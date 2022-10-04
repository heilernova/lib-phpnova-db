<?php
namespace PHPNova\Db;

use PDO;

class db
{
    /**
     * @param string $timezonte Example: "+00:00"
     */
    public static function setTimezone(string $timezone): void {
        $_ENV['nvx-db']['timezone'] = $timezone;
    }

    /**
     * @param 'mysql'  | 'pgslq' $type
     * @param string $host
     */
    public static function setDataConnection(string $type, $host, $user, $password, $dbname, $port = null): void {

    }

    public static function connect(): DbConnect {
        return new DbConnect();
    }

    public static function setDefaultPDO(PDO $pdo): void {
        $_ENV['nvx-db']['pdo'] = $pdo;
    }

    public function setParceExecuteCamelCaseToSnakeCase(): void {
        $_ENV['nvx-db']['config']['writing-style-execute'] = "snakecase";
    }
    public function setParceExecuteSnakeCaseToCamelCase(): void {
        $_ENV['nvx-db']['config']['writing-style-execute'] = "snakecase";
    }

    public function setParceResultSnakeCaseToCamelCase(): void {
        $_ENV['nvx-db']['config']['writing-style-execute'] = "camelcase";
    }

    public function setParceResultCamelCaseToSnakeCase(): void {
        $_ENV['nvx-db']['config']['writing-style-execute'] = "snakecase";
    }
}