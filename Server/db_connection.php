<?php
	
	//=======================================================================================
	//							FITCHER AMB LA CLASSE DB_CONNECTION
	//=======================================================================================


	/* La classe db_connection s'encarregarà de dur
	 * a terme totes les instruccions necessaries tant com
	 * per iniciar la conexió amb MySQL com per
	 * finalitzar-la.
	*/
	class db_connection{

		public $db_host;
		public $db_name;
		public $db_user;
		public $db_password;
		public $char_code;
		public $connection;


		function __construct($db_host, $db_name, $db_user, $db_password, $char_code){

			$this->db_host = $db_host;
			$this->db_name = $db_name;
			$this->db_user = $db_user;
			$this->db_password = $db_password;
			$this->char_code = $char_code;

		}

		//Funció encarregada d'inciar la conexió amb la base de dades.
		function connect(){

			//Iniciem la conexió
			$this->connection=mysqli_connect($this->db_host,$this->db_user,$this->db_password,$this->db_name);

				/* Si no s'ha pogut establir conexió amb MySQL 
				 *  es notifica, i a més es finalitza l'execució
				 * del script.
				*/
				if(mysqli_connect_errno()){

					echo "Fallo al conectarse";

					exit();
				}

				/* Verifica si existeix la base de dades
				 * que volem utilitzar. Si no existeix es
				 * finalitza l'execució del script
				*/
				mysqli_select_db($this->connection, $this->db_name) or die ("No se ha podido encontrar la base de datos");

				//Modifiquem la codificació de carácters que utilitzarem
				mysqli_set_charset($this->connection, $this->char_code);

				

		}


		function disconnect(){

			//Tanquem la conexió amb MySQL
			mysqli_close($this->connection);

		}

		function get_connection(){

			return $this->connection;
		}




	}


	/*$db_host = "localhost";
	$db_name="pbe";
	$db_user="root";
	$db_password="";
	$char_code = "utf8";

	$db = new db_connection($db_host, $db_name, $db_user, $db_password, $char_code);

	$db->connect();

	$db_connection = $db->get_connection();

	$consulta = "SELECT * FROM STUDENTS";

	$resultados = mysqli_query($db_connection, $consulta);
	$fila=mysqli_fetch_row($resultados);

	echo $fila[0] . " ";
	echo $fila[1] . " ";


	$db->disconnect();*/
?>