<?php
//************COMENTARIS***********
// OBSERVACIONS:
// - el uid s'ha de passar desde python al url http://localhost/pbe/get.php?timetables?uid=34737834&day=Fri&ho... 
// A SOLUCIONAR:
// - hi ha problemes quan les constraints son amb comparadors de dies/hores i limit.
//- s'ha de revisar el verificador de constraints, perque amb el comparador de dies no funciona.
//************COMENTARIS***********

	//dades de la db
	$dbHost = "localHost";
	$dbName = "pbe";
	$dbUsr = "root";
	$dbPassword = "";

	//connexio amb la db utilitzant el fitxer connectDB.php
	require_once("connectDB.php");
	require_once("functions.php");
	$db = new dbConnection($dbHost, $dbName, $dbUsr, $dbPassword, "utf8");
	$connection = $db->connect();
	$funct = new RegularFunctions;

	//agafem el url i separem les dades que ens interessen (taula i vector de constraints)
	//verifiquem que les dades son correctes amb funcions del fitxer constVeryfier.php
	//require_once("constVeryfier.php");
	$url= $_SERVER["REQUEST_URI"];
	$aux = explode('?', $url);
	$lenQuery = count($aux);
	$constr = NULL;
	if($lenQuery > 1){
		$table = $aux[1];
		$contsVeryfier = new ConstraintsVerify($connection, $aux[2], $table);
		if($lenQuery > 2 && $aux[2] != NULL) 
			$constr = $contsVeryfier->verify();
	}

	else{
		echo "Error en la introducció de la query";
		exit();
	}
	$constrUid = $funct->searchUid($constr);

	//segons la taula que volguem mirar creem una query diferent a la bd i mostrem el seu resultat al servidor
	switch($table){
		case "timetables":
			$iMax = 4;
			$funct->orderAndPrintTimetable($connection, $iMax, $constr, $table, $contsVeryfier, $constrUid);				
			break;

		case "tasks":
			$iMax = 3;
			$constrStr = $contsVeryfier->constrCreator($constr, $table);
			$constrStr = $constrStr. " order by date";
			//$funct->printInServer($connection, $constrStr, $iMax);
			$funct->showIt($connection, $constrStr, $iMax, $table, False);
			break;
		case "marks":
			$iMax = 3;
			$constrStr = $contsVeryfier->constrCreator($constr, $table);
			$constrStr = $constrStr. " order by subject";
			$funct->showIt($connection, $constrStr, $iMax, $table, False);
			break;		
	}

	//tanquem la conexio amb la db
	mysqli_close($connection);

	
	//posteriorment python llegeix els valors que apareixen al servidor web
?>
