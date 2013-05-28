<?php

/**
 * all site wide configurations are set here
 * @author John
 *
 */

class config{

	
	public $ip = "localhost" ; //server
	public$user = "yourusernamehere";      //Username
	public$pass = "yourpasswordhere";      //Password
	public$dbname   = "database name here";      //Database name
	
	
	public function __construct() {
		
		$this->ip;
		$this->user;
		$this->pass;
		$this->dbname;

		
	}

}

