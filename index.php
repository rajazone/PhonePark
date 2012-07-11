<?php
include_once "db.php";
$searchaddress=null;
$searchaddress = $_POST['street'];
$addlat = $_GET['addlat'];
$addlong = $_GET['addlong'];
$staddress = $_GET['stadd'];
if($searchaddress==null&&$addlat==null&&$addlong==null)
{
	$query = mysql_query("select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters`");
}
if($searchaddress!=null&&$addlat==null&&$addlong==null)
{
	$query = mysql_query("select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters` where address like '%$searchaddress%'");
}
if($addlat!=null && $addlong!=null)
{
	$fromlat=$addlat-0.005;
	$tolat=$addlat+0.005;
	$fromlong=$addlong-0.005;
	$tolong=$addlong+0.005;
	$query = mysql_query("select latitude , longitude , address , parkingspaces , occupiedspaces , dayratehour , daytimelimit , direction from `backup_chicago_meters` where longitude between $fromlong and $tolong or latitude between $fromlat and $tolat");
}

if($query)
{
	if(mysql_num_rows($query)!=0)
	{
	

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
			$latis[] = $array[0];
			$longis[] = $array[1];
			$address[]=$array[2];
			$pspaces[]=$array[3];
			$ospaces[]=$array[4];
			$rate[]=$array[5];
			$timeLimit[]=$array[6];
			$dir[]=$array[7];	
		}
		
	}
	else
	{
		$message=1;
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<?php

$result = mysql_query("SELECT address FROM backup_chicago_meters");

while($row = mysql_fetch_array($result))
  {
	  $addr=$addr.",".$row['address'];

  }
  ?>


   <head>
      <title>Phone Park - Map</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <link rel="stylesheet" type="text/css" media="all" href="css/main.css" />
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyC6wUQFujAdK4nH-DwujL8vDUfGdag2clw&sensor=false">      
      </script>
	  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  	  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
      
      <div id="header">
      <img src="images/banner.jpg" />
      </div>
	  <script type="text/javascript">



	  	$(document).ready(function() {
	  		var temp=document.getElementById("hid_value").value;
			var addrList=temp.split(",");
	        $("#street").autocomplete({
    		   source: function(request, response) {
        	     var results = $.ui.autocomplete.filter(addrList, request.term);
	             response(results.slice(0, 20));
    	       }    	       
        	});  
        });


       
	</script>
       </head>

<div class="container">
<div id="pagecontent">
<body>
	<div id=sidebar>

  		<a href="index.php">Home</a>
  		

  	<div id=screenshot>
  	<a href="phonepark-maps.pdf">Screens</a>
  	</div>
  	
  	
	<form name=addform action=index.php method=post />
		<input type=text id="street" name=street />
		<input type=submit name=search value="search by street" />
		<br />
		<input type=hidden id="hid_value" name=addr_values value="<?php echo $addr; ?>" >
	</form>


	<div>
  		<form name=searchform method=post action=new.php>
    		<input name=stadd type=text />
    		<input type=submit value="Search by Address" />
    		</form>
  	</div>

  	<div>
  		<?php
  			if($searchaddress!=null)
  			{
  				echo "Parking spaces for ". $searchaddress;
  			}
  			if($staddress!=null)
  			{
  				echo "Parking spaces near ".$stadd;
  			} 
  		?>
  	</div>
  	  	<div id="legend"><img src='images/legend.png' alt="legend" /> </div> 

	</div>
  <div id="map" style="width: 675px; height: 440px;">
  <?php
  if($message==1)
  	echo "Address not in DB"; 
  ?></div>
 
  		
  		

  	
  


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
var scriptaddLat = '<?php echo $addlat; ?>';
var scriptaddLong = '<?php echo $addlong; ?>';
var scriptStAdd = '<?php echo $staddress; ?>';
var k = 0;
if(scriptSearch=='')
k=1411;
if(scriptaddLat!='')
{
	var myLatlng = new google.maps.LatLng(scriptaddLat,scriptaddLong);
    var myOptions = {
      zoom: 16,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById('map'), myOptions);
    var addmarker = new google.maps.Marker({
        position: myLatlng, 
        map: map,
        title:scriptStAdd
    });

   
   
}
else{
//map variable which is main method that displays the map
var map = new google.maps.Map(document.getElementById('map'), {
      		zoom: 15,
      		center: new google.maps.LatLng(scriptLati[k], scriptLongi[k]),
      		mapTypeId: google.maps.MapTypeId.ROADMAP
    	});
    	}

//Declaring infowindow for pop up message for markers
var infowindow = new google.maps.InfoWindow();
var highZoom = false;
var lowZoom=false;
var veryHighZoom = false;
var veryLowZoom=false;

//declaring the arrays for markers for storing different types of markers
var hrmarkers = new Array();
var vrmarkers = new Array();
var nermarkers= new Array();
var sermarkers= new Array();
var hgmarkers = new Array();
var vgmarkers = new Array();
var negmarkers= new Array();
var segmarkers= new Array();
var hbmarkers = new Array();
var vbmarkers = new Array();
var nebmarkers= new Array();
var sebmarkers= new Array();
var negmarkers30= new Array();
var segmarkers30= new Array();
var nebmarkers30= new Array();
var sebmarkers30= new Array();
var nermarkers30= new Array();
var sermarkers30= new Array();

//Declaring the images for different markers for initial map
var marker, i;
var hrimage='images/hr/hrsmall.png', vrimage='images/vr/vrsmall.png',hgimage='images/hg/hgsmall.png',vgimage='images/vg/vgsmall.png',vbimage='images/vb/vbsmall.png', hbimage='images/hb/hbsmall.png', nerimage='images/ner/nersmall.png', negimage='images/neg/negsmall.png', nebimage='images/neb/nebsmall.png', nerimage30='images/ner30/nersmall30.png', negimage30='images/neg30/negsmall30.png', nebimage30='images/neb30/nebsmall30.png',serimage='images/ser/sersmall.png', segimage='images/seg/segsmall.png', sebimage='images/seb/sebsmall.png';

    
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
      		else if(scriptDir[i]=='NE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:nerimage
      			});
      			nermarkers.push(marker);

      		}
      		else if(scriptDir[i]=='SE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:serimage
      			});
      			sermarkers.push(marker);

      		}
      		else if(scriptDir[i]=='NE30')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:nerimage30
      			});
      			nermarkers30.push(marker);

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
      		else if(scriptDir[i]=='NE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:nebimage
      			});
      			nebmarkers.push(marker);

      		}
      		else if(scriptDir[i]=='SE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:sebimage
      			});
      			sebmarkers.push(marker);

      		}
      		else if(scriptDir[i]=='NE30')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:nebimage30
      			});
      			nebmarkers30.push(marker);

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
        			icon:hgimage
      			});
      			hgmarkers.push(marker);
      		}
      		else if(scriptDir[i]=='NE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:negimage
      			});
      			negmarkers.push(marker);

      		}
      		else if(scriptDir[i]=='SE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:segimage
      			});
      			segmarkers.push(marker);

      		}
      		else if(scriptDir[i]=='NE30')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:negimage30
      			});
      			negmarkers30.push(marker);

      		}
      		else
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:vgimage
      			});
      			vgmarkers.push(marker);

      		}

      	}
      	google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(scriptAddress[i]+"<br> Total space: "+scriptPspaces[i]
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
  	for (i = 0; i < hgmarkers.length; i++)
  		hgmarkers[i].setIcon('images/hg/hgmedium.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbmedium.png');
  	for (i = 0; i < vgmarkers.length; i++)
  		vgmarkers[i].setIcon('images/vg/vgmedium.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrmedium.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbmedium.png');
  	for (i = 0; i < negmarkers.length; i++)
  		negmarkers[i].setIcon('images/neg/negmedium.png');
  	for (i = 0; i < nermarkers.length; i++)
  		nermarkers[i].setIcon('images/ner/nermedium.png');
  	for (i = 0; i < nebmarkers.length; i++)
  		nebmarkers[i].setIcon('images/neb/nebmedium.png');
  	for (i = 0; i < negmarkers30.length; i++)
  		negmarkers30[i].setIcon('images/neg30/negmedium30.png');
  	for (i = 0; i < nermarkers30.length; i++)
  		nermarkers30[i].setIcon('images/ner30/nermedium30.png');
  	for (i = 0; i < nebmarkers30.length; i++)
  		nebmarkers30[i].setIcon('images/neb30/nebmedium30.png');
  	for (i = 0; i < segmarkers.length; i++)
  		segmarkers[i].setIcon('images/seg/segmedium.png');
  	for (i = 0; i < sermarkers.length; i++)
  		sermarkers[i].setIcon('images/ser/sermedium.png');
  	for (i = 0; i < sebmarkers.length; i++)
  		sebmarkers[i].setIcon('images/seb/sebmedium.png');
  	
  	
  	highZoom=false;

}

if(veryHighZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrlarge.png');
  	for (i = 0; i < hgmarkers.length; i++)
  		hgmarkers[i].setIcon('images/hg/hglarge.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hblarge.png');
  	for (i = 0; i < vgmarkers.length; i++)
  		vgmarkers[i].setIcon('images/vg/vglarge.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrlarge.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vblarge.png');
  	for (i = 0; i < negmarkers.length; i++)
  		negmarkers[i].setIcon('images/neg/neglarge.png');
  	for (i = 0; i < nermarkers.length; i++)
  		nermarkers[i].setIcon('images/ner/nerlarge.png');
  	for (i = 0; i < nebmarkers.length; i++)
  		nebmarkers[i].setIcon('images/neb/neblarge.png');
  	for (i = 0; i < negmarkers30.length; i++)
  		negmarkers30[i].setIcon('images/neg30/neglarge3030.png');
  	for (i = 0; i < nermarkers30.length; i++)
  		nermarkers30[i].setIcon('images/ner30/nerlarge30.png');
  	for (i = 0; i < nebmarkers30.length; i++)
  		nebmarkers30[i].setIcon('images/neb30/neblarge30.png');
  	for (i = 0; i < segmarkers.length; i++)
  		segmarkers[i].setIcon('images/seg/seglarge.png');
  	for (i = 0; i < sermarkers.length; i++)
  		sermarkers[i].setIcon('images/ser/serlarge.png');
  	for (i = 0; i < sebmarkers.length; i++)
  		sebmarkers[i].setIcon('images/seb/seblarge.png');
  	
  	
  	veryHighZoom=false;

}


if(lowZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrsmall.png');
  	for (i = 0; i < hgmarkers.length; i++)
  		hgmarkers[i].setIcon('images/hg/hgsmall.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbsmall.png');
  	for (i = 0; i < vgmarkers.length; i++)
  		vgmarkers[i].setIcon('images/vg/vgsmall.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrsmall.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbsmall.png');
  	for (i = 0; i < negmarkers.length; i++)
  		negmarkers[i].setIcon('images/neg/negsmall.png');
  	for (i = 0; i < nermarkers.length; i++)
  		nermarkers[i].setIcon('images/ner/nersmall.png');
  	for (i = 0; i < nebmarkers.length; i++)
  		nebmarkers[i].setIcon('images/neb/nebsmall.png');
  	for (i = 0; i < negmarkers30.length; i++)
  		negmarkers30[i].setIcon('images/neg30/negsmall30.png');
  	for (i = 0; i < nermarkers30.length; i++)
  		nermarkers30[i].setIcon('images/ner30/nersmall30.png');
  	for (i = 0; i < nebmarkers30.length; i++)
  		nebmarkers30[i].setIcon('images/neb30/nebsmall30.png');
  	for (i = 0; i < segmarkers.length; i++)
  		segmarkers[i].setIcon('images/seg/segsmall.png');
  	for (i = 0; i < sermarkers.length; i++)
  		sermarkers[i].setIcon('images/ser/sersmall.png');
  	for (i = 0; i < sebmarkers.length; i++)
  		sebmarkers[i].setIcon('images/seb/sebsmall.png');
  	
  	lowZoom=false;

}

if(veryLowZoom==true)
{
	for (i = 0; i < hrmarkers.length; i++)
  		hrmarkers[i].setIcon('images/hr/hrverysmall.png');
  	for (i = 0; i < hgmarkers.length; i++)
  		hgmarkers[i].setIcon('images/hg/hgverysmall.png');
  	for (i = 0; i < hbmarkers.length; i++)
  		hbmarkers[i].setIcon('images/hb/hbverysmall.png');
  	for (i = 0; i < vgmarkers.length; i++)
  		vgmarkers[i].setIcon('images/vg/vgverysmall.png');
  	for (i = 0; i < vrmarkers.length; i++)
  		vrmarkers[i].setIcon('images/vr/vrverysmall.png');
  	for (i = 0; i < vbmarkers.length; i++)
  		vbmarkers[i].setIcon('images/vb/vbverysmall.png');
  	for (i = 0; i < negmarkers.length; i++)
  		negmarkers[i].setIcon('images/neg/negverysmall.png');
  	for (i = 0; i < nermarkers.length; i++)
  		nermarkers[i].setIcon('images/ner/nerverysmall.png');
  	for (i = 0; i < nebmarkers.length; i++)
  		nebmarkers[i].setIcon('images/neb/nebverysmall.png');
  	for (i = 0; i < negmarkers30.length; i++)
  		negmarkers30[i].setIcon('images/neg30/negverysmall30.png');
  	for (i = 0; i < nermarkers30.length; i++)
  		nermarkers30[i].setIcon('images/ner30/nerverysmall30.png');
  	for (i = 0; i < nebmarkers30.length; i++)
  		nebmarkers30[i].setIcon('images/neb30/nebverysmall30.png');
  	for (i = 0; i < segmarkers.length; i++)
  		segmarkers[i].setIcon('images/seg/segverysmall.png');
  	for (i = 0; i < sermarkers.length; i++)
  		sermarkers[i].setIcon('images/ser/serverysmall.png');
  	for (i = 0; i < sebmarkers.length; i++)
  		sebmarkers[i].setIcon('images/seb/sebverysmall.png');
  	
  	veryLowZoom=false;

}


});



</script>
</div>
</div>


</body>
</html>

