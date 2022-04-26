<?php
header("Access-Control-Allow-Origin: https://php-jwt.netlify.app/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
 
// instantiate visitor object
$visitor = new Visitors($db);
$visitor->user_ip = (isset($_GET['user_ip']) && $_GET['user_ip']) ? $_GET['user_ip'] : '0';
 
// check if email exists and if password is correct
if(!empty($visitor->user_ip)){
    // $visitor>$id = $visitor>id;
    $result = $visitor->getByIP();
    if(!empty($result)){
        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => $result
         );

         // set response code
        http_response_code(200);
    
        // generate jwt
        $jwt = JWT::encode($token, $key);
        echo json_encode(
                array(
                    "data" => $result,
                    'status' => "success",
                    "message" => "Visitor Found.",
                    "access_token" => $jwt
                )
            );

    } else {
		// set response code - 404 Not found
	    	http_response_code(404);
        echo json_encode(array("message" => "Visitor not Found."));
    }
    
 } else{

    $result = $visitor->getAll();
    $count = count($result);

    if ($count > 0) {

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "iat" => $iat,
            "nbf" => $nbf,
            "data" => $result
         );

         // set response code
        http_response_code(200);
    
        // generate jwt
        $jwt = JWT::encode($token, $key);
        echo json_encode(
                array(
                    "data" => $result,
                    "count" => $count,
                    'status' => "success",
                    "message" => "list of Visitor.",
                    "access_token" => $jwt
                )
            );
    } else {

        // set response code - 404 Not found
	    	http_response_code(404);
            echo json_encode(array("message" => "No Data."));
    }
 
    
}
?>