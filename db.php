<?php
	$connect=mysql_connect("host","username","password")
	or
	die("A connection to the server could not be established");
	$result=mysql_select_db("dbname")
	or 
	die("Database could not be selected");
	?>