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

// instantiate product object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$secretKey=isset($data->key) ? $data->key : "";

// set product property values
$user->name = $data->name;
$user->company = $data->company;
$user->phone = $data->phone;
$user->email = $data->email;
$user->password = $data->password;
$user->created_on = date("Y-m-d H:i:s");

// if($secretKey == $key) {
    // create the user
    if($user->create()){

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
             "name" => $user->name,
             "company" => $user->company,
             "phone" => $user->phone,
             "email" => $user->email,
             "password" => $user->password,
             "created_on" => $user->created_on
            )
         );
         $jwt = JWT::encode($token, $key);
    
        // set response code
        http_response_code(200);
    
        // display message: user was created
        echo json_encode(
            array(
                "data" => $token['data'],
                'status' => "success",
                "message" => "User was created.",
                "access_token" => $jwt
            )
        );
    }
    
    // message if unable to create user
    else{
    
        // set response code
        http_response_code(400);
    
        // display message: unable to create user
        echo json_encode(array("status" => "invalid","message" => "Unable to create user."));
    }
// } else {
//     // set response code
//     http_response_code(400);
    
//     // display message: unable to create user
//     echo json_encode(array("status" => "invalid","message" => "Missing Token."));
// }
?>