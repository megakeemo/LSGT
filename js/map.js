//Global Variables
var map;
var geocoder;
var layers = [];
var markersArray = [];
var drawingManager;
var coordinates = [];
var poly;
var aspect = 'https://dl.dropboxusercontent.com/u/64127605/Aspect.kmz';
var elevation = 'https://dl.dropboxusercontent.com/u/64127605/Elevation.kmz';
var landuse = 'https://dl.dropboxusercontent.com/u/64127605/Landuse.kmz';
var lithology = 'https://dl.dropboxusercontent.com/u/64127605/Lithology.kmz';
var slope = 'https://dl.dropboxusercontent.com/u/64127605/Slope.kmz';
var inventory = 'https://dl.dropboxusercontent.com/u/64127605/Landslide_Inventory.kmz';

//Intializes the map
function initialize() 
{
	var mapOptions = {
	  center: new google.maps.LatLng(18.29134, -76.87675),
	  zoom: 13,
	  disableDefaultUI: true,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	}
    geocoder = new google.maps.Geocoder();
	map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
	

	//Adds KML layers
	layers[0] = new google.maps.KmlLayer(aspect,
		{
			preserveViewport: true,
			suppressInfoWindows: true,
		});
	layers[1] = new google.maps.KmlLayer(elevation,
		{
			preserveViewport: true,
			suppressInfoWindows: false,
		});
	layers[2] = new google.maps.KmlLayer(landuse,
		{
			preserveViewport: true,
			suppressInfoWindows: false,
		});
	layers[3] = new google.maps.KmlLayer(lithology,
		{
			preserveViewport: true,
			suppressInfoWindows: false,
		});
	layers[4] = new google.maps.KmlLayer(slope,
		{
			preserveViewport: true,
			suppressInfoWindows: true,
		});
	layers[5] = new google.maps.KmlLayer(inventory,
		{
			preserveViewport: true,
			suppressInfoWindows: false,
		});
	
	//Sets some KML layers to not display initially	
	layers[0].setMap(null);
	layers[1].setMap(null);
	layers[2].setMap(map);
	layers[3].setMap(null);
	layers[4].setMap(null);
	layers[5].setMap(map);
}

//Turn KML layers on and off
function toggleLayer(i) 
{
  if(layers[i].getMap() === null) 
  {
    layers[i].setMap(map);
  }
  else 
  {
    layers[i].setMap(null);
  }
}

//Measures the distance between 2 points on the map
//Gets the coordinates of those points and displays the address
function measureDistance() 
{
	//Ruler 1
	var ruler1 = new google.maps.Marker({
		position: map.getCenter() ,
		map: map,
		draggable: true
	});
	markersArray.push(ruler1); //Adds marker to the array
	
	google.maps.event.addListener(ruler1, 'dragend', function(evt)
	{
		var latlng2 = document.getElementById('latlng2').value = evt.latLng.lat().toFixed(5) + ',' + evt.latLng.lng().toFixed(5);
		
		//Gets the address from the coordinates
		var latlngStr = latlng2.split(',', 2);
		var lat = parseFloat(latlngStr[0]);
		var lng = parseFloat(latlngStr[1]);
		var newlatlng = new google.maps.LatLng(lat, lng);
		geocoder.geocode( {'latLng': newlatlng},
			function(results, status) {
			if(status == google.maps.GeocoderStatus.OK) {
			  if(results[0]) {
				document.getElementById("address2").value = results[0].formatted_address;
			  }
			  else {
				document.getElementById("address2").value = "No results";
			  }
			}
			else {
			  document.getElementById("address2").value = status;
			}
		  });
	});
	// centers the map on markers coords
	map.setCenter(ruler1.position);

	//Ruler 2
	var ruler2 = new google.maps.Marker({
		position: map.getCenter(),
		map: map,
		draggable: true
	});
	markersArray.push(ruler2); //Adds marker to the array
	
	google.maps.event.addListener(ruler2, 'dragend', function(evt)
	{
		var latlng1 = document.getElementById('latlng1').value = evt.latLng.lat().toFixed(5) + ',' + evt.latLng.lng().toFixed(5);
		
		//Gets the address from the coordinates
		var latlngStr = latlng1.split(',', 2);
		var lat = parseFloat(latlngStr[0]);
		var lng = parseFloat(latlngStr[1]);
		var newlatlng = new google.maps.LatLng(lat, lng);
		geocoder.geocode( {'latLng': newlatlng},
			function(results, status) {
			if(status == google.maps.GeocoderStatus.OK) {
			  if(results[0]) {
				document.getElementById("address1").value = results[0].formatted_address;
			  }
			  else {
				document.getElementById("address1").value = "No results";
			  }
			}
			else {
			  document.getElementById("address1").value = status;
			}
		  });
		});
	// centers the map on markers coords
	map.setCenter(ruler2.position);
	
	//Ruler Labels
	var ruler1label = new Label({ map: map });
	var ruler2label = new Label({ map: map });
	ruler1label.bindTo('position', ruler1, 'position');
	ruler2label.bindTo('position', ruler2, 'position');

	//Distance Ruler
	var rulerpoly = new google.maps.Polyline({
		path: [ruler1.position, ruler2.position] ,
		strokeColor: "#FFFF00",
		strokeOpacity: .7,
		strokeWeight: 7
	});
	markersArray.push(rulerpoly); //Adds marker to the array
	rulerpoly.setMap(map);

	//Distance Labels
	ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
	markersArray.push(ruler1label); //Adds label to the array
	ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
	markersArray.push(ruler2label); //Adds label to the array

	//Updates distance as it is dragged around
	google.maps.event.addListener(ruler1, 'drag', function() {
		rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
		ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
		ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
	});

	//Updates distance as it is dragged around
	google.maps.event.addListener(ruler2, 'drag', function() {
		rulerpoly.setPath([ruler1.getPosition(), ruler2.getPosition()]);
		ruler1label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
		ruler2label.set('text',distance( ruler1.getPosition().lat(), ruler1.getPosition().lng(), ruler2.getPosition().lat(), ruler2.getPosition().lng()));
	});
	
}

//Calculates the distance between the 2 points on the map
function distance(lat1,lon1,lat2,lon2) 
{
	var R = 6371; // km //distance of Earth's radius
	var dLat = (lat2-lat1) * Math.PI / 180;
	var dLon = (lon2-lon1) * Math.PI / 180; 
	var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
		Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) * 
		Math.sin(dLon/2) * Math.sin(dLon/2); 
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
	var d = R * c;
	if (d>1) return Math.round(d)+"km";
	else if (d<=1) return Math.round(d*1000)+"m";
	return d;
}

// Defines the label
function Label(opt_options) 
{
	// Initialization
	this.setValues(opt_options);

	// Label specific
	var span = this.span_ = document.createElement('span');
	span.style.cssText = 'position: relative; left: 0%; top: -8px; ' +
			  'white-space: nowrap; border: 0px; font-family:arial; font-weight:bold;' +
			  'padding: 2px; background-color: #ddd; '+
				'opacity: .75; '+
				'filter: alpha(opacity=75); '+
				'-ms-filter: "alpha(opacity=75)"; '+
				'-khtml-opacity: .75; '+
				'-moz-opacity: .75;';

	var div = this.div_ = document.createElement('div');
	div.appendChild(span);
	div.style.cssText = 'position: absolute; display: none';
};
Label.prototype = new google.maps.OverlayView;

//Implement onAdd
Label.prototype.onAdd = function() {
	var pane = this.getPanes().overlayLayer;
	pane.appendChild(this.div_);

	// Ensures the label is redrawn if the text or position is changed.
	var me = this;
	this.listeners_ = [
		google.maps.event.addListener(this, 'position_changed',
		function() { me.draw(); }),
		google.maps.event.addListener(this, 'text_changed',
		function() { me.draw(); })
	];	
};

//Implement onRemove
Label.prototype.onRemove = function() { this.div_.parentNode.removeChild(this.div_ );
	// Label is removed from the map, stop updating its position/text.
	for (var i = 0, I = this.listeners_.length; i < I; ++i) {
		google.maps.event.removeListener(this.listeners_[i]);
	}
};

//Implement draw
Label.prototype.draw = function() {
	var projection = this.getProjection();
	var position = projection.fromLatLngToDivPixel(this.get('position'));

	var div = this.div_;
	div.style.left = position.x + 'px';
	div.style.top = position.y + 'px';
	div.style.display = 'block';

	this.span_.innerHTML = this.get('text').toString();
};


//Draws a polygon on the map to select an area  for landslide susceptibility calucation
function drawPolygon()
{
	drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.POLYGON,
		drawingControl: false,
		polygonOptions: {
			fillColor: 'red',
			fillOpacity: 5,
			strokeWeight: 2,
			strokeColor: 'red',
			editable: false,
			zIndex: 1
    	}
	});
	
	//Gets coordinate data and formats it
	google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
		allcoordinates = (polygon.getPath().getArray()); //all coordinates
		$('#coords-area').val("Coordinates: " + allcoordinates);
		
		//formats the coordinates
		coordinates = (polygon.getPath().getArray()[0]); //first click point
		coordinates = coordinates.toString();
		coordinates = coordinates.substring(1, 38);
		coordinates = coordinates.split(",");

		//Encode coordinates in JSON
	    coordx = JSON.stringify(coordinates[1].substring(1,10));
		coordy = JSON.stringify(coordinates[0].substring(0,8));
		
		//Sends coordinate data to server and prints the output
		$.ajax({        
		   type: "POST",
		   url: "php/CalcSusceptibility.php",
		   data: 'coordx=' + coordx + '&coordy=' + coordy,
		   async: false,
		   dataType: 'html',
		   success: function(data) {
			    $('#results-panel').show();
           		$('#results-panel').fadeIn(500);    
				$('#results-area').html(data);
       		}
		}); 
	});
	
	//Complete drawing
	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(evt) {
		if (evt.type == google.maps.drawing.OverlayType.POLYGON) 
		{
			drawingManager.setDrawingMode(null);
			drawingManager.setOptions({
			drawingControl: false
			
		});
		
            var newPoly = evt.overlay;
            newPoly.type = evt.type;
            setSelection(newPoly);
	  	}
	});
	//Limits to only one polygon
	deleteOverlays();
	drawingManager.setMap(map);
}


// Sets the map on all markers in the array.
function setAllMap(map) 
{
	for (var i = 0; i < markersArray.length; i++) 
  	{
	  markersArray[i].setMap(map);
  	}
}

// Removes the overlays from the map, but keeps them in the array.
function clearOverlays() 
{
	setAllMap(null);
  	drawingManager.setMap(null);
}

// Deletes all markers in the array by removing references to them.
function deleteOverlays() 
{
	clearOverlays();
	deletePoly();
  	markersArray = [];	
}

//Sets the polygon selection
function setSelection(shape) 
{
	clearSelection();
	poly = shape;
}

//Clears the polygon selection
function clearSelection() 
{
	if (poly) 
	{
	  poly.setEditable(false);
	  poly = null;
	}
}

//Deletes polygon from map
function deletePoly() 
{
	if (poly) 
	{
	  poly.setMap(null);
	}
}

//Loads map on window load
google.maps.event.addDomListener(window, 'load', initialize);