<?php
header("Access-Control-Allow-Origin: https://php-jwt.netlify.app/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/user.php';
 
// generate json web token
include_once '../config/core.php';
include_once '../vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once '../vendor/firebase/php-jwt/src/ExpiredException.php';
include_once '../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once '../vendor/firebase/php-jwt/src/JWT.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->email = $data->email;
$email_exists = $user->emailExists();

// get jwt
$secretKey=isset($data->key) ? $data->key : "";
 
// check if email exists and if password is correct

    if($email_exists && password_verify($data->password, $user->password) && $secretKey == $key){
    
        $token = array(
        "key" => $key,
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "name" => $user->name,
            "company" => $user->company,
            "phone" => $user->phone,
            "email" => $user->email,
            "created_on" => $user->created_on
        )
        );
    
        // set response code
        http_response_code(200);
    
        // generate jwt
        $jwt = JWT::encode($token, $key);
        echo json_encode(
                array(
                    "data" => $token['data'],
                    'status' => "success",
                    "message" => "Successful login.",
                    "access_token" => $jwt
                )
            );
    
    }
    
    // login failed
    else{
    
        // set response code
        http_response_code(401);
    
        // tell the user login failed
        echo json_encode(array("status" => "invalid","message" => "Login failed."));
    }
?>