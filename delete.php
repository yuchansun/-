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

      //delete data

      $postid = $_GET["postid"];

      $sql="delete from job where postid=?";

      $stmt = mysqli_stmt_init($conn);

      mysqli_stmt_prepare($stmt, $sql);

      mysqli_stmt_bind_param($stmt, "i", $postid);

      $result = mysqli_stmt_execute($stmt);

      mysqli_close($conn);

      header('location:query.php');

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

  <table class="table table-bordered table-striped">

    <tr>

      <td>編號</td>

      <td>求才廠商</td>

      <td>求才內容</td>

      <td>刊登日期</td>

    </tr>

    <tr>

      <td><?=$postid?></td>

      <td><?=$company?></td>

      <td><?=$content?></td>

      <td><?=$pdate?></td>

    </tr>

  </table>

  <a href="delete.php?postid=<?=$postid?>&action=confirmed" class="btn btn-primary">刪除</a>



<?php

require_once "footer.php";

?>