

  
  <?php

$msg = $_GET["msg"]??"";

?>

<?php require_once "header.php"?>

<div class="container">
  <br>

<form action="login_process.php" method="post">

  <input placeholder="帳號" class="form-control" type="text" name="account"><br>

  <input placeholder="密碼" class="form-control" type="password" name="password"><br>

  <input id='登入' class="btn " type="submit" value="登入">


  <!--  -->

  <a id="註冊" class="fcc-btn " href="sign.php">註冊</a>  
<!-- 
  <input class="btn btn-primary" type="submit" value="註冊">
  <a herf="sign.php"> -->




    

  <!--  -->


  <?=$msg?>

</form>

</div>

<?php require_once "footer.php"?>

<!-- <style>
    .登入 {
      background-color: #284777;

    }

  </style> -->
  <style>
#登入 {
    background-color: #284777;
    color: white;
}
#登入:hover {
    background-color: #1a325a;
}
#註冊 {
    
    color: #284777;
}
</style>
