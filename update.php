<?php

require_once "header.php";

try {

  $postid = "";

  $company = "";

  $content = "";

  $pdate = "";



  if ($_GET) {

    require_once 'db.php';

    $action = $_GET["action"]??"";
    

    if ($action=="confirmed"){

      $postid = $_GET["postid"];

      $company = $_POST["company"];

      $content = $_POST["content"];

      //update data

      $postid = $_GET["postid"];

      $sql="update job set company=?, content=? where postid=?";

      // $sql="delete from job where postid=?";

      $stmt = mysqli_stmt_init($conn);

      mysqli_stmt_prepare($stmt, $sql);

      mysqli_stmt_bind_param($stmt, "ssi",$company, $content, $postid);

      $result = mysqli_stmt_execute($stmt);

      echo $result;

      if ($result){

        // mysqli_close($conn);

        header('location:query.php');  

      }

    } 

    else{

      //show data

      $postid = $_GET["postid"];

      $sql="select postid, company, content, pdate from job where postid=?";    

      // $result = mysqli_query($conn, $sql);

      $stmt = mysqli_stmt_init($conn);

      mysqli_stmt_prepare($stmt, $sql);

      mysqli_stmt_bind_param($stmt, "i", $postid);

      $res = mysqli_stmt_execute($stmt);

      if ($res){

        mysqli_stmt_bind_result($stmt, $postid, $company, $content, $pdate);

        mysqli_stmt_fetch($stmt);

      }

    }//confirmed else

    mysqli_close($conn);

  }//$_GET
  

} catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}

?>

<div class="container">

<form action="update.php?postid=<?=$postid?>&action=confirmed" method="post">

<div class="mb-3 row">

  <label for="_company" class="col-sm-2 col-form-label">求才廠商</label>

  <div class="col-sm-10">

    <input type="text" class="form-control" name="company" id="_company" placeholder="公司名稱" value="<?=$company?>" required>

  </div>

</div>

<div class="mb-3">

  <label for="_content" class="form-label">求才內容</label>

  <textarea class="form-control" id="_content" name="content" rows="10" required><?=$content?></textarea>

</div>

<input class="btn btn-primary" type="submit" value="送出">

</form>

</div>
<!-- 
  <a href="delete.php?postid=<?=$postid?>&action=confirmed" class="btn btn-primary">刪除</a>
 -->


<?php

require_once "footer.php";

?>