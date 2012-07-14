<?php
include 'db.php';
$address = $_REQUEST['add'];
$status = $_REQUEST['status'];
$result="Unsuccessful";
if($address!=null)
{
	$stmt = $db->prepare("select parkingspaces,occupiedspaces from `backup_chicago_meters` where address=? Limit 0,10");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total = $row['parkingspaces'];
	$present = $row['occupiedspaces'];
}

if($status=='Parking'&&($present<$total))
{
	$stmt = $db->prepare("update `backup_chicago_meters` set occupiedspaces=$present+1 where address=?");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$affected_rows = $stmt->rowCount();
	if($affected_rows>0)
		$result="Successful";
	
}
else if($status=='Unparking'&&$present>0)
{
	$stmt = $db->prepare("update `backup_chicago_meters` set occupiedspaces=$present-1 where address=?");
	$stmt->bindValue(1, $address, PDO::PARAM_STR);
	$stmt->execute();
	$affected_rows = $stmt->rowCount();
	if($affected_rows>0)
		$result="Successful";	
}
	
$send = $result." ".$status." at ".$address;
echo $send;
?>
