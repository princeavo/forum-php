<?php
    $dsn = "mysql:host=localhost;dbname=frenchCodeur";
    $pdo = new PDO($dsn,"root","",[
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);