<?php
include 'config.php';
echo '
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css">
<script src="//code.jquery.com/jquery.min.js"></script>
<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<title>Flight Map</title>

<script src="//maps.googleapis.com/maps/api/js?libraries=geometry"></script>
<script>
var map_types = {
	vfrc: {
		max_zoom: 12,
		default_zoom: 10
	},
	tac: {
		max_zoom: 12,
		default_zoom: 10
	},
	sec: {
		max_zoom: 12,
		default_zoom: 10
	},
	wac: {
		max_zoom: 10,
		default_zoom: 9
	},
	enrl: {
		max_zoom: 11,
		default_zoom: 10
	},
	enrh: {
		max_zoom: 10,
		default_zoom: 9
	},
	"default": {
		default_zoom: 10,
		clean_zoom: 7
	}
};

function getMapTypeOption(a) {
	var b = GoogleMap.getMapTypeId();
	if (map_types[b] && map_types[b][a]) {
		return map_types[b][a]
	}
	else {
		return map_types["default"][a]
	}
}

function addMapTypes() {
	for (var b in map_types) {
		if (b == "default") {
			continue
		}
		var a = {
			minZoom: 4,
			maxZoom: map_types[b].max_zoom,
			name: b,
			tileSize: new google.maps.Size(256, 256),
			getTileUrl: (function(c) {
				return function(f, e) {
					var d = f.x % (1 << e);
					if (d < 0) {
						d = d + (1 << e)
					}
					var g = (1 << e) - f.y - 1;
					return "http://wms.chartbundle.com/tms/1.0.0/" + c + "/" + e + "/" + d + "/" + g + ".png"
				}
			})(b)
		};
		track_map.mapTypes.set(b, new google.maps.ImageMapType(a))
	}
}

function changeMapType(gMap) {
	var b = document.getElementById("menu_pulldown");
	var a = b.options[b.selectedIndex].value;
	gMap.setMapTypeId(a);
	setStateCookie()
}

function changeMapType2(gMap) {
	var b = document.getElementById("menu_pulldown2");
	var a = b.options[b.selectedIndex].value;
	gMap.setMapTypeId(a);
	setStateCookie()
}


function showMapType(gMap) {
	var b = document.getElementById("menu_pulldown");
	for (var a = 0; a < b.options.length; a++) {
		if (b.options[a].value == gMap.getMapTypeId()) {
			b.options[a].selected = true;
			break
		}
	}
}

function showMapType2(gMap) {
	var b = document.getElementById("menu_pulldown2");
	for (var a = 0; a < b.options.length; a++) {
		if (b.options[a].value == gMap.getMapTypeId()) {
			b.options[a].selected = true;
			break
		}
	}
}

var track_map;

function initialize() {
	var l = "hybrid";

	var mapOptions = {
		zoom: 10,
		maxZoom: 12,
		disableDefaultUI: true
	};

	var mapOptions = {
		zoom: 6,
		disableDefaultUI: true
	};

	track_map = new google.maps.Map(document.getElementById("track-map"),
		mapOptions);

	addMapTypes();
	track_map.setMapTypeId(l);
	showMapType2(track_map);



	track_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(
		document.getElementById("track_map_toolbar"));
}


function setBounds(pointa, pointb, map_object) {
	var bounds = new google.maps.LatLngBounds();

	bounds.extend(pointa);
	bounds.extend(pointb);

	map_object.fitBounds(bounds);
	map_object.panToBounds(bounds);
}

function showTrack(track, pointa, pointb, map_object) {
	var iconsetngs = {
		path: google.maps.SymbolPath.FORWARD_OPEN_ARROW
	};

	var line = new google.maps.Polyline({
		path: track,
		strokeColor: "#00FF00",
		strokeOpacity: 0.9,
		strokeWeight: 2,
		geodesic: true,
		map: map_object,
		icons: [{
			icon: iconsetngs,
			repeat: "100px",
			offset: "100%"
		}]
	});

	var marker1 = new google.maps.Marker({
		position: pointa,
		map: map_object,
		icon: "half_origin.png",
		title: "Start"
	});
	var marker2 = new google.maps.Marker({
		position: pointb,
		map: map_object,
		icon: "half_destination.png",
		title: "End"
	});
	var bounds = new google.maps.LatLngBounds();
	line.getPath().forEach(function(latLng) {
		bounds.extend(latLng);
	});
	track_map.fitBounds(bounds);
}

google.maps.event.addDomListenerOnce(window, "load", initialize);
</script>
</head>
<body class="page-latest">
	<section class="container main-content">';

$reg       = '';
$sqlwhere = '';


$limit=1000;
if(isset($_GET['limit'])) {
	$limit = $_GET['limit'];
}

if (isset($_GET['reg'])) {
	if (preg_match("/^[0-9A-Z\-].*/", $_GET['reg'])) {
		$reg = $_GET['reg'];
		$sqlwhere .= 'WHERE REGISTRATION = "' . $reg . '"';
	} else {
		$sqlwhere .= 'WHERE ID = 0';
	}
} else {
	$sqlwhere .= 'WHERE ID = 0';
}

$stmt           = "SELECT * FROM " . $flightsdatabasetable . " " . $sqlwhere;

$conn = new mysqli($databasehost, $databaseusername, $databasepassword, $databasename);

if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query($stmt);

echo '<div class="info column"><h1>Flight Map</h1></div>
<div class="column"><div class="info">REGISTRATION = <span>' . $reg . '</span></div>';

$result_num = $result->num_rows;

if ($result_num > 0) {

	
	echo '<h1 class="TrackedRoute">Tracked Log</h1>
	<div class="mapcontainer">
	<div id="track-map" style="position: relative; background-color: rgb(229, 227, 223); overflow: hidden; -webkit-transform: translateZ(0px);"></div></div>';
	

		echo '<script>window.onload = $(function () { $("#track_map_toolbar").show();});</script>';
	echo '</div>';




  echo '  
    <div id="track_map_toolbar" style="display: none;">
      <form id="charts_form2" class="charts_form" method="post">
        <select id="menu_pulldown2" class="mapButton" onChange="changeMapType2(track_map)">
          <optgroup label="VFR">
			<option selected value="vfrc">Hybrid VFR</option>
            <option value="sec">Sectional</option>
            <option value="tac">Terminal</option>
            <option value="wac">WAC</option>
          </optgroup>
          <optgroup label="IFR">
            <option value="enrl">Low IFR</option>
            <option value="enrh">High IFR</option>
          </optgroup>
          <optgroup label="Google">
            <option value="roadmap">Roadmap</option>
            <option value="hybrid">Satellite</option>
            <option value="terrain">Terrain</option>
          </optgroup>
        </select>
      </form>
    </div>
   </section>

<script>
window.onload = function() { ';

	$count = 0;

	while($row = $result->fetch_assoc() and $count++ < $limit) {
	
		if ($row["Track"] != null) {
			$trackarray = json_decode($row["Track"]);
			}
		else {
			$trackarray = "";
			}

	if ($trackarray != null) {
		// Discard the heading - we don't need it
		$size = count($trackarray);
		for ($i = 2; $i < $size; $i += 3) {
			unset($trackarray[$i]);
		}
		$trackarray = array_merge($trackarray);
		echo "var Track = [";
		$i = 1; // Globalization: Latitudes will be odd, Longitudes even
		foreach ($trackarray as $coord) {
			if($i % 2){
				// Odd - Latitude
				echo "new google.maps.LatLng(" . $coord . ",";
			} else {
				// Even - Longitude
				echo $coord . "), ";
			}
			$i++; // Increment counter
		}
			
		$trackFirstLatitude  = $trackarray[0];
		$trackFirstLongitude = $trackarray[1];
		$trackLastArray      = array_slice($trackarray, -2, 2, false);
		$trackLastLatitude   = $trackLastArray[0];
		$trackLastLongitude  = $trackLastArray[1];
		
		echo "];\n";
		
		echo 'showTrack(Track, new google.maps.LatLng(' . $trackFirstLatitude . ', ' . $trackFirstLongitude . '), new google.maps.LatLng(' . $trackLastLatitude . ', ' . $trackLastLongitude . '), track_map);';

	}


}
echo ' } </script>';

} else {
	echo "0 results";
}
$conn->close();

echo '</body></html>';
?>
