<?php

    date_default_timezone_set("America/New_York"); 
  
    include 'dblogin_interface.php'; 
    include 'autolog.php'; 
    include 'targets.php'; 
    include 'sqlCheck.php'; 
    include 'interface.php'; 

    $target = targetIs('auto'); 
    $response = file_get_contents('php://input'); 
    $decoder = json_decode($response, true); 
   
    if (! empty($decoder)) {
        $write = "+ data received in modA.php\n"; 
        $write .= print_r($decoder, true); autolog($write, $target); 
        
    } 

    if (! $feedback = setMod($conn, $decoder)) {
        $error = "backend at setMod() failed."; 
        $report = array('type' => 'modA', 'error' => $error); 
        echo json_encode($report); 
        $write = "+ " . $error . " \n"; autolog($write, $target); 
    } else {
        $write = "+ called the function setMod(), output: \n"; 
        $write .= print_r($feedback, true); autolog($write, $target);
        echo $feedback; 
    } 


    function setMod($conn, $decoder) {
         /* temp response: */
         $error = "mod A response is being developed."; 
         $report = array('type' => 'modA', 'error' => $error); 
         return json_encode($report); 

         $target = targetIs('auto'); 
         $rel = $decoder['release']; 
         $remarks = $decoder['remarks']; 
         $grades = $decoder['grades']; 
         $feedback = $decoder['feedback']; 
         $arrayoftIds = array(); 
         
        /* task ALPHA: set remarks. */ 
        $write = "+ setMod() task Alpha, set remarks \n"; 
        $write = print_r($remarks, true) . "\n"; autolog($write, $target); 
        if (empty($remarks)){
           $write = "+ setMod(), remarks object is empty\n"; 
           $error = "modA remarks object is empty. "; 
        }//if empty remarks    
        else {
            foreach($remarks as $x) {
                $srem = addslashes($x['newR']); 
                $tId = $x['tId']; $qId = $x['qId']; 
                $arrayoftIds = buildarrayoftIds($tId, $arrayoftIds); 
                $sql1 = "UPDATE rd248.QuestionStudentRelation SET remarks = '$srem' 
                      WHERE QuestionStudentRelation.testId = '$tId' AND 
                      QuestionStudentRelation.questionId = '$qId'"; 
                if (! $result1 = sqlCheck($sql1, $conn)) {
                   $error .= "sql1 in modA, remarks update failed. "; 
                   $write = "+ error: " . $error ."\n"; 
                   autolog($write, $target); 
                } else {
                   $write = "+ modA, update of remarks succesful; tId: " . $tId . " qId: " . $qId . 
                            " remark: " . $srem . "\n"; 
                   autolog($write, $target);  
                }//if result sqlcheck conn 
           }
        }//if empty remarks else 
        
        /* task BRAVO: set grades */ 
        $write = "+ setMod() task BRAVO, set grades \n"; 
        $write = print_r($grades, true) . "\n"; autolog($write, $target); 
        if (empty($grades)){
            $write = "+ setMod(), grades array is empty\n"; 
            $error .=  "modA grades is empty. "; 
        } else {
            foreach($grades as $y) {
                $grade = $y['grade']; 
                $tId = $y['tId']; $qId = $y['qId']; 
                $arrayofIds = buildarrayoftIds($tId, $arrayoftIds); 
                $sql2 = "UPDATE rd248.QuestionStudentRelation SET grade = '$grade' 
                        WHERE QuestionStudentRElation.testId = '$tId' AND
                        QuestionStudentRelation.questionId = '$qId'"; 
                if (! $result2 = sqlCheck($sql2, $conn)) {
                    $error .= "sql2 in modA, graes update failed. "; 
                    $write = "+ error: " . $error . "\n"; 
                    autolog($write, $target); 
                } else {
                    $write = "+ modA, update of grades succesful; tId: " . $tId . " qId: " . $qId .
                             " grade: " . $grade . "\n"; 
                    autolog($write, $target); 
                }//if result2 sqlcheck 
            }//foreach grades as y
        }//if empty grades else 
          
   }//setMod

   function buildarraytIds($tId, $arrayoftIds) {
        /* desc: this checks if the tId is already in the array of tIds,
           if not, it is pushed into the array. this returns the new array. */ 

        $miss = 0; 
        foreach($arrayoftIds as $x) {
                $miss   = 0;  
                if ($x ===  $tId) {
                    $miss = 0; 
                    return false; 
                } else {
                    $miss = 1;  
                }               
        }
        if ($miss ===  1) {
                array_push($arrayoftIds, $tId); 
                return $arrayoftIds; 
        }
   }//buildarraytIds()
?>

