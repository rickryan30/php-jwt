<?php
// 'visitors' object
class Visitors{
 
    // database connection and table name
    private $conn;
    private $table_name = "visitors";
 
    // object properties
    public $id;
    public $user_ip;
    public $country;
    public $visited;
    public $postedon;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
     
    // create new user record
    function create(){

        if(empty($this->user_ip) || 
        empty($this->country) ||
        empty($this->visited) ||
        empty($this->postedon)) {
            return false;
        }
     
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_ip = :user_ip,
                    country = :country,
                    visited = :visited,
                    postedon = :postedon";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->user_ip=htmlspecialchars(strip_tags($this->user_ip));
        $this->country=htmlspecialchars(strip_tags($this->country));
        $this->visited=htmlspecialchars(strip_tags($this->visited));
        $this->postedon=htmlspecialchars(strip_tags($this->postedon));
     
        // bind the values
        $stmt->bindParam(':user_ip', $this->user_ip);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':visited', $this->visited);
        $stmt->bindParam(':postedon', $this->postedon);
     
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }
         
    
    // retrieve all data in the database
    public function getAll(){
     
        // query to check if email exists
        $query = "SELECT * FROM $this->table_name";
     
        // prepare the query
        $stmt = $this->conn->prepare( $query );
     
        // execute the query
        $stmt->execute();
     
        // get number of rows
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
       
        return $result;
    }

    // retrieve user by id in the database
    public function getByIP(){
     
        // query to check if email exists
        $query = "SELECT * FROM $this->table_name WHERE user_ip = :user_ip";

        // prepare the query
        $stmt = $this->conn->prepare( $query );
        // execute the query
        $stmt->execute(['user_ip' => $this->user_ip]);
     
        // get number of rows
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
       
        return $result;
    }

    // update a user record
    public function update(){
     
        // // if no posted password, do not update the password
        // $query = "UPDATE " . $this->table_name . "
        //         SET
        //             user_ip = :user_ip,
        //             country = :country,
        //             visited = :visited,
        //             postedon = :postedon
        //         WHERE id = :id";
     
        // // prepare the query
        // $stmt = $this->conn->prepare($query);
     
        // // sanitize
        // $this->user_ip=htmlspecialchars(strip_tags($this->user_ip));
        // $this->country=htmlspecialchars(strip_tags($this->country));
        // $this->visited=htmlspecialchars(strip_tags($this->visited));
        // $this->postedon=htmlspecialchars(strip_tags($this->postedon));
     
        // // bind the values from the form
        // $stmt->bindParam(':user_ip', $this->user_ip);
        // $stmt->bindParam(':country', $this->country);
        // $stmt->bindParam(':visited', $this->visited);
        // $stmt->bindParam(':postedon', $this->postedon);
     
        // // unique ID of record to be edited
        // $stmt->bindParam(':id', $this->id);
        
        $query = "UPDATE $this->table_name SET visited = :visited, postedon = :postedon 
        WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $data = array(
            'id' => $this->id,
            'visited' => $this->visited,
            'postedon' => $this->postedon
        );

        // execute the query
        if($stmt->execute($data)){
            return true;
        }
      
        return false;
    }
}