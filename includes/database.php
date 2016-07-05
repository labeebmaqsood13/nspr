<?php

class Database{

	public $connection;

	function __construct($database){
		$this->open_db_connection($database);
	}

	public function open_db_connection($database){

		$this->connection = new mysqli('localhost','root','',$database);
			if($this->connection->connect_errno > 0){
			   	 die('Unable to connect to database [' . $this->connection->connect_error . ']');
			}

	}

	public function insert_query($sql){

		$result = $this->connection->query($sql);
			
			if($this->connection->error){
				echo 'Unable to execute the query ['.$this->connection->error.']<br>';
			}

		return $result;	
	}

}

// $db = new Database('nspr_nmap');


?>