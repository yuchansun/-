<?php

$msg = $_GET["msg"]??"";

?>

<?php require_once "header.php"?>

<div class="container">

<form action="login_process.php" method="post">

  <input placeholder="帳號" class="form-control" type="text" name="account"><br>

  <input placeholder="密碼" class="form-control" type="password" name="password"><br>

  <input class="btn btn-primary" type="submit" value="登入">


  <!--  -->

  <a class="fcc-btn" href="sign.php">註冊</a>  
<!-- 
  <input class="btn btn-primary" type="submit" value="註冊">
  <a herf="sign.php"> -->


  
  <!--  -->


  <?=$msg?>

</form>

</div>

<?php require_once "footer.php"?>