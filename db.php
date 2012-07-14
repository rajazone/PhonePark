<?php
<<<<<<< HEAD
	$connect=mysql_connect("host","username","password")
	or
	die("A connection to the server could not be established");
	$result=mysql_select_db("dbname")
	or 
	die("Database could not be selected");
	?>
=======

//credentials are given for database
$dsn = 'mysql:dbname=dbname;host=host';
$user = 'username';
$password = 'password';

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}?>
>>>>>>> Commit with DB to PDO MySQL
