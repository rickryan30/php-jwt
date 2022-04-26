<?php
// used to get mysql database connection
class Database{

	// specify your own database credentials
	private $host = "bqlbxtjmwdnzivs07gog-mysql.services.clever-cloud.com";
	private $db_name = "bqlbxtjmwdnzivs07gog";
	private $username = "u37qhbkdrkwmbnuw";
	private $password = "ox1xSzHXsQoCPczKnLdh";
	public $con;

	// private $host = "databases.000webhost.com";
	// private $db_name = "id10330233_rickdb";
	// private $username = "id10330233_portfoliodb";
	// private $password = ")0h<e{)jFEExPA7l";
	// public $con;

	// get the database connection
	public function getConnection(){

		$this->conn = null;

		try{
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			die('Failed to connect to DB: '.$e->getMessage());
		}

		return $this->conn;
	}
}