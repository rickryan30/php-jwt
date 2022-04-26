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
include_once '../objects/testimonials.php';
 
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
 
// instantiate $testi object
$testi = new Testimonials($db);
$testi->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
 
// check if $testi id is not empty
if(!empty($testi->id)){
    // $testi->$id = $testi->id;
    $result = $testi->getId();
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

    $result = $testi->getAll();
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