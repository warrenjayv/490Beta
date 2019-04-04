<?php 
$dbserver = "sql1.njit.edu";
$mySql_user = "rd248";
$mySql_password = "aZrVVjeCv";
$mySql_database = "rd248";

$response   = file_get_contents('php://input');
$decoder    = json_decode($response, true);

$question   = $decoder['desc'];
$question = addslashes($question); 
$difficulty = $decoder['diff'];
$cases      = $decoder['tests'];
// $keywords  = $decoder['Keys'];
$category = $decoder['topic'];
$category = addslashes($category);

/*testpoint*/

$conn       = mysqli_connect($dbserver, $mySql_user, $mySql_password, $mySql_database);
if (!$conn) {
       $error .= "failed to connect to database ";  
}
 // if (isset($decoder['Desc']) && (empty($question) == false) && (empty($category) == false))  {

   //    if(empty($cases) == false) {
   //         echo addQUEST($conn, $question, $difficulty , $cases, $category);
   //    } else {
   //        $error .= "no test cases inserted!"; 
  //	   $report = array("Type" => "AddQ", "Error" => $error); 
  //	   echo json_encode($report); 
   //    }

if(! empty($decoder))  {
     if(! $feedback = addQUEST($conn, $question, $difficulty, $cases, $category))  {
         $error = "backend addQUEST() failed!";
	 $report = array("type" => "addQ", "error" => $error);
	 echo json_encode($report); 
      }  else {
         echo $feedback; 
      }
} else {
  
      $error .= "empty desc. or topic"; 
      $report = array("type" => "addQ", "error" => $error); 
      echo json_encode($report);         

}

  function addQUEST($conn, $question, $difficulty , $cases, $category) {
      
      $sql1 = "SELECT * FROM Question"; 
       if ( ! $result1 = $conn->query($sql1)) { 
      //if (! $result1) { //NOTE: running another IF statement on queries RUNS its twice!! 
          $sqlerror = $conn->error; 
          //return "type: AddQ; SQL1 = " . $error; 
          $error .= "sql: " . $sqlerror . " "; 
       } else {   
          $id = $result1->num_rows; 
          $id += 1; //this counts the number of rows and add 1 for the next SQL statement.  
      } //if else
      
      $sql2 = "INSERT INTO Question (Id, question, difficulty, category) VALUES ('$id', '$question', '$difficulty',  '$category')";   
       if (! $result2 = $conn->query($sql2)) { 
      //if (! $result2) { ///NOTE: running another IF statement on queries RUNS its twice!!
        $sqlerror2 = $conn->error; 
        $error .= "sql2: " . $sqlerror2 . " "; 
       } else {
      
      ///*** adds the testcases to the '$id' that is certified above /// 
      ///*** '$cases' is already an array of testcases. loop through it. ///      
           foreach($cases as $v) {
               $v = addslashes($v);
               $sql3 = "INSERT INTO TestCases (questionID, testcases) VALUES  ('$id', '$v')"; 
	          if (! $result3 = $conn->query($sql3)) {
                    $sqlerror3 = $conn->error; 
                     $error .= "sql3: " . $sqlerror3 . " "; 
	              //return "type: AddQ; SQL3 = " . $error; 
	          } //if
            }//foreach

      }//else

      //note: Id(in the database) is not included here as it autoincremenets per insert. 
      if ($error === null) {
           $error = 0; 
      } 
      
      $question = stripslashes($question);
      $category = stripslashes($category);
      $questionobj  = array("id" => $id, "desc" => $question, "topic" => $category, "diff" => $difficulty, "tests" => $cases); 
     // $feedback = array("Type" => "AddQ", "Id" => $id, "Error" => $error, "Desc" => $question, "Topic" => $category, "Difficulty" => $difficulty, "Tests" => $cases);  
      $feedback = array("type" => "addQ", "error" => $error, "que" => $questionobj); 
      $recoil = json_encode($feedback); 
      if ($recoil == false) {
              return "backend tried to encode JSON and failed."; 
      } else {
         return $recoil; 
      }
      
  } //addQUEST(); 



// echo json_encode($backPACK); 

/*

$sql2    = "INSERT INTO Questions (Id, question, difficulty,signature,category) VALUES ('$id','$question', '$difficulty','$signature','$category')";
$result2 = $conn->query($sql2);

$c       = count($cases);

for ($x = 0; $x < $c; $x++) {
    $sql4    = "INSERT INTO TestCasess (Id, QuestionId, testCases, answer)
       VALUES('$id','$id', '$mycases[$x]', 'null')";
       $result4 = $conn->query($sql4);
}
if ($result4) {
    $log = array(
        "Response" => "Successfully Inserted"
    );
    echo json_encode($log, true);
} else {
    $log = array(
        "Response" => "Just Quit"
    );
    echo json_encode($log, true);
}
*/
mysqli_close($conn);
?>
