<?php
include_once "db.php";
$lati = $_REQUEST['Lati'];
$longi = $_REQUEST['Longi'];
$distance=0.5;
$url = 'http://admin.chicagometers.com/Kiosk/RadiusArray/0?LngLats='.$longi.','.$lati.'&Miles='.$distance.'&';
$homepage = file_get_contents($url);
$phpArray = json_decode($homepage,true);

$addArray = array();
foreach($phpArray as $array)
{
	$address= $array['Address'];
	$addArray[]=$address;
}
$result=$addArray[0];
$length = sizeof($addArray);
for($i=1;$i<$length;$i++)
{
	$result=$result." : ".$addArray[$i];
}
echo $result;
?>
