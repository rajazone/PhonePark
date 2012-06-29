<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

   <head>
      <title>Phone Park - Map</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <link rel="stylesheet" type="text/css" media="all" href="css/main.css" />
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyC6wUQFujAdK4nH-DwujL8vDUfGdag2clw&sensor=false">
    </script>


      <div id="header">
      <img src="images/banner.jpg" />
      </div>

       </head>

<div class="container">
<div id="pagecontent">
<body>
	<div id=sidebar>

	<form name=addform action=index.php method=post />
		Street : <input type=text name=street /> <br />eg:W Harrison, W Taylor<br/>
		<input type=submit name=search value=search /><br />

	</form>

				

	</div>
  <div id="map" style="width: 650px; height: 450px;"></div>
  <div id="legend"><img src='images/legend.png' /> </div> 

<?php

$searchaddress = $_POST['street'];


include_once "db.php";

if($searchaddress==null)
{
	$query = mysql_query("select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters`");
}
else
{
	$query = mysql_query("select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters` where address like '%$searchaddress%'");

}
//Declaring arrays for storing the result of the query
$longis=array();
$latis=array();
$address=array();
$pspaces = array();
$ospaces = array();
$rate = array();
$timeLimit = array();
$dir = array();

//Looping through the resultant array of the query and storing it in corresponding field arrays
while($array=mysql_fetch_array($query))
{
	$longis[] = $array[1];
	$latis[] = $array[0];
	$address[]=$array[2];
	$pspaces[]=$array[3];
	$ospaces[]=$array[4];
	$rate[]=$array[5];
	$timeLimit[]=$array[6];
	$dir[]=$array[7];	
}

?>

<script type="text/javascript">

//Getting the PHP field arrays and storing it in JavaScript field arrays
var aa1 = new Array("<?php echo implode(":",$longis);?>");
var scriptLongi = aa1[0].split(":");
var bb1 = new Array("<?php echo implode(":",$latis);?>");
var scriptLati = bb1[0].split(":");
var cc1 = new Array("<?php echo implode(":",$address);?>");
var scriptAddress = cc1[0].split(":");
var dd1 = new Array("<?php echo implode(":",$pspaces);?>");
var scriptPspaces = dd1[0].split(":");
var ee1 = new Array("<?php echo implode(":",$ospaces);?>");
var scriptOspaces = ee1[0].split(":");
var ff1 = new Array("<?php echo implode(":",$rate);?>");
var scriptRate = ff1[0].split(":");
var gg1 = new Array("<?php echo implode(":",$timeLimit);?>");
var scriptTime = gg1[0].split(":");
var hh1 = new Array("<?php echo implode(":",$dir);?>");
var scriptDir = hh1[0].split(":");

var scriptSearch = '<?php echo $searchaddress; ?>';
var k = 1;
if(scriptSearch=='')
k=1411;
//map variable which is main method that displays the map
var map = new google.maps.Map(document.getElementById('map'), {
      		zoom: 15,
      		center: new google.maps.LatLng(scriptLati[k], scriptLongi[k]),
      		mapTypeId: google.maps.MapTypeId.ROADMAP
    	});

//Declaring infowindow for pop up message for markers
var infowindow = new google.maps.InfoWindow();
var highZoom = false;
var lowZoom=false;
var veryHighZoom = false;
var veryLowZoom=false;

//declaring the arrays for markers for storing different types of markers
var hrmarkers = new Array();
var vrmarkers = new Array();
var homarkers = new Array();
var vomarkers = new Array();
var hbmarkers = new Array();
var vbmarkers = new Array();

//Declaring the images for different markers for initial map
var marker, i;
var hrimage='images/hr/hrsmall.png', vrimage='images/vr/vrsmall.png',hoimage='images/hg/hgsmall.png',voimage='images/vg/vgsmall.png',vbimage='images/vb/vbsmall.png', hbimage='images/hb/hbsmall.png';

    
for (i = 0; i < scriptLati.length; i++)
{
	var maxtreshold = scriptPspaces[i]/2;
    	var mintreshold = scriptPspaces[i]/3;
    	if(scriptOspaces[i]>maxtreshold)
    	{
    		if(scriptDir[i]=='E'||scriptDir[i]=='W'||scriptDir[i]=='EAST'||scriptDir[i]=='WEST'||scriptDir[i]=='East'||scriptDir[i]=='West')
    		{
    			marker = new google.maps.Marker({
        			position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:hrimage
      			});
      			hrmarkers.push(marker);
      		}
      		else
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:vrimage
      			});
      			vrmarkers.push(marker);

      		}

      	}

      	else if(scriptOspaces[i]>mintreshold&&scriptOspaces[i]<maxtreshold)
    	{
    		if(scriptDir[i]=='E'||scriptDir[i]=='W'||scriptDir[i]=='EAST'||scriptDir[i]=='WEST'||scriptDir[i]=='East'||scriptDir[i]=='West')
    		{
    			marker = new google.maps.Marker({
        			position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:hbimage
      			});
      			hbmarkers.push(marker);
      		}
      		else
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:vbimage
      			});
      			vbmarkers.push(marker);

      		}

      	}



      	else
    	{
    		if(scriptDir[i]=='E'||scriptDir[i]=='W'||scriptDir[i]=='EAST'||scriptDir[i]=='WEST'||scriptDir[i]=='East'||scriptDir[i]=='West')
    		{
    			marker = new google.maps.Marker({
        			position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:hoimage
      			});
      			homarkers.push(marker);
      		}
      		else
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:voimage
      			});
      			vomarkers.push(marker);

      		}

      	}
      	google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(scriptAddress[i]+"<br> Pakring space: "+scriptPspaces[i]
          +"<br> Occupied Space: "+scriptOspaces[i]+"<br> Rate: "+scriptRate[i]+" Time Limit: "+scriptTime[i]);
          infowindow.open(map, marker);}
          })(marker, i));

}

google.maps.event.addListener(map, 'zoom_changed', function() 
{
var zoomLevel = map.getZoom();
//use count to utilize the highZoom for optimizing...
if(zoomLevel > 16 && zoomLevel <19)
	highZoom=true;
else if(zoomLevel <17&& zoomLevel >14)
	lowZoom=true;
else if(zoomLevel <15)
	veryLowZoom=true;
else if(zoomLevel > 18)
	veryHighZoom=true;

if(highZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrmedium.png');
  	for (i = 0; i < homarkers.length; i++)
  		homarkers[i].setIcon('images/hg/hgmedium.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbmedium.png');
  	for (i = 0; i < vomarkers.length; i++)
  		vomarkers[i].setIcon('images/vg/vgmedium.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrmedium.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbmedium.png');
  	highZoom=false;

}

if(veryHighZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrlarge.png');
  	for (i = 0; i < homarkers.length; i++)
  		homarkers[i].setIcon('images/hg/hglarge.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hblarge.png');
  	for (i = 0; i < vomarkers.length; i++)
  		vomarkers[i].setIcon('images/vg/vglarge.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrlarge.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vblarge.png');
  	veryHighZoom=false;

}


if(lowZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrsmall.png');
  	for (i = 0; i < homarkers.length; i++)
  		homarkers[i].setIcon('images/hg/hgsmall.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbsmall.png');
  	for (i = 0; i < vomarkers.length; i++)
  		vomarkers[i].setIcon('images/vg/vgsmall.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrsmall.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbsmall.png');
  	lowZoom=false;

}

if(veryLowZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrverysmall.png');
  	for (i = 0; i < homarkers.length; i++)
  		homarkers[i].setIcon('images/hg/hgverysmall.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbverysmall.png');
  	for (i = 0; i < vomarkers.length; i++)
  		vomarkers[i].setIcon('images/vg/vgverysmall.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrverysmall.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbverysmall.png');
  	veryLowZoom=false;

}


});



</script>
</div>
</div>


</body>
</html>

