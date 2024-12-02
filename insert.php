<?php

require_once "header.php";

try {

  require_once 'db.php';
  SESSION_start();
  // 檢查角色是否為管理員
if (!($_SESSION["role"]=="M")) {
  echo "權限不足，無法新增資料";
  exit();

}

$msg="";



if ($_POST) {

  // insert data

  $company = $_POST["company"];

  $content = $_POST["content"];

  $sql="insert into job (company, content, pdate) values (?, ?, now())";

  $stmt = mysqli_stmt_init($conn);

  mysqli_stmt_prepare($stmt, $sql);

  mysqli_stmt_bind_param($stmt, "ss", $company, $content);

  $result = mysqli_stmt_execute($stmt);



  if ($result) {

    header('location:query.php');

  }

  else {

    $msg = "無法新增資料";

  }

  

}
?>

<html>
<div class="container">

<form action="insert.php" method="post">

  <div class="mb-3 row">

    <label for="_company" class="col-sm-2 col-form-label">求才廠商</label>

    <div class="col-sm-10">

      <input type="text" class="form-control" id="_company" name="company" placeholder="公司名稱" required>

    </div>

  </div>

  <div class="mb-3">

    <label for="_content" class="form-label">求才內容</label>

    <textarea class="form-control" id="_content" name="content" rows="10" required></textarea>

  </div>

  <input class="btn btn-primary" type="submit" value="送出">

  <?=$msg?>

</form>

</div>







<?php

  mysqli_close($conn);

}

//catch exception

catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}

require_once "footer.php";

?>