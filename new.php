<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
      <title>Phone Park - Map</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <link rel="stylesheet" type="text/css" media="all" href="css/main.css" />
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyC6wUQFujAdK4nH-DwujL8vDUfGdag2clw&sensor=false">      
      </script>

<?php

//receiving arguments from the form in index.php
$add = $_POST['stadd'];
$dist = $_POST['distance'];
//when address is not null we can process the request
if($add!=null)
{
?>


<script type=text/javascript>

  	var address = '<? echo $add; ?>';
  	var distance = '<? echo $dist; ?>';
	var geocoder = new google.maps.Geocoder();
	//geo coding the given address into latitude and longitude
	geocoder.geocode( { 'address': address}, function(results, status) {
  	var location = results[0].geometry.location;
  	var lati = location.lat();
  	var longi = location.lng();
  	//calling page.php and passing lati and longi arguments to it
  	document.location = 'http://www.rajak.me/page.php?addlat='+lati+'&addlong='+longi+'&stadd='+address+'&dist='+distance;
  	
	});
  </script>
 <?
}
//when address entered is null, return to index.php
else
	echo "<script type=text/javascript>document.location = 'http://www.rajak.me/index.php' ;</script>";

 ?> 
  </head>

</html>

