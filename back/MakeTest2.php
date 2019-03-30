<?php

//lastupdate: 03/19/2019 8:13 PM

include 'dblogin_interface.php';

$response   = file_get_contents('php://input');
$decoder    = json_decode($response, true);


/*test point
$decoder = array("Desc" => "testing addT", "Rel" => '0', "Questions" => array("1")); 
*/ 
   if ($decoder != null) { //ensures this page doesn't run a query  if no input is received  
       if (! $feedback = addExam($conn, $decoder)) { //calls the function getQUEST() ; 
                $error .= "backend getExam() failed."; 
                $report = array("type" => "addT", "error" => $error); 
                echo json_encode ($report); 
        } else {
                echo $feedback; 
        }  
    } else {
               $error .= "backend received nothing.";
               $report = array("Type" => "addT", "error" => $error);
               echo json_encode ($report); 
    }   
      
function addExam($conn, $decoder) {
      $release = $decoder['rel'];
      $testName = $decoder['desc'];
      $testName = addslashes($testName);
      $questions = $decoder['ques']; 
      
      /*
      foreach($questions as $q) {
           $arrayofIds = $q['Id'];    
	    }
     */
      
       if (empty($testName)) {
                $error .= "TestName is NULL or Empty";
                $report = array("type" => "addT", "error" => $error);
                return json_encode($report); //terminate the program.
       }
       
       $sql1 = "SELECT * FROM Test"; 
           if ( ! $result1 = $conn->query($sql1)) { 
                 $sqlerror = $conn->error; 
                 $error .= "sql: " . $sqlerror . " "; 
           } else {   
                 $id = $result1->num_rows; 
                 $id += 1; //testid
           
           }//sql1 
           $sql2 = "INSERT INTO Test (Id, released, testName) VALUES ('$id', '$release', '$testName')"; 
                 if ( ! $result1 = $conn->query($sql2)) { 
                        $sqlerror2 = $conn->error; 
                        $error .= "sql2: " . $sqlerror2 . " "; 
                 } else { 
                     foreach ($questions as $x) { 
                        $qId = $x['id'];
                        $sql3 = "INSERT INTO QuestionStudentRelation (testId, questionId, testName) VALUES ('$id', '$qId', '$testName')"; 
                        if ( ! $result3 = $conn->query($sql3)) { 
                                    $sqlerror3 = $conn->error; 
                                    $error .= "sql3: " . $sqlerror3 . " ";
                         } else {
                             //succesful insert into table 'QuestionStudentRelation'
                         } 
                     
                     }//foreach arrayofIds as $x
                 }//sql2
                 
              //obtain each question.
          $arrayofQuestions = array(); 
          foreach($questions as $y) { 
            $qId2 = $y['id'];
            $sql4 = " SELECT * FROM Question WHERE Id = '$qId2' ";      
             if ( ! $result4 = $conn->query($sql4)) { 
                          $sqlerror4 = $conn->error; 
                          $error .= "sql4: " . $sqlerror4 . " ";               
             } else {   
                  while($row4 = mysqli_fetch_assoc($result4)) {
                      $description = $row4['question'];
                      $questionId = $row4['Id'];
                      $difficulty = $row4['difficulty'];
                      $topic = $row4['category']; 
                      $temp = array("id" => $questionId, "desc" => $description, "topic" => $topic, "diff" => $difficulty); 
                      array_push($arrayofQuestions, $temp); 
                  }//while $row4 = msqyli($result4)
             }  
          }//foreach $question as $y
          
                 if ($error === null) {
                      $error = 0; 
                  }
                  $testName = stripslashes($testName);
                  $package = array("type" => "addT", "error" => $error, "id" => $id, "desc" => $testName,  "Rel" => $release, "Sub" => "0", "ques" => $arrayofQuestions); 
                  return json_encode($package); 

}//addexam ()

?>