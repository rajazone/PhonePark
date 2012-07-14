<?php
//including db file
include 'db.php';

//getting arguments from android http post
$address = $_REQUEST['add'];
$status = $_REQUEST['status'];
$result="Unsuccessful";
if($address!=null)
{
	//query to get total and occupied parking spaces in the searched address
	$stmt = $db->prepare("select parkingspaces,occupiedspaces from `backup_chicago_meters` where address=? Limit 0,10");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total = $row['parkingspaces'];
	$present = $row['occupiedspaces'];
}

//corner condition - cannot park if parking space is full
if($status=='Parking'&&($present<$total))
{
	//query to update the DB - increasing the occupied space count for the specified address
	$stmt = $db->prepare("update `backup_chicago_meters` set occupiedspaces=$present+1 where address=?");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$affected_rows = $stmt->rowCount();
	//if query is successful $result is successful - otherwise, initial - unsuccessful
	if($affected_rows>0)
		$result="Successful";
	
}
else if($status=='Unparking'&&$present>0)
{
	//query to update the DB - decreasing the occupied space count for the specified address
	$stmt = $db->prepare("update `backup_chicago_meters` set occupiedspaces=$present-1 where address=?");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$affected_rows = $stmt->rowCount();
	//if query is successful $result is successful - otherwise, initial - unsuccessful
	if($affected_rows>0)
		$result="Successful";	
}

//message is constructed	
$send = $result." ".$status." at ".$address;

//message is send to android which will be toasted on the screen
echo $send;
?>
