<?php

require __DIR__ . '/../vendor/autoload.php';
use PHPNova\Db\Client;
use PHPNova\Db\db;


try {
    //code...
    
    $pdo = db::connect()->mysql('localhost', 'root', '', 'data_services');
    
    $client = new Client($pdo);
    
    
    $result = $client->executeCommand("SELECT * FROM address_vi_list WHERE code = ?", [ '97' ]);

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