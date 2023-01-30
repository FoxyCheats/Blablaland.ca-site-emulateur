<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=bbl", "foxy", "S9979gDwhaRq6Xx93NuG"); //x array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}
catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
