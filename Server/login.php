<?php

	$dbHost = "localhost";
	$dbName="pbe";
	$dbUsr="root";
	$dbPassword="";
	require_once("connectDB.php");
	$db = new dbConnection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	$connection = $db->connect();
	$uid = $_GET["uid"];
	$query = "select nom from students where uid=".'"'. $uid.'"';
	$results = mysqli_query($connection, $query);
	if($fila=mysqli_fetch_row($results))
    	echo $fila[0];	
	else
		echo "ERROR: LOGIN";
	$db->disconnect();	
?>
