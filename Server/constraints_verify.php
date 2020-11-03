<?php

	//=======================================================================================
	//				  FITCHER AMB LA CLASSE CONSTRAINS_VERIFY
	//=======================================================================================


	/* La classe generada en aquest fitcher té
	 * la funció de verificar si un llistat de
	 * constraints (o podem especificar una en
	 * concret) existeixen en una taula  específica 
	*/
	class constraints_verify{

		public $conexion;
		public $constr;
		public $table;

				
		function __construct($connection, $constr, $table){

			$this->connection = $connection;
			$this->constr = $constr;
			$this->table = $table;


		}

		/* Aquesta funció tracta un llistat de constraints.
		 * Si es detecta que alguna d'elles no existeix a la
		 * taula, retorna False. Contrariament retorna True.
		*/	
		function verify(){

			for($i = 0; $i < count($this->constr); $i++){

				//S'utilitza la funció que verifica individualment cada constraint.
				if(!$this->verify_single_constr($this->constr[$i]))

					return False;

				}

			return True;

		}

		
		//Aquesta segona funció s'encarrega d'analitzar únicament una constraint
		function verify_single_constr($single_constr){
			
			//Es fa un split per separar el nom de la columna que volem verificar del seu valor.
			$constr_split = explode('=', $single_constr);

			/* Es construeix la query que enviarem a la base de dades.
			 * Utilitzarem el nom de la taula que utilitzarem i també 
			 * el nom de la columna que volem verificar
			*/
			$query = "SHOW COLUMNS FROM ".$this->table." LIKE ".'"'.$constr_split[0].'"';


			//S'envia la query a la base de dades
			$results = mysqli_query($this->connection, $query);

			//Verifiquem si existeix la columna
			if(!mysqli_fetch_row($results))
				return False;

			else 
				return True;
		}


	}


	/*$db_host = "localhost";
	$db_nombre="pbe";
	$db_usuario="root";
	$db_contra="";

	$conexion=mysqli_connect($db_host,$db_usuario,$db_contra,$db_nombre);

	$url= $_SERVER["REQUEST_URI"];

	$url_split = explode("?", $url);

	if(count($url_split) > 1){

	$table = $url_split[1];
	echo($table."<br>");

	if(count($url_split) > 2){

		$constr = explode("&", $url_split[2]);
		echo(count($constr)."<br>");

		for($i = 0; $i < count($constr); $i++){

			echo ($constr[$i]."<br>");

		}
	}
}

	if(mysqli_connect_errno()){

		echo "Fallo al conectarse";

		exit();
	}

	mysqli_select_db($conexion, $db_nombre) or die ("No se ha podido encontrar la base de datos");

	mysqli_set_charset($conexion, "utf8");

	$constrain = new constraints_verify($conexion, $constr, $table);
	
	if($constrain->verify()){
		echo "bien";
	}

	else
		echo "Una mal";*/


?>
