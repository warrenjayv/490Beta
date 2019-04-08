<?php
date_default_timezone_set("America/New_York"); 

include 'dblogin_interface.php'; 
include 'autolog.php'; 
include 'targets.php'; 
include 'testBuilder.php'; 

// $target = '/afs/cad/u/w/b/wbv4/public_html/Middle/tracklogs/getAt.txt';
$target = targetIs('getA'); 
$response = file_get_contents('php://input'); 
$decoder = json_decode($response, true); 

$write = "[ + ] page accessed getA " . date("Y-m-d h:i:sa") . "\n"; 
autolog($write, $target); 

/*testpoint*/
/* $decoder = array("ids" => array("10"));*/

if (! empty($decoder)) {
	$write = "data received...\n"; 
	$write .= print_r($decoder, true) . "\n";  autolog($write, $target); 

	if ( ! $feedback = getAttempt($conn, $decoder)) {
		$error = "backend getAttempt() failed. please check logs! ";
		$write = $error . "\n"; autolog($write, $target); 	
	} else {
		$write = "executing getAttempt()...output:\n"; 
		$write .= print_r($feedback, true); autolog($write, $target); 
		echo $feedback; 
	}	
}//if empty decoder
else {
	 $error = "decoder at backend of getA received empty or null"; 
	 $write = $error . "\n"; autolog($write, $target); 
	 $report = array('type' => 'getA', 'error' => $error); 
	 echo json_encode($report); 
	 return 0; 
} 

function getAttempt($conn, $decoder) {
	$target = targetIs('getA'); 
        $ids = $decoder['ids']; //test ids

	$write = "executing getAttempt() with testIds... " . print_r($ids, true) . "\n"; 
	
	$attemptArray  = array(); //array of attempt json objects. 
//	$testArray = array(); //test 

	foreach($ids as $x) {
		
		$ansObj = array(); 
		$write = "executing testObject() for test id : " . $x . " \n"; 
	        if ($testObj = testObject($conn, $x)) {
				$write .= print_r($testObj, true) . "\n"; 
				$write .= "pushed test "  . $testObj['id'] . " into testArray\n"; 
				autolog($write, $target); 
			//	array_push($testArray, $testObj); 
			} else {
				$write = "testObj() failed in getAttempt()! terminating.\n"; 
				autolog($write, $target); 
				return false;  
			}
		
		$write = "executing ansObject() for test id : " . $x . " \n"; 
		    if ($ansObj  = ansObject($conn, $x)) {
		    		$write .= "ansObject() was succesful...output: \n"; 
				$write .= print_r($ansObj, true) . "\n"; 
				$write .= "warning! ansObject is an array!\n"; 
				autolog($write, $target); 
				//array_push($ansArray, $array); 
			} else {
				$write = "critical: ansObject() failed!\n"; autolog($write, $target); 
				return false; 
			}
	
	$write =  "building the attempt object...\n";
	$temp = array('test' => $testObj);
//	$temp2 = array($ansObj); 
	$temp = array_merge($temp, (array)$ansObj); 
	$write .= print_r($temp, true);
	$write .= "pushing the attempt object into array of attempts\n";
	autolog($write, $target); 
	array_push($attemptArray, $temp); 

	}//foreach ids as x 
		
	if ($error == null) { $error = 0; } 
	$payload  = array('type' => 'getA', 'error' => $error, 'answers' => $attemptArray); 
	$package = json_encode($payload); 
	return $package; 

}//getAttempt(); 

function ansObject($conn, $id) {
	$ansArray = array(); 
	     $userAnswers = array(); 
	     $feedbacks = array(); 
	     $comments = array(); 

        $target = targetIs('getA'); 
	$write = "executing ansObject() with testId : " . $id . "\n"; 
	$sql = "SELECT * FROM QuestionStudentRelation WHERE testId = '$id' "; 
		if (! $result = $conn->query($sql)) {
			$sqlerror = $conn->error; 
			$error = "sql: " . $sqlerror . " "; 
			$write = $error . "\n"; autolog($write, $target); 
			return false; 
		} else {
			while($row = mysqli_fetch_assoc($result)) {
				array_push($userAnswers, $row['userAnswer']); 
				array_push($feedbacks, $row['feedback']); 
				array_push($comments, $row['comment']); 
			}//while row mysqli 
			$ansArray = array('answers' => $userAnswers, 'feedback' => $feedbacks, 'remarks' => $comments); 

			$write = "ansObject() formed the ansArray\n"; 
			$write .= print_r($ansArray, true) . "\n"; 
			autolog($write, $target); 
			return $ansArray; 
		}//if result conn-query 
}


?> 
