<?php

require __DIR__ . '/../vendor/autoload.php';
use PHPNova\Db\Client;
use PHPNova\Db\db;


try {
    //code...
    
    $pdo = db::connect()->mysql('localhost', 'root', '', 'ftc_assosiations');
    // db::setParceResultSnakeCaseToCamelCase();
    // db::setParceExecuteCamelCaseToSnakeCase();
    // db::setTimezone('+05:00');
    
    $client = new Client($pdo);

    $client->setTimezone('-05:00');
    
    // $client->executeInsert(['dni' => '1007244089', 'dniType' => 'CC', 'name' => 'Heiler', 'lastName' => 'Nova'], 'persons_tb_naturals');
    
    $result = $client->executeSelect( table:'tb_associations_affiliates');
    // $result = $client->executeCommand("SELECT now()");

    $content = json_encode($result->rows, 128);
    header('Content-Type: application/json; charse=utf-8');
    $content = utf8_encode($content);
} catch (\Throwable $th) {
    //throw $th;

    $msg = $th->getMessage();
    $msg .= "\n\n";
    $msg .= "File: ". $th->getFile();
    $msg .= "\nLine: " . $th->getLine();
    $content = $msg;
    
}

echo $content;
// echo '<p style="font-size: 16px; font-family: Cascadia Code; white-space: pre-wrap;">' . $content . "</p>";

// // $client->setpar

// // // $client->execute->command()
// // $client->setDefaultTable('tb_users');

// // $client->executeInsert()

// $pd = new PDO("");

// // $pd->getAttribute(PDO::attr_de)

// $name = "date_expiration";

// echo nvx_db_snakeccase_to_camecase($name) . "<br>";
// echo nvx_db_camecase_to_snakecase("johanHeiler");


// $pdo = new PDO("mysql:host=localhost; dbname=crossfit", 'root', '');
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERR_NONE);
// $stmt = $pdo->query("select * from tb_users_1");

// if ($stmt){
//     echo "SI";
// }else{
//     echo "Error: " . json_encode($pdo->errorInfo(), 128) ;
// }