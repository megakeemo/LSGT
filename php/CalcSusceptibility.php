<?php
/*
* Logistic Regression Formula
* Y = B0 + B1*X1 + B2*X2 + B3*X3 + B4*X4 + B5*X5
	
* The X's are the independent variables
* The B(beta) are the partial regression coefficients
	
* X1 represents the Aspect
* X2 represents the Elevation
* X3 represents the Landuse
* X4 represents the Lithology
* X5 represents the Slope
	
* Regression Coefficient Formula
* b = (X' * X)^-1 * X'Y
*/

include("ConnectDB.php");
require_once("Matrix.php"); //Library for matrix operations

error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', 300);

/**
* Retrieve the reclassed value for each factor
**/

//Gets the Reclassed Value from the Aspect table
$query1 = "SELECT DISTINCT Reclassed_Value FROM aspect_susc_indexes
          JOIN aspect ON
		  aspect_susc_indexes.Aspect_id = aspect.grid_code";
$result1 = mysql_query($query1) or die(mysql_error());	

if(mysql_num_rows($result1))
{
	while ($row = mysql_fetch_array($result1)) 
	{ 
		$Aspect_Value[] = $row["Reclassed_Value"];
	}
}

else{
	echo "Empty result set.";
}


//Gets the Reclassed Value from the Elevation table
$query2 = "SELECT DISTINCT Reclassed_Value FROM elevation_susc_indexes
          JOIN elevation ON
		  elevation_susc_indexes.Elevation_id = elevation.grid_code";
$result2 = mysql_query($query2) or die(mysql_error());	

if(mysql_num_rows($result2))
{
	while ($row = mysql_fetch_array($result2)) 
	{ 
		$Elevation_Value[] = $row["Reclassed_Value"];
	}
}

else{
	echo "Empty result set.";
}


//Gets the Reclassed Value from the Landuse table
$query3 = "SELECT DISTINCT Reclassed_Value FROM landuse_susc_indexes
          JOIN landuse ON
		  landuse_susc_indexes.Landuse_id = landuse.grid_code";
$result3 = mysql_query($query3) or die(mysql_error());	

if(mysql_num_rows($result3))
{
	while ($row = mysql_fetch_array($result3)) 
	{ 
		$Landuse_Value[] = $row["Reclassed_Value"]; 
	}
}

else{
	echo "Empty result set.";
}


//Gets the Reclassed Value from the Lithology table
$query4 = "SELECT DISTINCT Reclassed_Value FROM lithology_susc_indexes
          JOIN lithology ON
		  lithology_susc_indexes.Lithology_id = lithology.grid_code";
$result4 = mysql_query($query4) or die(mysql_error());	

if(mysql_num_rows($result4))
{
	while ($row = mysql_fetch_array($result4)) 
	{ 
		$Lithology_Value[] = $row["Reclassed_Value"]; 
	}
}

else{
	echo "Empty result set.";
}


//Gets the Reclassed Value from the Slope table
$query5 = "SELECT DISTINCT Reclassed_Value FROM slope_susc_indexes
          JOIN slope ON
		  slope_susc_indexes.Slope_id = slope.grid_code";
$result5 = mysql_query($query5) or die(mysql_error());	

if(mysql_num_rows($result5))
{
	while ($row = mysql_fetch_array($result5)) 
	{ 
		$Slope_Value[] = $row["Reclassed_Value"]; 
	}
}

else{
	echo "Empty result set.";
}


/**
* Inputs the reclassed values into a matrix
* Caculates the partial regression coefficients, b
* b = (X' * X)^-1 * X'Y
*/

//Depedent Matrix, X
$arr1 = array(
		  array(1, $Aspect_Value[0], $Aspect_Value[1],  $Aspect_Value[2], $Aspect_Value[3], $Aspect_Value[4]),
		  array(1, $Elevation_Value[0], $Elevation_Value[1],  $Elevation_Value[2], $Elevation_Value[3], $Elevation_Value[4]),
		  array(1, $Landuse_Value[0], $Landuse_Value[1],  $Landuse_Value[2], $Landuse_Value[3], 0),
		  array(1, $Lithology_Value[0], $Lithology_Value[1],  $Lithology_Value[2], $Lithology_Value[3], $Lithology_Value[4]),
		  array(1, $Slope_Value[0], $Slope_Value[1],  $Slope_Value[2], $Slope_Value[3], $Slope_Value[4]),
		);

//Depedent Matrix, Y is 1 based on the sample
$arr2 = array(
		  array(1),
		  array(1),
		  array(1),
		  array(1),
		  array(1),
		);

//Creates instances of the matrix class
$X = new Lib_Matrix($arr1);
$Y = new Lib_Matrix($arr2);

/*Matrix Operations*/
$XT = $X->Transpose(); 		    //Transpose of X
$XTX = $XT->Multiply($X);       //Product of X and XT
$inXTX = $XTX->Inverse();       //Inverse of XTX

$XTY = $XT->Multiply($Y);       //Product of X and XT
$beta = $inXTX->Multiply($XTY);	//Regression coefficients
$r = $beta->GetInnerArray();

//Get each individual beta value
for($i=0; $i < count($r); $i++)
{
	$b0 = $r[0][0];
	$b1 = $r[1][0];
	$b2 = $r[2][0];
	$b3 = $r[3][0];
	$b4 = $r[4][0];
	$b5 = $r[5][0];
}

/**
* Retrieve frequency ratio for each factor
**/

//Gets the user input coordinates	
$coordx = json_decode($_REQUEST['coordx']);
$coordy = json_decode($_REQUEST['coordy']);

if($coordx > -77.1 && $coordx < -76 && $coordy > -18.9 && $coordy < -18.1)
{
	echo "Out of bounds. LSGIST does not currently support calculations for areas outside of St. Mary, Jamaica";
}


$cx = (float) substr($coordx, 0, 6);
$cy = (float) substr($coordy, 0, 7);

//Takes into consideration area around the selected coordinate
$xlbounds = $cx - .005;  //x coordinate lower bound
$xubounds = $cx + .005; //x coordinate upper bound
$ylbounds = $cy - .005; //y coordinate lower bound
$yubounds = $cy + .005; //y coordinate upper bound

//Gets the Frequency Ratio from the Aspect table
$aspect_query = "SELECT Frequency_Ratio FROM aspect_susc_indexes
          JOIN aspect ON
		  aspect_susc_indexes.Aspect_id = aspect.grid_code
		  WHERE X_Coord BETWEEN '$xlbounds' AND '$xubounds'
		  AND Y_Coord BETWEEN '$ylbounds' AND '$yubounds'";
$aspect_result = mysql_query($aspect_query) or die(mysql_error());	

if(mysql_num_rows($aspect_result))
{
	while ($row = mysql_fetch_array($aspect_result)) 
	{ 
		$Aspect_FR = $row["Frequency_Ratio"];
	}
}

else{
	//echo "Empty result set.";
}


//Gets the Frequency Ratio from the Elevation table
$elevation_query = "SELECT Frequency_Ratio FROM elevation_susc_indexes
          JOIN elevation ON
		  elevation_susc_indexes.Elevation_id = elevation.grid_code
		  WHERE X_Coord BETWEEN '$xlbounds' AND '$xubounds'
		  AND Y_Coord BETWEEN '$ylbounds' AND '$yubounds'";
$elevation_result = mysql_query($elevation_query) or die(mysql_error());	

if(mysql_num_rows($elevation_result))
{
	while ($row = mysql_fetch_array($elevation_result)) 
	{ 
		$Elevation_FR = $row["Frequency_Ratio"]; 	
	}
}

else{
	//echo "Empty result set.";
}


//Gets the Frequency Ratio from the Landuse table
$landuse_query = "SELECT Frequency_Ratio FROM landuse_susc_indexes
          JOIN landuse ON
		  landuse_susc_indexes.Landuse_id = landuse.grid_code
		  WHERE X_Coord BETWEEN '$xlbounds' AND '$xubounds'
		  AND Y_Coord BETWEEN '$ylbounds' AND '$yubounds'";
$landuse_result = mysql_query($landuse_query) or die(mysql_error());

if(mysql_num_rows($landuse_result))
{
	while ($row = mysql_fetch_array($landuse_result)) 
	{ 
		$Landuse_FR = $row["Frequency_Ratio"]; 	
	}
}

else{
	//echo "Empty result set.";
}


//Gets the Frequency Ratio from the Lithology table
$lith_query = "SELECT Frequency_Ratio FROM lithology_susc_indexes
          JOIN lithology ON
		  lithology_susc_indexes.Lithology_id = lithology.grid_code
		  WHERE X_Coord BETWEEN '$xlbounds' AND '$xubounds'
		  AND Y_Coord BETWEEN '$ylbounds' AND '$yubounds'";
$lith_result = mysql_query($lith_query) or die(mysql_error());	

if(mysql_num_rows($lith_result))
{
	while ($row = mysql_fetch_array($lith_result)) 
	{ 
		$Lith_FR = $row["Frequency_Ratio"]; 	
	}
}

else{
	//echo "Empty result set.";
}


//Gets the Reclassed Value from the Slope table
$slope_query = "SELECT Frequency_Ratio FROM slope_susc_indexes
          JOIN slope ON
		  slope_susc_indexes.Slope_id = slope.grid_code
		  WHERE X_Coord BETWEEN '$xlbounds' AND '$xubounds'
		  AND Y_Coord BETWEEN '$ylbounds' AND '$yubounds'";
		  //WHERE X_Coord >= ('$coordx' - .05) AND X_Coord <= ('$coordx' + .05) 
		  //AND Y_Coord >= ('$coordy' - .05) AND Y_Coord <= ('$coordy' + .05)"
$slope_result = mysql_query($slope_query) or die(mysql_error());	

if(mysql_num_rows($slope_result))
{
	while ($row = mysql_fetch_array($slope_result)) 
	{ 
		$Slope_FR = $row["Frequency_Ratio"]; 	
	}
}

else{
	//echo "Empty result set.";
}	


/**
* Calculate susceptibilty index
* Y = B0 + B1*X1 + B2*X2 + B3*X3 + B4*X4 + B5*X5
* Use frequency ratio is place of the X values
* Multiply regression coeffcient(b0 ... bn) by the frequency ratio of each factor
**/

$Y = ($b0) + ($b1 * $Aspect_FR) + ($b2 * $Elevation_FR) + ($b3 * $Landuse_FR) + ($b4 * $Lith_FR) + ($b5 * $Slope_FR);


/**
* Calculate probability of landslide occuring
* p = 1 / (1 + e^-Y)
* Result will be between 1 and 0*
* The closer the result tends to one, then the greater the chance of landslide occuring
* The closer the result tends to zero, then the lesser the chance of landslide occuring
**/

$Yneg =  -$Y;
$ex = exp($Yneg);
$prob = (float) 1 / (1 + $ex);
$prob = round($prob,6);

//Sends data back to client
if($prob > 0 && $prob < .25)
{
	echo "The probability of a landslide ocurring in this area is " . $prob . ". "; 
	echo "This is very low, so you can select this area for planning and development.";
}
else if($prob > .25 && $prob < .5)
{
	echo "The probability of a landslide ocurring in this area is " . $prob . ". "; 
	echo "This is moderately low, so you can select this area for planning and development.";
}
else if($prob > .5 && $prob < .75)
{
	echo "The probability of a landslide ocurring in this area is " . $prob . ". "; 
	echo "This is moderately high, so it will not be advisable to select this area for major planning and development due to the probability of small scale landslides.";
}
else if($prob > .75 && $prob <= 1)
{
	echo "The probability of a landslide ocurring in this area is " . $prob . ". "; 
	echo "This is very high, so it will not be advisable to select this area for planning and development based on significant landslide activity.";
}
else
{
	"Landslide probability could not be calculated for this area.";
}

if(!isset($coordx) && !isset($coordy))
{
	"Landslide probability could not be calculated for this area.";
}

?>