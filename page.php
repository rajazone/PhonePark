<?php
//session is started
session_start();

//Getting arguments from the new.php - javascript
$addlati = $_GET['addlat'];
$addlongi = $_GET['addlong'];
$staddress = $_GET['stadd'];
$distance = $_GET['dist'];

//constructing the url for chicagometers.com api with the received arguments
$url = 'http://admin.chicagometers.com/Kiosk/RadiusArray/0?LngLats='.$addlongi.','.$addlati.'&Miles='.$distance.'&';

//getting the page content
$homepage = file_get_contents($url);

//getting it in json array
$phpArray = json_decode($homepage,true);

$addArray = array();
foreach($phpArray as $array)
{
	//pulling address value and storing it in address array
	$address= $array['Address'];
	$addArray[]=$address;
}
//storing the array of addresses in SESSION variable - new session it will be null
$_SESSION['nearby'] = $addArray;

//redirected to index.php with the lat,long,add,dist arguments passed - nearby address array is received in index.php through SESSION
header("Location: index.php?addlat=$addlati&addlong=$addlongi&stadd=$staddress&dist=$distance");


?>

