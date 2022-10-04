<?php
namespace PHPNova\Db;

use PDOStatement;

class ResultFunction
{
    public readonly int $rowCount;
    public readonly mixed $result;

    public function __construct(PDOStatement $stmt, $config)
    {
        try {
            
            $rows = require __DIR__ . '/Scripts/map-rows.php';

            // $this->result = $rows[0]
            $this->rowsCount = $stmt->rowCount();

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}