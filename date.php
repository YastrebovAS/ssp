<?php

$connection = new PDO("mysql:host= localhost; dbname=patent", 'root', '', array(
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,));

$today = date('y-m-d');
$date = date('y-m-d',(strtotime($today.' + 14 days')));
$connection->exec('LOCK TABLES deletion WRITE');
$warning = $connection->prepare("INSERT INTO deletion VALUES('guuug', '2023-04-05', 'cringe', 3)");
$warning ->execute();
?>
