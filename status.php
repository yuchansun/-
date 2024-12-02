<?php require_once "header.php"?>
    
<?php

session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}

// if (!$_SESSION["account"]){

// //   header("Location: index.php");

// }
$account = "student1";
$password = "1";


if ($_POST){

//   echo $_POST["account"],"<br/>";

//   echo "Status:<br/>";

  

  //new syntax in php 7

  $statuslist = $_POST["status"]?? ["N/A"];

  

  foreach( $statuslist as $status ) {

    echo "$status <br/>";

  }

  

  $dinner = $_POST["dinner"]?? "";

  echo "$dinner <br/>";

}



?>


<!-- 這邊複製錯了 -->




<form action="status.php" method="post">



<!-- name:<input type="text" name="name" /><br/>-->
  <?php echo  $_SESSION["account"] ?> <br>

<input type="checkbox" name="status[]" value="faculty" checked="checked" /> Faculty<br>

<input type="checkbox" name="status[]" value="student" /> Student<br/>

<input type="checkbox" name="dinner" value="dinner" checked="checked" /> Dinner needed<br>



<input type="submit" value="Submit" />



</form>



<?php require_once "footer.php"?>
