<?php

require_once "header.php";

try {

  $stu_id = "";

  $name = "";

  $position = "";

  $activities = "";



  if ($_GET) {

    require_once 'db.php';

    $action = $_GET["action"]??"";

    if ($action=="confirmed"){

      //delete data

      $stu_id = $_GET["stu_id"];

      $sql="delete from member where stu_id=?";

      $stmt = mysqli_stmt_init($conn);

      mysqli_stmt_prepare($stmt, $sql);

      mysqli_stmt_bind_param($stmt, "i", $postid);

      $result = mysqli_stmt_execute($stmt);

      mysqli_close($conn);

      header('location:成員活躍度追蹤.php');

    }

    else{

      //show data

      $stu_id = $_GET["stu_id"];

      $sql="select stu_id, name, position, activities from member where stu_id=?";    

      // $result = mysqli_query($conn, $sql);

      $stmt = mysqli_stmt_init($conn);

      mysqli_stmt_prepare($stmt, $sql);

      mysqli_stmt_bind_param($stmt, "i", $stu_id);

      $res = mysqli_stmt_execute($stmt);

      if ($res){

        mysqli_stmt_bind_result($stmt, $stu_id, $name, $position, $activities);

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