<?php
//credentials are given for database
$dsn = 'mysql:dbname=dbname;host=host';
$user = 'username';
$password = 'password';

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}?>

