<?php
   // Initialize database
   $host = "";
   $user = "";
   $pass = "";
   $db = "";

   global $mysqli;
   $mysqli = new mysqli($host, $user, $pass, $db);
   // Error checking
   if ($mysqli->connect_errno) {
      echo $mysqli->connect_error;
      exit();
   }
?>
