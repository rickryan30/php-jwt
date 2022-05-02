<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "user";
 
    // object properties
    public $id;
    public $name;
    public $company;
    public $phone;
    public $email;
    public $password;
    public $created_on;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
     
    // create new user record
    function create(){

        if(empty($this->name) || 
        empty($this->company) ||
        empty($this->phone) ||
        empty($this->email) ||
        empty($this->password) ||
        empty($this->created_on)) {
            return false;
        }
     
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    company = :company,
                    phone = :phone,
                    email = :email,
                    password = :password,
                    created_on = :created_on";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->company=htmlspecialchars(strip_tags($this->company));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->created_on=htmlspecialchars(strip_tags($this->created_on));
     
        // bind the values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':created_on', $this->created_on);
     
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
     
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }
         
    // check if given email exist in the database
    function emailExists(){
     
        // query to check if email exists
        $query = "SELECT id, name, company, phone, password, created_on
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
     
        // prepare the query
        $stmt = $this->conn->prepare( $query );
     
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
     
        // bind given email value
        $stmt->bindParam(1, $this->email);
     
        // execute the query
        $stmt->execute();
     
        // get number of rows
        $num = $stmt->rowCount();
     
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
     
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
            // assign values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->company = $row['company'];
            $this->phone = $row['phone'];
            $this->password = $row['password'];
            $this->created_on = $row['created_on'];
     
            // return true because email exists in the database
            return true;
        }
     
        // return false if email does not exist in the database
        return false;
    }
     
    // update a user record
    public function update(){
     
        // if password needs to be updated
        $password_set=!empty($this->password) ? ", password = :password" : "";
     
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    company = :company,
                    phone = :phone,
                    email = :email
                    {$password_set}
                WHERE id = :id";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->company=htmlspecialchars(strip_tags($this->company));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->email=htmlspecialchars(strip_tags($this->email));
     
        // bind the values from the form
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
     
        // hash the password before saving to database
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
     
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
     
        // execute the query
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
    public function getById(){
     
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