<?php

	$dbHost = "localhost";
	$dbName="pbe";
	$dbUsr="root";
	$dbPassword="";
	require_once("connectDB.php");
	$db = new dbConnection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	
	$connection = $db->connect();
	
	$user = $_GET["user"];
	$password = $_GET["password"];


	$query = "SELECT NOM FROM STUDENTS WHERE USER=".'"'. $user.'"'."AND PASSWORD=".'"'.$password.'"';
	echo $query;
	$results = mysqli_query($connection, $query);


	if($fila=mysqli_fetch_row($results)){

    echo json_encode(array("name"=>$fila[0]));	
	
	}

	else{
		echo json_encode((array("name"=>"ERROR")));
	}

	

	$db->disconnect();

	
?>
