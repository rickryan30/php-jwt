<?php
 // Allow from any origin
 if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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
$like->user_ip = (isset($_GET['user_ip']) && $_GET['user_ip']) ? $_GET['user_ip'] : '0';
 
// check if likes user_ip is not empty
if(!empty($like->user_ip)){
    // $like->$id = $like->id;
    $result = $like->getByIP();
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
                    "message" => "Visitor already Click Like",
                    "access_token" => $jwt
                )
            );

    } else {
		// set response code - 404 Not found
	    	http_response_code(404);
        echo json_encode(array("message" => "Visitor Didnt Click Like"));
    }
    
 } else{

    $result = $like->getAll();
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
                    "message" => "list of Visitors who likes.",
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