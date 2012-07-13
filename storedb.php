<?php
include "db.php";
$address = $_REQUEST['add'];
$status = $_REQUEST['status'];
$test1=0;
$total=0;
$present=0;
if($address!=null)
{
	$test1=1;
	$test2=$address;
	$res = mysql_query("select parkingspaces,occupiedspaces from `backup_chicago_meters` where address='$address'");
	if(mysql_num_rows($res)!=0)
			$test2=1;
	$resArr = mysql_fetch_assoc($res);
	$total = $resArr['parkingspaces'];
	$present = $resArr['occupiedspaces'];
}

if($status=='Parking'&&($present<$total))
	$query = mysql_query("update `backup_chicago_meters` set occupiedspaces=$present+1 where address='$address'");
else if($status=='Unparking'&&$present>0)
	$query = mysql_query("update `backup_chicago_meters` set occupiedspaces=$present-1 where address='$address'");
	
//$send = "Query1 : ".$res." Query 2 : ".$query." total : ".$total." present : ".$present." Add : ".$address;
//$send = "Test : ".$test." total : ".$total." present : ".$present;
//"Query1 : ".$res." Query 2 : ".$query." Add : ".$address. " Status : ".$status;

$send = "Test1 : ".$test1." Test 2 : ".$test2;
echo $send;

unset($res);
unset($resArr);
unset($total);
unset($present);
unset($query);
unset($address);
unset($status);

?>
