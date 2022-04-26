<?php
 // Allow from any origin
 if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/likes.php';
 
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

// instantiate like object
$like = new Likes($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$secretKey=isset($data->key) ? $data->key : "";

// set product property values
$like->user_ip = $data->user_ip;
$like->country = $data->country;
$like->postedon = $data->postedon;

if($secretKey == $key) {
    // insert the like
    if($like->create()){

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => array(
             "user_ip" => $like->user_ip,
             "country" => $like->country,
             "postedon" => $like->postedon
            )
         );
         $jwt = JWT::encode($token, $key);
    
        // set response code
        http_response_code(200);
    
        // display message: like was inserted
        echo json_encode(
            array(
                "data" => $token['data'],
                'status' => "success",
                "message" => "Visitor Clicked Inserted.",
                "access_token" => $jwt
            )
        );
    }
    
    // message if unable to insert like
    else{
    
        // set response code
        http_response_code(400);
    
        // display message: unable to insert like
        echo json_encode(array("status" => "invalid","message" => "Unable to insert like."));
    }
} else {
    // set response code
    http_response_code(400);
    
    // display message: unable to insert like
    echo json_encode(array("status" => "invalid","message" => "Missing Token."));
}
?>