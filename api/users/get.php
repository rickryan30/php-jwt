<?php
header("Access-Control-Allow-Origin: https://php-jwt.netlify.app/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
$user->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
 
// check if email exists and if password is correct
if(!empty($user->id)){
    // $user->$id = $user->id;
    $result = $user->getById();
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
                    "message" => "User Found.",
                    "access_token" => $jwt
                )
            );

    } else {
		// set response code - 404 Not found
	    	http_response_code(404);
        echo json_encode(array("message" => "User not Found."));
    }
    
 } else{

    $result = $user->getAll();
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
                    'status' => "success",
                    "message" => "list of user.",
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