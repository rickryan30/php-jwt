<?php
header("Access-Control-Allow-Origin: https://php-jwt.netlify.app/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/testimonial-replies.php';
 
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
 
// instantiate $reply object
$reply = new Reply($db);
$reply->tid = (isset($_GET['tid']) && $_GET['tid']) ? $_GET['tid'] : '0';
 
// check if $reply id is not empty
if(!empty($reply->tid)){
    // $reply->$id = $reply->id;
    $result = $reply->getTid();
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
                    "message" => "Data Found",
                    "access_token" => $jwt
                )
            );

    } else {
		// set response code - 404 Not found
	    	http_response_code(404);
        echo json_encode(array("message" => "Data not Found"));
    }
    
 } else{

    $result = $reply->getAll();
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
                    "message" => "List of Data",
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