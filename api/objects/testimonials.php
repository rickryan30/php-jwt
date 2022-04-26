<?php
// 'Testimonials' object
class Testimonials{
 
    // database connection and table name
    private $conn;
    private $table_name = "visitors_testimonials";
 
    // object properties
    public $id;
    public $name;
    public $testimonials;
    public $country;
    public $postedon;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
     
    // insert like record
    function create(){

        if(empty($this->name) || 
        empty($this->testimonials) ||
        empty($this->country) ||
        empty($this->postedon)) {
            return false;
        }
     
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    testimonials = :testimonials,
                    country = :country,
                    postedon = :postedon";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->testimonials=htmlspecialchars(strip_tags($this->testimonials));
        $this->country=htmlspecialchars(strip_tags($this->country));
        $this->postedon=htmlspecialchars(strip_tags($this->postedon));
     
        // bind the values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':testimonials', $this->testimonials);
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
        $query = "SELECT * FROM $this->table_name ORDER BY `id` DESC";
     
        // prepare the query
        $stmt = $this->conn->prepare( $query );
     
        // execute the query
        $stmt->execute();
     
        // get number of rows
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
       
        return $result;
    }

    // retrieve user by id in the database
    public function getId(){
     
        // query to check if email exists
        $query = "SELECT * FROM $this->table_name WHERE id = :id";

        // prepare the query
        $stmt = $this->conn->prepare( $query );
        // execute the query
        $stmt->execute(['id' => $this->id]);
     
        // get number of rows
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
       
        return $result;
    }
}