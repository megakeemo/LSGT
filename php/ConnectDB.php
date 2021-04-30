<?php
//Connect to the database
$con = mysql_connect("localhost", "root", "");

if(!$con)
{
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("landslide_susceptibility", $con) or die("Cannot select DB");
?>