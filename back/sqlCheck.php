<?php

  function sqlCheck($sql, $conn) {
      $target = targetIs('modA'); 
      $write = "+ running sqlCheck for : \n" . $sql . "\n"; 
      autolog($write, $target); 
      if (! $result = $conn->query($sql)) {
        $sqlerr = $conn->error; 
        $error = "sqlCheck() error: " . $sqlerr . " "; 
        echo $error; 
        $write = "+ " . $error . "\n"; 
        autolog($write, $target); 
        return false; 
      } else {
         return $result; 
      }
  }//sqlCheck()

?>
