<?php

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// echo  $_ENV["HOST"]."  ". $_ENV["DB_USER"]."  ". $_ENV["PASSWORD"]."  ". $_ENV["DATABASE"]."  ";

// connecting to MySQL DB
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    # host, user , password, database name
    $mysqli = new mysqli( $_ENV["HOST"], $_ENV["DB_USER"], $_ENV["PASSWORD"], $_ENV["DATABASE"]);

    // $mysqli = new mysqli( "localhost", "root", "root", "vpp");
    // $mysqli->set_charset("utf8mb4");
}
catch(Exception $e) {
    error_log($e->getMessage());
    exit("Error connecting to database"); //Should be a message a typical user could understand
}