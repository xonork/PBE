<?php

require_once("constVeryfier.php");

	class RegularFunctions{


    //funcio que retorna un vector amb els resultats de la qurey a la bd 
		static function showInServer($connectDB, $consultDB, $fields){
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

		//funcio que retorna un vector de dies ordenats a partir de l'actual.
		static function dayParser($constrDay){
			if($constrDay == 10)
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

		//funcio que detecta si en un vector hi ha algun caracter comparador caracteristic de certes cosntraints
		static function compDetector($constr){
			if($constr != NULL){
				foreach ($constr as $value) {
					$aux = explode("=", $value);
					foreach ($aux as $value2) {
						$aux2 = explode("[", $value2);
						if(count($aux2) > 1)
							if($aux2[1] != NULL)
					 			return True;
					}
				}
			}
			return False;
		}

		//funcio que retorna el numero corresponent al dia que hi ha a la constraint day si existeix
		static function dayDetector($constr){
			$constrDay = 10; //valor que no correspon a cap dia, per tant no hi ha constraint day
			if($constr != NULL){
				foreach ($constr as $value) {
					$aux = explode("=", $value);
					if($aux[0] == "day")
						return True;
				}
			}
			return False;
		}

		//funcio que retorna el numero del dia a partir del que treballar si hi ha a les constraints
		static function compDayDetector($constr){
			$constrDay = 10;
			if($constr != NULL){
				foreach ($constr as $value) {
					$aux = explode("=", $value);
					foreach ($aux as $value2){
						$aux2 = explode("[", $value2);
						if((count($aux2) > 1) && ($aux2[0] == "day")){
							switch($aux[1]){
								case "Mon":
									$constrDay = 1;
									break;
								case "Tue":
									$constrDay = 2;
									break;
								case "Wed":
									$constrDay = 3;
									break;
								case "Thu":
									$constrDay = 4;
									break;
								case "Fri":
									$constrDay = 5;
									break;
							}	
						}
					}
				}
			}
			return $constrDay;
		}

		//funcio que mostra al server
		static function printInServer($connection, $constr_str, $i_max){
			$out = self::showInServer($connection, $constr_str, $i_max);
				foreach ($out as $value) {
					echo $value. "<br>";
				}
		}

		//funcio que realitza les querys per ordre de dia a partir de l'actual i aplica certes constraints
		function orderAndPrintTimetable($connection, $i_max, $constr, $table, $contsVeryfier){
			$constrDay = self::compDayDetector($constr);
			$days = self::dayParser($constrDay);
				if(($constr == NULL || self::compDetector($constr)) && (!(self::dayDetector($constr)) && self::compDetector($constr))){
					for ($i=0; $i < count($days); $i++) {
						if($i != 0){
							$constr = NULL;
							$constr[0] = "day=".$days[$i];
							$constr_str = $contsVeryfier->constrCreator($constr, $table);
							self::printInServer($connection, $constr_str, $i_max);
						}
						else{
							if($constr != NULL)
								array_push($constr,"day=".$days[$i]);
							else
								$constr[0] = "day=".$days[$i];
							$constr_str = $contsVeryfier->constrCreator($constr, $table);
							self::printInServer($connection, $constr_str, $i_max);
						}
					}
				}
				else{
					$constr_str = $contsVeryfier->constrCreator($constr, $table);
					self::printInServer($connection, $constr_str, $i_max);
				}
		}
	}



?>
