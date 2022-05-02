<?php
// 'Likes' object
class Reply{
 
    // database connection and table name
    private $conn;
    private $table_name = "visitors_testimonial_reply";
 
    // object properties
    public $id;
    public $tid;
    public $name;
    public $reply;
    public $country;
    public $postedon;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
     
    // insert like record
    function create(){

        if(empty($this->tid) ||
        empty($this->name) ||
        empty($this->reply) ||
        empty($this->country) ||
        empty($this->postedon)) {
            return false;
        }
     
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    tid = :tid,
                    name = :name,
                    reply = :reply,
                    country = :country,
                    postedon = :postedon";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->tid=htmlspecialchars(strip_tags($this->tid));
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->reply=htmlspecialchars(strip_tags($this->reply));
        $this->country=htmlspecialchars(strip_tags($this->country));
        $this->postedon=htmlspecialchars(strip_tags($this->postedon));
     
        // bind the values
        $stmt->bindParam(':tid', $this->tid);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':reply', $this->reply);
        $stmt->bindParam(':country', $this->country);
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
    public function getTid(){
     
        // query to check if email exists
        $query = "SELECT * FROM $this->table_name WHERE tid = :tid";

        // prepare the query
        $stmt = $this->conn->prepare( $query );
        // execute the query
        $stmt->execute(['tid' => $this->tid]);
     
        // get number of rows
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
       
        return $result;
    }
}