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
$secret=isset($data->secretKey) ? $data->secretKey : "";

// set product property values
$visitor->user_ip = $data->user_ip;
$visitor->country = $data->country;
$visitor->visited = $data->visited;
$visitor->postedon = $data->postedon;

if(password_verify($secret, $secretKey)) {
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
                "visited" => $visitor->visited,
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