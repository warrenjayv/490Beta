<?php

    date_default_timezone_set("America/New_York"); 
  
    include 'dblogin_interface.php'; 
    include 'autolog.php'; 
    include 'targets.php'; 
    include 'sqlCheck.php'; 
    include 'interface.php'; 

    $target = targetIs('modA'); 
    $response = file_get_contents('php://input'); 
    $decoder = json_decode($response, true); 
   
    if (! empty($decoder)) {
        $write = "\n + data received in modA.php\n"; 
        $write .= print_r($decoder, true); autolog($write, $target); 
        
    } 

/*test input*/
/*
    $write = "+ test input is being inserted to test product.\n"; 
    $decoder = array('type' => 'modA', 'rel' => '1', 
               'remarks' => array('0'=> array('tId' => '7', 'qId' => '3', 'newR' => 'you cheated.')), 
               'feedback' => array(), 
               'grades' => array('0' => array('tId' => '7', 'qId' => '3', 'newG' => '-100')));
    $write .= print_r($decoder, true) . "\n";  
    autolog($write, $target); 

*/
    if (! $feedback = setMod($conn, $decoder)) {
        $error = "CRITICAL backend at setMod() failed."; 
        $report = array('type' => 'modA', 'error' => $error); 
        echo json_encode($report); 
        $write = "+ " . $error . " \n"; autolog($write, $target); 
    } else {
        $write = "\n+  called the function setMod(), output: \n"; 
        $write .= print_r($feedback, true); autolog($write, $target);
        echo $feedback; 
    } 


    function setMod($conn, $decoder) {
         /* temp response: */
        //  $error = "mod A response is being developed."; 
        //  $report = array('type' => 'modA', 'error' => $error); 
        //  return json_encode($report); 

         $target = targetIs('modA'); 
         $rel = $decoder['rel']; 
         $remarks = $decoder['remarks']; 
         $grades = $decoder['grades']; 
         $feedbacks = $decoder['feedback']; 
         $arrayoftIds = array(); 
         
        /* task ALPHA: set remarks. */ 
        $write = "+ setMod() task Alpha, set remarks \n"; 
        $write = print_r($remarks, true) . "\n"; autolog($write, $target); 
        if (empty($remarks)){
           $write = "+ setMod(), remarks object is empty\n"; 
           $error .= "modA remarks object is empty. "; 
        }//if empty remarks    
        else {
            foreach($remarks as $x) {
                $srem = addslashes($x['newR']); 
                $tId = $x['tId']; $qId = $x['qId']; 
                $arrayoftIds = buildarraytIds($tId, $arrayoftIds);
                $write = "+ check arrayoftIds: " . print_r($arrayoftIds, true) . "\n"; 
                      autolog($write, $target);  
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
                $grade = $y['newG']; 
                $tId = $y['tId']; $qId = $y['qId']; 
                $arrayoftIds = buildarraytIds($tId, (array)$arrayoftIds); 
                $write = "+ check arrayoftIds: "  . print_r($arrayoftIds, true) . "\n"; 
                      autolog($write, $target);  
                $sql2 = "UPDATE rd248.QuestionStudentRelation SET grade = '$grade' 
                        WHERE QuestionStudentRelation.testId = '$tId' AND
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

        /* task CHARLIE: set new feedbacks */ 
       $write = "+ setMod() task CHARLIE, set feedbacks \n"; 
       $write = print_r($feedbacks, true) . "\n"; autolog($write, $target); 
       if (empty($feedbacks)){
          $write = "+ setMod(), feedbacks array is empty \n"; 
          $error .= "modA feedbacks is empty. "; 
       } else {
          foreach($feedbacks as $z) {
            $feed = addslashes($z['newF']); 
            $tId = $z['tId']; $qId = $y['qId']; 
            $index = $z['index']; 
            $index += 1; //add 1 because it starts at 1 in DB 

            $write = "+ updating feedback for tId : " . $tId . ", qId : "  . $qId . "\n"; 
            $write = "+ index : " . $index . "\n"; 
            $arrayoftIds = buildarraytIds($tId, (array)$arrayoftIds); 
            $write = "+ check arrayoftIds:\n" . print_r($arrayoftIds, true) . "\n"; 
                   autolog($write, $target); 
            $sql3 = "UPDATE rd248.Feedback SET feedback = '$feed' WHERE
            Feedback.testId = '$tId' AND Feedback.questionId = '$qId'
            AND Feedback.arrayindex = '$index'";
             if (! $result3 = sqlCheck($sql3, $conn)) {
                   $error .= "sql3 in modA, feedback update failed. "; 
                   $write = "+ error: " . $error . "\n"; 
                   autolog($write, $target); 
             } else {
                   $write = "+ modA, update of feedback succesful; tId: " . $tId . " qId: " . $qId . " new feedback: "
                             . $feed . "\n"; 
                   autolog($write, $target); 
             }//if result3 sqlcheck
          }//foreach feedback as z
       }//if empty feedbacks else 

       /* task FOXTROT: set the rel according to modA request. */ 
       $write = "+ setMod() task FOXTROT, set rel settings to " . $rel . "\n"; 
       autolog($write, $target); 
       $sql4 = "UPDATE rd248.QuestionStudentRelation SET rel = '$rel' 
               WHERE QuestionStudentRelation.testId = '$tId'"; 
       if (! $result4 = sqlCheck($sql4, $conn)) {
            $error .= "sql4 in modA, rel update failure. "; 
            $write = "+ error " . $error . "\n"; 
            autolog($write, $target); 
       } else {
            $write = "+ modA, update of rel is succesful for tId: " . $tId . 
                     " qId: " . $qId . "\n"; 
            autolog($write, $target); 
       }//if result4 sqlcheckc onn

       /* task DELTA: return the attempt object in proper json format */ 
       /* if arrayoftIds is empty, insert a false one */ 
       if (empty($arrayoftIds)) {
          $write = "+ arrayoftIds were empty. \n"; 
                 autolog($write, $target); 
          $decoder = array('ids' => array('0' => '0')); 
       } else { 
           $decoder = array("ids" => $arrayoftIds); 
       }
       $package = getAttempt($conn, $decoder); 
       $attempt = $package['attempts']; 
       $attempt = $attempt[0]; 
       $report = array('type' => 'modA', 'error' => $error, 
                 'attempt' => $attempt);  
       return json_encode($report); 
   }//setMod

   function buildarraytIds($tId, $arrayoftIds) {
        /* desc: this checks if the tId is already in the array of tIds,
           if not, it is pushed into the array. this returns the new array. */ 
        $target = targetIs('modA'); 
        $write = "+ executing the buildarray for tIds\n"; 
        $write .= "+ input: tId: " . $tId . " array: " . print_r($arrayoftIds, true) . "\n"; 

        if(empty($arrayoftIds)) {
            $write = "+ arrayoftIds is detected empty at buildarray()\n"; 
            $write .= "+ appending ." . $tId . " to the array\n"; 
            autolog($write, $target); 
            $arrayoftIds = array(); 
            array_push($arrayoftIds, $tId); 
        }

        autolog($write, $target); 
        $miss = 0; 
        foreach((array) $arrayoftIds as $x) {
                $miss   = 0;  
                if ($x ===  $tId) {
                    $miss = 0; 
                    return (array)$arrayoftIds;   
                } else {
                    $miss = 1;  
                }               
        }//foreach arrayoftIds as x
        if ($miss ===  1) {
                array_push($arrayoftIds, $tId); 
                return  (array)$arrayoftIds; 
        }
   }//buildarraytIds()

   
?>
