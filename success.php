
<?php require_once "header.php"?>
<?php

session_start();

if ($_SESSION["account"]){

  echo "welcome!".$_SESSION["account"]."<p>";

//   echo "<a href='logout.php'>Logout</a><p>";

//   echo "<a href='login.php'>login.php</a>";

//   echo "<a href='success2.php'>success2.php</a>";

}

else {

  header("Location: login.php");

}



?>
  <?php require_once "footer.php"?>