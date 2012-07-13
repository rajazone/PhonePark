<?php
session_start();
$addarray = $_SESSION['serialized_data'];
foreach ($addarray as $add)
echo $add."<br />";
?>
