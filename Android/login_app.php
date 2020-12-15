<?php

	$dbHost = "localhost";
	$dbName="pbe";
	$dbUsr="root";
	$dbPassword="";
	require_once("connectDB.php");
	$db = new dbConnection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	
	$connection = $db->connect();
	
	$user = $_POST["user"];
	$password = $_POST["password"];
	

	$query = "SELECT * FROM STUDENTS WHERE USER=".'"'. $user.'"'."AND PASSWORD=".'"'.$password.'"';
	$results = mysqli_query($connection, $query);
	$response = array();
	$response["success"] = false;
	if(($fila=mysqli_fetch_row($results)) && $user!=''){
		
		$response["success"]=true; 
		$response["uid"] = $fila[0];
		$response["name"] = $fila[1];	
	
	}

	echo json_encode($response);

	

	$db->disconnect();

	
?>