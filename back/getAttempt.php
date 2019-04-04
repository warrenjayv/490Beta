<?php
date_default_timezone_set("America/New_York"); 

include 'dblogin_interface.php'; 
include 'autolog.php'; 

$target = '/afs/cad/u/w/b/wbv4/public_html/Middle/tracklogs/getAt/txt';
$response = file_get_contents('php://input'); 
$decoder = json_decode($response, true); 

$write = "[ + ] page accessed addT " . date("Y-m-d h:i:sa") . "\n"; 
autolog($write, $target); 

if ( ! $feedback = getAttempt($conn, $decoder)) {
	$error = "backend getAttempt() failed.";
	$write = $error . "\n"; autolog($write, $target); 	
} else {
	$write = "executing getAttempt()...output:\n"; 
	$write .= print_r($feedback, true); autolog($write, $target); 
	echo $feedback; 
}

$getAttempt($conn, $decoder)) {
	return 1; 
}

?> 
