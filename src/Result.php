<?php
namespace PHPNova\Db;

use PDOStatement;

class Result
{
    public readonly int $rowCount;
    public readonly array $rows;
    public readonly array $fields;

    public function __construct(PDOStatement $stmt, $config)
    {
        try {
            
            $rows = require __DIR__ . '/Scripts/map-rows.php';

            $this->rows = $rows;
            $this->rowCount = $stmt->rowCount();

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}