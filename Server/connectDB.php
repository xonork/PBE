<?php
	class dbConnection{
		//atributs de la classe
		public $dbHost;
		public $dbName;
		public $dbUser;
		public $dbPassword;
		public $charCode;
		public $connection;
		//constructor on es crea l'objecte pertinent a la connexio
		function __construct($dbHost, $dbName, $dbUser, $dbPassword, $charCode){
			$this->dbHost = $dbHost;
			$this->dbName = $dbName;
			$this->dbUser = $dbUser;
			$this->dbPassword = $dbPassword;
			$this->charCode = $charCode;
		}
		//funció encarregada d'inciar la conexió amb la base de dades i retorna la connexio.
		function connect(){
			//Iniciem la conexió
			$this->connection=mysqli_connect($this->dbHost,$this->dbUser,$this->dbPassword,$this->dbName);
				//si hi ha error en la connexio s'avisa al usuari i surt de l'execucio
			if(mysqli_connect_errno()){
				echo "Error al connectar el servidor amb la base de dades.";
				exit();
			}
			//verifica si existeix una db amb el nom especificat com a atribut i si no es el cas avisa al usuari
			mysqli_select_db($this->connection, $this->dbName) or die ("No s'ha pogut trobar la base de dades.");
			//Modifiquem la codificació de carácters que utilitzarem
			mysqli_set_charset($this->connection, $this->charCode);
			return $this->connection;	
		}
		//funcio per tancar la connexio amb la db
		function disconnect(){
			mysqli_close($this->connection);
		}
	}
?>
