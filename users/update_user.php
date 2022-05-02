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
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        // set user property values
		$user->name = $data->name;
		$user->company = $data->company;
		$user->phone = $data->phone;
		$user->email = $data->email;
		$user->password = $data->password; 
		$user->id = $decoded->data->id;
		 
		// create the product
		if($user->update()){
		    // we need to re-generate jwt because user details might be different
			$token = array(
			   "iss" => $iss,
			   "aud" => $aud,
			   "iat" => $iat,
			   "nbf" => $nbf,
			   "data" => array(
				"id" => $user->id,
				"name" => $user->name,
				"company" => $user->company,
				"phone" => $user->phone,
				"email" => $user->email,
				"email" => $user->email,
			   )
			);
			$jwt = JWT::encode($token, $key);
			 
			// set response code
			http_response_code(200);
			 
			// response in json format
			echo json_encode(
			        array(
						'status' => "success",
			            "message" => "User was updated.",
			            "access_token" => $jwt
			        )
			    );
		}
		 
		// message if unable to update user
		else{
		    // set response code
		    http_response_code(401);
		 
		    // show error message
		    echo json_encode(array("message" => "Unable to update user."));
		}
    }
 
    // if decode fails, it means jwt is invalid
	catch (Exception $e){
	 
	    // set response code
	    http_response_code(401);
	 
	    // show error message
	    echo json_encode(array(
	        "message" => "Access denied.",
	        "error" => $e->getMessage()
	    ));
	}
}

// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
?>