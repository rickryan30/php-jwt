<?php
header("Access-Control-Allow-Origin: https://php-jwt.netlify.app/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/visitors.php';
 
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

// instantiate product object
$visitor = new Visitors($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$secretKey=isset($data->key) ? $data->key : "";

// set product property values
$visitor->user_ip = $data->user_ip;
$visitor->country = $data->country;
$visitor->postedon = $data->postedon;

if($secretKey == $key) {
    // create the visitor
    if($visitor->create()){

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
                "user_ip" => $visitor->user_ip,
                "country" => $visitor->country,
                "postedon" => $visitor->postedon
            )
         );
         $jwt = JWT::encode($token, $key);
    
        // set response code
        http_response_code(200);
    
        // display message: visitor was created
        echo json_encode(
            array(
                "data" => $token['data'],
                'status' => "success",
                "message" => "Visitor Inserted",
                "access_token" => $jwt
            )
        );
    }
    
    // message if unable to create visitor
    else{
    
        // set response code
        http_response_code(400);
    
        // display message: unable to create visitor
        echo json_encode(array("status" => "invalid","message" => "Unable to insert visitor."));
    }
} else {
    // set response code
    http_response_code(400);
    
    // display message: unable to create visitor
    echo json_encode(array("status" => "invalid","message" => "Missing Token."));
}
?>