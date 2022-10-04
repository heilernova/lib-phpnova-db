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

    public static function setParceExecuteCamelCaseToSnakeCase(): void {
        $_ENV['nvx-db']['writing-style']['send'] = "camelcase-snakecase";
    }
    public static function setParceExecuteSnakeCaseToCamelCase(): void {
        $_ENV['nvx-db']['writing-style']['send'] = "snakecase-camelcase";
    }

    public static function setParceResultSnakeCaseToCamelCase(): void {
        $_ENV['nvx-db']['writing-style']['result'] = "snakecase-camelcase";
    }

    public static function setParceResultCamelCaseToSnakeCase(): void {
        $_ENV['nvx-db']['writing-style']['result'] = "camelcase-snakecase";
    }
}