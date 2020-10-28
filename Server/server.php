$db_host = "localhost";
	$db_nombre="pbe";
	$db_usuario="root";
	$db_contra="";

	$conexion=mysqli_connect($db_host,$db_usuario,$db_contra,$db_nombre);

	if(mysqli_connect_errno()){

		echo "Fallo al conectarse";

		exit();
	}

	mysqli_select_db($conexion, $db_nombre) or die ("No se ha podido encontrar la base de datos");

	mysqli_set_charset($conexion, "utf8");

	$uid = $_GET["uid"];
	//$query = $_GET["query"];

	$consulta = "SELECT * FROM DATOS WHERE UID = " . $uid;

	$resultados = mysqli_query($conexion, $consulta);

	while($fila=mysqli_fetch_row($resultados)){

		echo $fila[0] . " ";
		echo $fila[1] . " ";
		echo $fila[2] . " ";

		echo "<br>";

	}

	mysqli_close($conexion);

	
?>
