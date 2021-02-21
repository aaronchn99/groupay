<?php

class Database {

  	private $database;

  	function __construct() {
  		$this->database = $this->getConnection();
      		$this->exec("PRAGMA foreign_keys = ON;");
  	}

  	function __destruct() {
  		$this->database->close();
  	}

  	function exec($query) {
  		$this->database->exec($query);
  	}

  	function query($query) {
  		$result = $this->database->query($query);
  		return $result;
  	}

  	function querySingle($query) {
  		$result = $this->database->querySingle($query,true);
  		return $result;
  	}

  	function prepare($query) {
  		return $this->database->prepare($query);
  	}

    	function stmtQuerySingle($stmt){
      		$results = $stmt->execute();
      		return $results->fetchArray();
   	}

  	function escapeString($string) {
  		return $this->database->escapeString($string);
  	}

  	private function getConnection() {
      		$cwd = dirname(__FILE__);
  		$conn = new SQLite3($cwd.'/db/database.db');
  		return $conn;
  	}
  }
?>
