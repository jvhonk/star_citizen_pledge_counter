<?php

require_once 'initialize.php';

/**
 * This is a class that handles all database connections and query's.
 * @author John
 *
 */

class database {
	

	
	/**
	 * query a database , returns a object.
	 * @param string $sql   provide the SQL Query string
	 */
	
	public static function insertDb($sql) {
		
		$connect= new config();
		$ip = $connect->ip;
		$user = $connect->user;
		$pass = $connect->pass;
		$dbname = $connect->dbname;
		
		$mysqli = new mysqli($ip, $user, $pass, $dbname);
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			die;
		}
		if (!$mysqli->query($sql)) {
		echo "Inserting the Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		die;
		}
	    $mysqli->close();
	    
	}
	
/**
 * Insert a database object
 * @param string $sql    provide the SQL Query string
 */
	
	public static function queryDb($sql) {
	
		$connect= new config();
		$ip = $connect->ip;
		$user = $connect->user;
		$pass = $connect->pass;
		$dbname = $connect->dbname;
		
		$mysqli = new mysqli($ip, $user, $pass, $dbname);
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			die;
		}
		
		$result = $mysqli->query($sql);
		
		if (!$result) {
			echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
			die;
		}
		$mysqli->close();
		return $result;
		 
	}
	
	
	
	
}