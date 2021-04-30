<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<title>Landslide Susceptibilty GIS Tool</title>
<link rel="shortcut icon" href="images/favicon.png" />
<link rel="stylesheet" type="text/css" href="css/styles.css" media="screen" />
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxsPIx2ZHu0VbwKqhQeZnpgVBROs7Xt-U&sensor=true&libraries=drawing"></script>
<script type="text/javascript" src="js/jquery1.9.1.js"></script>
<script type="text/javascript" src="js/map.js"></script>
<script type="text/javascript" src="js/json2.js"></script>
<script type="text/javascript" src="js/stylize.js"></script>
</head>
<body>
	<div class="hide"><img src="images/hide.png" title="Hide UI" /></div>
	<div id="map-canvas"></div>
    <div id="address-box">
    	<div id="coord-box">
            <table width="0" border="0" cellspacing="2" cellpadding="1">
          <tr>
            <td><label for="latlng1">LatLng:</label></td>
            <td><input id="latlng1" type="text" value="" disabled="disabled" /></td>
          </tr>
          <tr>
            <td><label for="address1">Address:</label></td>
            <td><input id="address1" type="text" value="" disabled="disabled"/></td>
          </tr>
          <tr>
            <td><label for="latlng2">LatLng2:</label></td>
            <td><input id="latlng2" type="text" value="" disabled="disabled"/></td>
          </tr>
          <tr>
            <td><label for="address2">Address 2:</label></td>
            <td><input id="address2" type="text" value="" disabled="disabled"/></td>
          </tr>
        </table>
    </div>
    </div>
    <div id="control-col">
    	<ul id="controls">
        	<li><button type="button" name="calculate-si" value="calc-si" title="Click on the map to select an area" onclick="drawPolygon();">Calculate SI</button></li>
            <li><button type="button" name="measure-dist" value="measure-dist" title="Drag the markers to measure distance" onclick='measureDistance();'>Measure Distance</button></li>
            <li><button type="button" name="clear" class="clear" value="clear" title="Clears all data and overlays on screen" onclick='deleteOverlays();'>Clear Screen</button></li>
            <li>Aspect <input type="checkbox" id="layer0" onclick="toggleLayer(0)" unchecked/></li>
            <li>Elevation <input type="checkbox" id="layer1" onclick="toggleLayer(1)" unchecked/></li>
            <li>Landuse <input type="checkbox" id="layer2" onclick="toggleLayer(2)" checked/></li>
            <li>Lithology <input type="checkbox" id="layer3" onclick="toggleLayer(3)" unchecked/></li>
            <li>Slope <input type="checkbox" id="layer3" onclick="toggleLayer(4)" unchecked/></li>
            <li>Landslide Inventory <input type="checkbox" id="layer3" onclick="toggleLayer(5)" checked/></li>
        </ul>
    </div>
    <div id="coords-panel"><textarea id="coords-area" rows=6 cols=27 disabled="disabled"></textarea></div>
    <div id="results-panel"><textarea id="results-area" rows=8 cols=23 disabled="disabled"></textarea></div>
</body>
</html>