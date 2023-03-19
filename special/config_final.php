<?php
    define('USER', 'root');
    define('PASSWORD', '');
    define('HOST', 'localhost');
    define('DATABASE', 'patent');
    $connection = new PDO("mysql:host=".HOST.";dbname=".DATABASE, USER, PASSWORD, array(  
        PDO::ATTR_EMULATE_PREPARES => false,  
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
?>