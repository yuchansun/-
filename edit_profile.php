<?php require_once "header.php"?>
<?php

session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}
// 檢查使用者是否登入


// require_once "db.php";

// 取得登入使用者的帳號
// $account = $_SESSION["account"];
$oldAccount = $_SESSION["account"];

try {
    require_once 'db.php';
    if ($_POST) {

// 如果是表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name=$_POST["name"];
    $newAccount = $_POST["account"]; 
    $password=$_POST["password"]; 


    // 更新資料庫
    // $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE account = ?";
    // $sql = "UPDATE user ( account, password ,role ,name, created_at) set (?, ?, 'U', ? , now())"  ; //問題在這一行  
    // $sql = "UPDATE user ( account, password ,role ,name, created_at) set (?, ?, 'U', ? , now())"  ; //問題在這一行 UPDATE user SET password = ?, name = ?, created_at = NOW() WHERE account = ? 
    // $sql = "UPDATE user SET password = ?, name = ?, created_at = NOW() WHERE account = ?";
        
    // $stmt = mysqli_stmt_init($conn);
    // mysqli_stmt_prepare($stmt, $sql);
    // mysqli_stmt_bind_param($stmt, "sss", $account, $password,$name);
    // $result = mysqli_stmt_execute($stmt);

    // if ($result) {
    //     header('location:logout.php');
    //   }
    //   else {
    //       header('location:edit_profile.php?msg=無法更新資料');
    //       exit();
    //   }
    // }


    // $sql="UPDATE user set account=?, password=?, name=? where account=?";

    // $stmt = mysqli_stmt_init($conn);

    // mysqli_stmt_prepare($stmt, $sql);

    // mysqli_stmt_bind_param($stmt, "ssss",$newAaccount, $password, $name, $oldAccount);

    // $result = mysqli_stmt_execute($stmt);



    $sql = "UPDATE user SET  account=?, password = ?, name = ?, created_at = NOW() WHERE account = ?";
       $stmt = mysqli_prepare($conn, $sql);
       mysqli_stmt_bind_param($stmt, "ssss",$newAccount, $password, $name, $oldAccount);
       $result = mysqli_stmt_execute($stmt);


        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["account"] = $newAccount;
            $msg = "資料更新成功！請重新登入。";
            header('Location: logout.php'); // 更新後強制登出
            exit();
        } else {
            $msg = "更新失敗：" . mysqli_error($conn);
        }
    } else {
        // 從資料庫中取得使用者資料
        $sql = "SELECT name, password FROM user WHERE account = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $oldAccount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name, $password);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_close($conn);   ////
  }
}
catch(Exception $e) {
    header('location:edit_profile.php?msg=更新錯誤');
    exit();
  }

?>

<!DOCTYPE html>
<html>
<head>
    <title>修改註冊資料</title>
</head>
<body>
    <h1>修改註冊資料</h1>
    
<form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">姓名</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="輸入姓名" required >
        </div>

        <div class="mb-3">
            <label for="account" class="form-label">帳號</label>
            <input type="text" class="form-control" id="account" name="account" value="<?= htmlspecialchars($oldAccount) ?>"  placeholder="輸入帳號" required >
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">密碼</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="輸入密碼" required>
        </div>

        <button type="submit" class="btn btn-primary">更新資料</button>
    </form>