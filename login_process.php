
<?php

session_start();

//檢查是否取得POST內容

$account = $_POST['account'] ?? "N/A";

//因為db.php裡有$password

$_password = $_POST['password'] ?? "N/A";

try {

  require_once 'db.php';

  $sql = "select * from user where account = '$account'";

  $result = mysqli_query($conn, $sql);

  if($row = mysqli_fetch_assoc($result)) {

    if ($row["password"]==$_password){

      echo "登入成功";

      $_SESSION["account"]=$account;
      $_SESSION["role"] = $row['role']; 

      header("Location: 成員活躍度追蹤.php");   


    }

    else {

      echo "登入失敗";

      header("Location: login.php?msg=帳密錯誤");

    }

  }

  else {

    echo "登入失敗";

    header("Location: login.php?msg=帳密錯誤");

  }

  $conn = null;

}  //catch exception

catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}



?>x