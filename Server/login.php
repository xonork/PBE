<?php

	$dbHost = "localhost";
	$dbName="pbe";
	$dbUsr="root";
	$dbPassword="";
	require_once("connectDB.php");
	$db = new dbConnection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	
	$connection = $db->connect();
	
	$uid = $_GET["uid"];
	
	$query = "SELECT NOM FROM STUDENTS WHERE UID=".'"'. $uid.'"';
	
	$results = mysqli_query($connection, $query);


	if($fila=mysqli_fetch_row($results)){

    echo $fila[0];	
	
	}

	else{
		echo "ERROR";
	}

	

	$db->disconnect();

	
?>
