<?php

require_once "header.php";
$msg = $_GET["msg"] ?? "";

try {
    require_once 'db.php';
    if ($_POST) {

    // insert data
    $name=$_POST["name"];
    $account=$_POST["account"];  
    $password=$_POST["password"]; 



    // $sql= "insert into user ( , account, password, ) values ( ?, ?, ? )";

    $sql = "insert into user ( account, password ,role ,name, created_at) values (?, ?, 'U', ? , now())"  ; //問題在這一行
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $account, $password,$name);
    $result = mysqli_stmt_execute($stmt);
        
    if ($result) {
      header('location:login.php');
    }
    else {
        header('location:sign.php?msg=無法新增資料');
        exit();
    }
  }
  mysqli_close($conn);   ////
}
//catch exception

catch(Exception $e) {
    header('location:sign.php?msg=使用者帳號已存在');
    exit();
  }
?>

<form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">姓名</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="輸入姓名" required>
        </div>

        <div class="mb-3">
            <label for="account" class="form-label">帳號</label>
            <input type="text" class="form-control" id="account" name="account" placeholder="輸入帳號" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">密碼</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="輸入密碼" required>
        </div>

        <button type="submit" class="btn btn-primary">註冊</button>
    </form>
<!-- 
<form action="registor.php" method="post">
<table width="500" border="0">
<tr>
<td> </td>
<td>姓名</td>
<td><label for="textfield"></label>
<input type="text" name="username"/></td>
<tr>
<td> </td>
<td>帳號</td>
<td><label for="textfield"></label>
<input type="text" name="account"/></td>
</tr>
<tr>
<td> </td>
<td>密碼</td>
<td><label for="textfield"></label>
<input type="text" name="$password"/></td>
</tr>
<tr>
<td> </td>
<td> </td>
<td><input type="submit" name="button" id="button" value="送出" /></td>
</tr>
</table>

</form> -->



  
<?php require_once "footer.php"; ?>