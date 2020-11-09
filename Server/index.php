<?php
//************COMENTARIS***********
// - S'ha d'aclarar com funciona l'ordre de timetables.
// - S'ha d'implementar el tema de les constraints per URL que hi ha a l'enunciat?
// - El numero d'iteracions de cada taula (camps) esta una mica chapuza pero no he trobat altra opcio:
//		- count(*) pero retorna un objecte de tipus mysqli_result q no puc castejar a int per operar.
//		- si ho faig am un i++ per iteracio surt un warning al server que molesta.
// - el uid s'ha de passar desde python al url http://localhost/pbe/get.php?timetables?uid=34737834&day=Fri&ho...
//************COMENTARIS***********

	//dades de la db
	$dbHost = "localHost";
	$dbName = "pbe";
	$dbUsr = "root";
	$dbPassword = "";

	//connexio amb la db utilitzant el fitxer connectDB.php
	require_once("connectDB.php");
	$db = new db_connection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	$connection = $db->connect();
	$constr = NULL;
	//agafem el url i separem les dades que ens interessen
	$url= $_SERVER["REQUEST_URI"];
	$aux = explode('?', $url);
	$len_query = count($aux);
	if($len_query > 1){
		if($len_query > 1) $table = $aux[1];
		require_once("constVeryfier.php");
		$contsVeryfier = new constraints_verify($connection, $aux[2], $table);
		if($len_query > 2 && $aux[2] != NULL) 
			$constr = $contsVeryfier->verify();
	}
	else{
		echo "Error en la introducció de la query";
		exit();
	}
	

	//per a cada query diferent es fa una busqueda a la db amb la sentencia sql pertinent que a mes ordena els registres de la manera que s'especifica a l'enunciat
	switch($table){
		case "timetables":
			$i_max = 4;
			$constr_str = $contsVeryfier->constrCreator($constr, $table);
			$out = showInServer($connection, $constr_str, $i_max);
			$parsed_out = dayParser($out);
			foreach ($parsed_out as $value) {
				echo $value;
				echo "<br>";

			}
			break;
		case "tasks":
			$i_max = 3;
			$constr_str = $contsVeryfier->constrCreator($constr, $table);
			$out = showInServer($connection, $constr_str, $i_max);
			foreach ($out as $value) {
				echo $value;
				echo "<br>";
			}
			break;
		case "marks":
			$i_max = 3;
			$constr_str = $contsVeryfier->constrCreator($constr, $table);
			$constr_str = $constr_str. " order by subject";
			$out = showInServer($connection, $constr_str, $i_max);
			foreach ($out as $value) {
				echo $value;
				echo "<br>";
			}
			break;		
	}

	//funcio que es crida quan es vol mostrar al servidor el resultat final de la busqueda 
	function showInServer($connectDB, $consultDB, $fields){
		$result = mysqli_query($connectDB, $consultDB);
		$num_rows = mysqli_num_rows($result); 
		$out = array_fill(0,$num_rows,"");
		$j = 0;
		while($row = mysqli_fetch_row($result)){
		$i = 1;
			while($i <= $fields){
				$out[$j] = $out[$j].$row[$i]. ",";
				$i ++;
			}
		$j++;
		}
		return $out;
	}
	//tanquem la conexio amb la db
	mysqli_close($connection);
	//posteriorment python llegeix els valors que apareixen al servidor web

	function dayParser($out){
		$actualDate = date("w");;
		
		$days = ["Sun", "Mon", "Tue", "Wen", "Thu", "Fri", "Sat"];
		$parsedDays = array(count($days));

		for($i = 0; $i < count($days); $i++){
			
			$parsedDays[$i] = $days[($actualDate+$i)%count($days)];
		
		}

		$j = 0;
		$offset = 0;
		$found = False;

		while(($j < count($out) and !$found)){

			$aux = explode(',', $out[$j]);
			if($aux[0] == $parsedDays[0]){
				$offset = $j;
				$found = True;
			}

			$j++;


		}

		$parsed_out = array(count($out));
		for($i = 0; $i < count($out); $i++){
			$parsed_out[$i] = $out[($offset + $i)%count($out)];
			
		}	

		return $parsed_out;



	}
?>