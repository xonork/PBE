<?php
	class db_connection{
		//atributs de la classe
		public $db_host;
		public $db_name;
		public $db_user;
		public $db_password;
		public $char_code;
		public $connection;
		//constructor on es crea l'objecte pertinent a la connexio
		function __construct($db_host, $db_name, $db_user, $db_password, $char_code){
			$this->db_host = $db_host;
			$this->db_name = $db_name;
			$this->db_user = $db_user;
			$this->db_password = $db_password;
			$this->char_code = $char_code;
		}
		//funció encarregada d'inciar la conexió amb la base de dades i retorna la connexio.
		function connect(){
			//Iniciem la conexió
			$this->connection=mysqli_connect($this->db_host,$this->db_user,$this->db_password,$this->db_name);
				//si hi ha error en la connexio s'avisa al usuari i surt de l'execucio
			if(mysqli_connect_errno()){
				echo "Error al connectar el servidor amb la base de dades.";
				exit();
			}
			//verifica si existeix una db amb el nom especificat com a atribut i si no es el cas avisa al usuari
			mysqli_select_db($this->connection, $this->db_name) or die ("No s'ha pogut trobar la base de dades.");
			//Modifiquem la codificació de carácters que utilitzarem
			mysqli_set_charset($this->connection, $this->char_code);
			return $this->connection;	
		}
		//funcio per tancar la connexio amb la db
		function disconnect(){
			mysqli_close($this->connection);
		}
	}
?>