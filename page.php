<?php
session_start();
$addlati = $_GET['addlat'];
$addlongi = $_GET['addlong'];
$staddress = $_GET['stadd'];
$distance = $_GET['dist'];

$url = 'http://admin.chicagometers.com/Kiosk/RadiusArray/0?LngLats='.$addlongi.','.$addlati.'&Miles='.$distance.'&';

$homepage = file_get_contents($url);
$phpArray = json_decode($homepage,true);

$addArray = array();
foreach($phpArray as $array)
{
	$address= $array['Address'];
	$addArray[]=$address;
}
$_SESSION['nearby'] = $addArray;
header("Location: index.php?addlat=$addlati&addlong=$addlongi&stadd=$staddress&dist=$distance");


?>

