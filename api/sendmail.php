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
  
$data = json_decode(file_get_contents("php://input"));

$sendermail = $data->maileremail;
$serndername = $data->mailername;
$sendermessage = $data->mailarea;
    
$to = "rickryan29.rr@gmail.com"; // this is your Email address
$from = $sendermail; // this is the sender's Email address
$name = $serndername;
$subject = "Rick Ryan Website Email";
$message = $name . "\n" . "Wrote the following:" . "\n\n" . $sendermessage;

$headers = "From:" . $from;
$send = mail($to,$subject,$message,$headers);
// echo "Mail Sent. Thank you " . $name . ", we will contact you shortly.";

if( $send == true ) {
  echo json_encode(array(
          "success" => true
      ));
        return false;
}else {
  echo json_encode(array(
          "success" => false
      ));
        return false;
}
?>
