<?php
//starting session - used for passing arrays
session_start();

//including db file
include_once "db.php";
$searchAddress=null;

//Getting search address from search bar with post method
$searchAddress = $_POST['street'];

//Getting Latitude, Longitude, Address, Distance and near-by addresses from page.php 
$addLat = $_GET['addLat'];
$addLong = $_GET['addLong'];
$stAddress = $_GET['stadd'];
$nearbyAdd = $_SESSION['nearby'];
$distance=$_GET['dist'];

//If no address is entered and searched, displays full map with all parking spaces
if($searchAddress==null&&$addLat==null&&$addLong==null)
{
	$stmt = $db->query('select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters`');
}

//If some address is entered in first search bar and nothing is received from page.php
if($searchAddress!=null&&$addLat==null&&$addLong==null)
{
	//This query selectes lat,long,.... from DB where address column of table contains the address searched
	$stmt = $db->prepare("select latitude,longitude,address,parkingspaces,occupiedspaces,dayratehour,daytimelimit,direction from `backup_chicago_meters` where address like :searchTerm");
	$stmt->bindValue(':searchTerm', '%'.$searchAddress.'%', PDO::PARAM_STR);
	$stmt->execute();
}

//If some address is entered to find near by parking spaces, it goes to page.php and gets the lat,log of the specified add
if($addLat!=null && $addLong!=null)
{
	//This query returns lat,long.... from DB where address colum of table equals to any of the address in near-by address array
	$keyCount = count($nearbyAdd);
	$keys = implode(', ', array_fill(0, $keyCount, '?'));
	$query ="select latitude , longitude , address , parkingspaces , occupiedspaces , dayratehour , daytimelimit , direction from `backup_chicago_meters` where address in ({$keys})";
	$stmt = $db->prepare($query);
	$stmt->execute($nearbyAdd);


}

if($stmt)
{
	$row_count = $stmt->rowCount();
	if($row_count!=0)
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
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    			$latis[] = $row['latitude'];
			$longis[] = $row['longitude'];
			$address[]=$row['address'];
			$pspaces[]=$row['parkingspaces'];
			$ospaces[]=$row['occupiedspaces'];
			$rate[]=$row['dayratehour'];
			$timeLimit[]=$row['daytimelimit'];
			$dir[]=$row['direction'];	
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
//Loads addresses from DB into $result variable - auto complete purpose
$result = $db->query("SELECT address FROM `backup_chicago_meters`");

while($row = $result->fetch(PDO::FETCH_ASSOC))
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
    		<select name="distance">
			<option value="0.3">0.3</option>
			<option value="0.5">0.5</option>
			<option value="1">1.0</option>
			<option value="2">2.0</option>
		</select>miles
    		<input type=submit value="Search by Address" />
    		</form>
  	</div>

  	<div>
  		<?php
  			if($searchAddress!=null)
  			{
  				echo "Parking spaces for ". $searchAddress;
  			}
  			if($stAddress!=null)
  			{
  				echo "Parking spaces near ".$stadd." with in ".$distance." miles";
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

//Getting PHP variables for search address, lat n longi, street address
var scriptSearch = '<?php echo $searchAddress; ?>';
var scriptaddLat = '<?php echo $addLat; ?>';
var scriptaddLong = '<?php echo $addLong; ?>';
var scriptStAdd = '<?php echo $stAddress; ?>';
var k = 0;
if(scriptSearch=='')
k=1411; //to display loop as center of the map- otherwise 0 is fine
if(scriptaddLat!='') //If searched for parking spaces nearby an address - data received from page,php is not null 
{
	var myLatlng = new google.maps.LatLng(scriptaddLat,scriptaddLong);//map is loaded with new lat lng
	var myOptions = {
		zoom: 16,center: 
		myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	    }
    var map = new google.maps.Map(document.getElementById('map'), myOptions);
    var addmarker = new google.maps.Marker({
        position: myLatlng, 
        map: map,
        title:scriptStAdd
    });//marker is added with searched street address as title of the marker

   
   
}
else //If searched using first search bar - spaces in a street or a particular address listed in DB
{
	var map = new google.maps.Map(document.getElementById('map'), {
      		zoom: 15,
      		center: new google.maps.LatLng(scriptLati[k], scriptLongi[k]),
      		mapTypeId: google.maps.MapTypeId.ROADMAP
    	});
    	}

//Declaring infowindow for pop up message for markers
var infowindow = new google.maps.InfoWindow();

//Declaring different zoom levels
var highZoom = false;
var lowZoom=false;
var veryHighZoom = false;
var veryLowZoom=false;

//declaring the arrays for markers for storing different types of markers

//red line markers
var hrmarkers = new Array();
var vrmarkers = new Array();
var nermarkers= new Array();
var sermarkers= new Array();

//green line markers
var hgmarkers = new Array();
var vgmarkers = new Array();
var negmarkers= new Array();
var segmarkers= new Array();

//blue line markers
var hbmarkers = new Array();
var vbmarkers = new Array();
var nebmarkers= new Array();
var sebmarkers= new Array();

//inclined line markers
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
	var maxThreshold = scriptPspaces[i]/2;
    	var minThreshold = scriptPspaces[i]/3;
    	//If the occupied spaces exceeds max threshold, that is > half, the line markers are added to red group
    	if(scriptOspaces[i]>maxThreshold)
    	{
    		//If direction of parking space is East/ West, marker is added to horizontal lines array
    		if(scriptDir[i]=='E'||scriptDir[i]=='W'||scriptDir[i]=='EAST'||scriptDir[i]=='WEST'||scriptDir[i]=='East'||scriptDir[i]=='West')
    		{
    			marker = new google.maps.Marker({
        			position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:hrimage
      			});
      			hrmarkers.push(marker);
      		}
      		//If direction of parking space is NorthEast , marker is added to NE lines array
      		else if(scriptDir[i]=='NE')
      		{
      			marker = new google.maps.Marker({
      				position: new google.maps.LatLng(scriptLati[i], scriptLongi[i]),
        			map: map,
        			icon:nerimage
      			});
      			nermarkers.push(marker);

      		}
      		//If direction of parking space is SE , marker is added to NE lines array
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
      		//If direction of parking space is S or N , marker is added to vertical lines array
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

      	//If the occupied spaces exceeds min threshold but less than max threshold, the line markers are added to blue group
      	else if(scriptOspaces[i]>minThreshold&&scriptOspaces[i]<maxThreshold)
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

      	//If the occupied spaces is less than min threshold, that is < 1/3, the line markers are added to green group
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
      	//Adding info window to the markers - setting content with address ,parking spaces, rate and time
      	google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(scriptAddress[i]+"<br> Total space: "+scriptPspaces[i]
          +"<br> Occupied Space: "+scriptOspaces[i]+"<br> Rate: "+scriptRate[i]+" Time Limit: "+scriptTime[i]);
          infowindow.open(map, marker);}
          })(marker, i));

}

//When zoom level is changed, this function is called
google.maps.event.addListener(map, 'zoom_changed', function() 
{
//Getting the zoom level
var zoomLevel = map.getZoom();

//Setting the appropriate zoomlevel as true which are initialized to false at the begining of script
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
	//for all markers in the different marker arrays, icons are changed to medium size
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
	//for all markers in the different marker arrays, icons are changed to large size
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
	//for all markers in the different marker arrays, icons are changed to small size
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
	//for all markers in the different marker arrays, icons are changed to very small size
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

