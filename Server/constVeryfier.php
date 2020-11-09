<?php
//************COMENTARIS***********
// - He canviat el atribut $constr, ara es un string que despres es separa en vector de constraints en 
//   la funcio verify(), per estalviar linies al main, a mes retorna directament el vector amb 
//   les constraints.
//  - tenim el problema dels AND i lilit als if de constrCreator()
//************COMENTARIS***********

	class constraints_verify{
		//atributs de la classe
		public $conexion;
		public $constr;
		public $table;
		//constructor de l'objecte que s'ha de verificar
		function __construct($connection, $constr, $table){
			$this->connection = $connection;
			$this->constr = $constr;
			$this->table = $table;
		}
		//verifica si totes les constraints de la llista existeixen en la taula
		function verify(){
			$aux = explode('&', $this->constr);
			for($i = 0; $i < count($aux); $i++){
				//instancia a la funcio de la mateixa clase
				if(!$this->verify_single_constr($aux[$i])){
					echo "Error en la verificacio de les constraints";
					exit();
				}
			}
			return $aux;
		}	
		//verifica una sola constraint
		function verify_single_constr($single_constr){
			//vector incialitzat amb els noms de les containts (sense valor) de l'argument
			$aux = explode('=', $single_constr);
			$aux2 = explode('[', $aux[0]);
			if($aux2[0] != "limit"){
				//sentencia sql on es busca a les columnes de la taula (atribut) si hi ha alguna on es pugui referir la constraint
				$query = "SHOW COLUMNS FROM ".$this->table." LIKE ".'"'.$aux2[0].'"';
				//resultat obtingut d'enviar la sentencia a la db
				$results = mysqli_query($this->connection, $query);
				//Verifiquem si existeix la columna
				if(!mysqli_fetch_row($results))
					return False;
			} 
			return True;
		}

		//creador de constraints preparades per sentencia sql
		function constrCreator($constr, $table){
			if($constr != NULL){
				$constr_str = "";
				$limit_constr_str = $this->limitDetector($constr);
				$limit = $limit_constr_str[0];
				$constrSinLimit = $limit_constr_str[1];
				$len = count($constrSinLimit);
				for ($i=0; $i < $len; $i++) {
					//afegir cometes simples als valors de les constraints
					$aux = explode('=', $constrSinLimit[$i]);
						$aux2 = explode('[', $aux[0]);
						if(count($aux2) > 1) $extra = $aux2[1];
						else $extra = "";
						//afegim comparadors de les constraints si cal
						switch($extra){
							case "gt]":
								$aux2[0] = $aux2[0]. " >'";
								break;
							case "lt]":
								$aux2[0] = $aux2[0]. " <'";
								break;
							default:
								$aux2[0] = $aux2[0]."='";
								break;
						}
						$aux[1] = $aux[1]."'";
						$constr_str = $constr_str. $aux2[0]. $aux[1];
						//afegim operadors logics entre les constrains
						if($i < $len-1)
							$constr_str = $constr_str. " AND ";
						else
							$constr_str = $constr_str;
				}
				//afegim el limit si hi ha
				if($limit != NULL)
					$limit_str = "limit ". $limit;
				else 
					$limit_str = "";
				//sentencia sql final
				$constr_str = "select * from ". $table. " where ". $constr_str. $limit_str;
			}
			else
				$constr_str = "select * from ". $table;

			return $constr_str;
		}

		//buscar si hi ha limit entre les constraints i si hi ha l'extreu i es guarda el valor
		function limitDetector($constr){
			$limit_constr_str[0]= "";
			$len = count($constr);
			$j = 0;
			for ($i=0; $i < $len; $i++) { 
				$aux = explode('=', $constr[$i]);
				if($aux[0] == "limit"){
					$limit_constr_str[0] = $aux[1];
				}
				else{
					$constrSinLimit[$j] = $constr[$i];
					$j++;
				}
			}
			$limit_constr_str[1] = $constrSinLimit;
			return $limit_constr_str;
		}
	}
?>