<?php
//************COMENTARIS***********
// OBSERVACIONS:
// - el uid s'ha de passar desde python al url http://localhost/pbe/get.php?timetables?uid=34737834&day=Fri&ho... 
// - limit ha d'anar despres de order by
// A SOLUCIONAR:
// - les funcions showinserver i printinserver es poden fer en una sola?
// - les funcions del final del fitxer es poden serpar a un nou fitxer
// - nombrar els fitxers amb noms coherents al seu contingut
// - revisar la nomenclatura de les variables
// - les funcions dayDetector, compDetector i compDayDetector, es podrien simplificar en una sola?
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
			$funct->showIt($connection, $constrStr, $iMax, $table,$constrUid);
			break;
		case "marks":
			$iMax = 3;
			$constrStr = $contsVeryfier->constrCreator($constr, $table);
			$constrStr = $constrStr. " order by subject";
			$funct->showIt($connection, $constrStr, $iMax, $table,$constrUid);
			break;		
	}

	//tanquem la conexio amb la db
	mysqli_close($connection);

	
	//posteriorment python llegeix els valors que apareixen al servidor web
?>
