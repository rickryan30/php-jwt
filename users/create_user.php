<?php
// required headers
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
$secret=isset($data->secretKey) ? $data->secretKey : "";

// set product property values
$user->name = $data->name;
$user->company = $data->company;
$user->phone = $data->phone;
$user->email = $data->email;
$user->password = $data->password;
$user->created_on = date("Y-m-d H:i:s");

if(base64_decode($secret) == $key) {
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
                'status' => "success",
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
} else {
    // set response code
    http_response_code(400);
    
    // display message: unable to create user
    echo json_encode(array("status" => "invalid","message" => "Missing Token."));
}
?>