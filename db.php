<?php
	$connect=mysql_connect("socioeventscom.ipagemysql.com","rajakrish","pipeline45")
	or
	die("A connection to the server could not be established");
	$result=mysql_select_db("phonepark")
	or 
	die("Database could not be selected");
	?>