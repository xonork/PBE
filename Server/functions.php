<?php

	require_once("constVeryfier.php");
	//constants
	define('MON', 1);
	define('TUE', 2);
	define('WED', 3);
	define('THU', 4);
	define('FRI', 5);
	define('HOURCOMP', 6);
	define('HOUR', 7);
	define('DAY', 8);
	define('LIMIT', 9);

	class RegularFunctions{


		//funcio que retorna un vector amb els resultats de la qurey a la bd 
		static function showInServer($connectDB, $consultDB, $fields, $table, $done){
			$colsNames = self::namesOfColumns($connectDB, $consultDB, $fields, $table);
			$result = mysqli_query($connectDB, $consultDB);
			$num_rows = mysqli_num_rows($result); 
			$tableArray = [];
			$j = 0;
			while($row = mysqli_fetch_row($result)){
				if(!$done){
					$i = 0;
					$uid = $row[$i];
					$done = True;
				}
				else
					$i = 1;
				$arrayAux = [];
				while($i <= $fields){
					$arrayAux[$colsNames[$i]] = $row[$i];
					$i ++;
					$j ++;
				}	
				array_push($tableArray,$arrayAux);
			}
			return $tableArray;
		}

		function namesOfColumns($connectDB, $consultDB, $fields, $table){
			$colsNames = [];
			$columnsQuery = "select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME = '". $table. "'";
			$columns = mysqli_query($connectDB, $columnsQuery);
			foreach ($columns as $value) {
				foreach ($value as $v) {
					array_push($colsNames, $v);
				}
			}
			return $colsNames;
		}

		function showIt($connectDB, $consultDB, $fields, $table, $done){
			$out = self::showInServer($connectDB, $consultDB, $fields, $table, False);
			echo json_encode($out);
		}

		//funcio que retorna un vector de dies ordenats a partir de l'actual.
		static function dayParser($constrDay){
			if ($constrDay == 0)
				$actualDate = date("w");
			else
				$actualDate = $constrDay;
			$days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			$parsedDays = array(count($days));
			//ordena els dies a partir de $actualDate
			for($i = 0; $i < count($days); $i++){
				$parsedDays[$i] = $days[($actualDate+$i)%count($days)];
			}
			return $parsedDays;
		}

		function detector($constr){
			if($constr != NULL){
				foreach ($constr as $value) {
					$aux = explode("=", $value);
					if($aux[0] == "hour")
						return HOUR;
					else if($aux[0] == "day")
						return DAY;
					else if($aux[0] == "limit")
						return LIMIT;
					foreach ($aux as $value2) {
						$aux2 = explode("[", $value2);
						if((count($aux2) > 1) && ($aux2[0] == "day")){
							switch($aux[1]){
								case "Mon":
									return MON;
									break;
								case "Tue":
									return TUE;
									break;
								case "Wed":
									return WED;
									break;
								case "Thu":
									return THU;
									break;
								case "Fri":
									return FRI;
									break;
							}
						}
						if(count($aux2) > 1){
							if($aux2[1] != NULL)
					 			return HOURCOMP;
						}
					}
				}
			return 0;
			}
		}

		//fucnio que busca i retorna el valor de la constraint uid en un vector de constraints
	    function searchUid($constr){
	        $constrUid= "";
	        if($constr != NULL){
	            foreach ($constr as $value) {
	                $aux = explode("=", $value);
	                if($aux[0] == "uid")
	                     $constrUid = $aux[1];

	            }
	        }
	        return $constrUid;
	    }

		function orderAndPrintTimetable($connection, $i_max, $constr, $table, $contsVeryfier, $constrUid){
			$timetableArray = [];
			$jsonArray = [];
			$constrDay = self::detector($constr);
			$days = self::dayParser($constrDay);
			if((((self::detector($constr) != DAY) && (self::detector($constr) != HOUR)) || (self::detector($constr) <= HOURCOMP)) && (self::detector($constr) != LIMIT)) {
				for ($i=0; $i < count($days); $i++) {
					if($i != 0){
						$constr = NULL;
						$constr[0] = "uid=".$constrUid;
						$constr[1] = "day=".$days[$i];
        				$constrStr = $contsVeryfier->constrCreator($constr, $table);
						$aux = self::showInServer($connection, $constrStr, $i_max, $table, True);
						foreach ($aux as $value) {
							array_push($timetableArray,$value);
						}
					}
					else{
						if($constr != NULL){
							array_push($constr,"day=".$days[$i]);
						}
						else
						$constr[0] = "day=".$days[$i];
						$constrStr = $contsVeryfier->constrCreator($constr, $table);
						$aux = self::showInServer($connection, $constrStr, $i_max, $table, False);
						foreach ($aux as $value) {
							array_push($timetableArray,$value);
						}		
					}
					//echo $constrStr;	
				}
			}
			else{
				$constrStr = $contsVeryfier->constrCreator($constr, $table);
				$aux = self::showInServer($connection, $constrStr, $i_max, $table, False);
				foreach ($aux as $value) {
					array_push($timetableArray,$value);
				}	
			}
			$jsonArray = array("uid" => $constrUid, $table => $timetableArray);
			echo json_encode($jsonArray);
		}
	}

?>
