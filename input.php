<?php
//Getting arguments from android app through http post
$lati = $_REQUEST['Lati'];
$longi = $_REQUEST['Longi'];
//default distance is set as 0.5 miles - this is used to find the parking space around received lat,lng with in 'X' miles
$distance=0.5;

//url is constructed with the passed arguments and chicagometers.com api
$url = 'http://admin.chicagometers.com/Kiosk/RadiusArray/0?LngLats='.$longi.','.$lati.'&Miles='.$distance.'&';

//content of the page is stored
$homepage = file_get_contents($url);

//content is stored as json array
$phpArray = json_decode($homepage,true);

$addArray = array();

//looping through each object of json array
foreach($phpArray as $array)
{
	//pulling address value and storing it in address array
	$address= $array['Address'];
	$addArray[]=$address;
}
//closest parking space is stored in $result
$result=$addArray[0];

//sending result back to android
echo $result;
?>
