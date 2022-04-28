<?php
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set('Asia/Manila');
 
// variables used for jwt
$key = "P@ssw0rd";
$secretKey = "$2y$10$95ZU0.ialtPJlFdPceQ2KeG1TQO57eRzIwx.S7dCyGFFxoqfvEdoy";
$iss = "http://localhost:8080/Rick-Portfolio-Api-JWT/";
$aud = "http://localhost:8080/Rick-Portfolio-Api-JWT/";
$iat = 1356999524;
$nbf = 1357000000;
?>  