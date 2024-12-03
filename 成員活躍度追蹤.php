<?php

require_once "header.php";


session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}
try {
  require_once 'db.php';
  // $sql="select * from job";
  // $result = mysqli_query($conn, $sql);
?>


<?php 


//  $order 
 $order = $_POST["order"]??"";
 $sql="select * from job";
 if ($order){
  $sql.=" order by $order";
}
// $result = mysqli_query($conn, $sql);


//  $searchtxt = $_POST["searchtxt"] ?? ""
 $searchtxt = $_POST["searchtxt"]??"";
 $searchtxt = mysqli_real_escape_string($conn, $searchtxt); //使用mysqli_real_escape_string()來避免語法錯誤
 $sql="select * from job where company = '$searchtxt'";
 $condition = $searchtxt ? "where company like '%$searchtxt%' or content like '%$searchtxt%'":"";
 $sql="select * from job $condition";
 
//  $result = mysqli_query($conn, $sql);
 
//  $date
 $start_date = $_POST["start_date"]??"";
 $end_date = $_POST["end_date"]??"";

 



if ($searchtxt){
  // $sql="select * from job where company = '$searchtxt'";
  $condition = $searchtxt ? "where (company like '%$searchtxt%' or content like '%$searchtxt%') ":"";
  $sql="select * from job $condition";
} 

if ($start_date){
  if ($condition){$condition.=" and pdate >= '$start_date'";}
  else{
  $condition .=" where pdate >= '$start_date'";
  }
  if ($end_date){
    $condition .=" and pdate <= '$end_date' ";
  }
  $sql="select * from job $condition";
}
else {

if ($end_date){
  if ($condition){$condition.=" and pdate >= '$end_date'";}
  else{
  $condition .=" where pdate <= '$end_date'";}
  $sql="select * from job $condition";
}
}
if ($order){
  $sql.=" order by $order";
}
// echo $sql;
  $result = mysqli_query($conn, $sql);

 ?>

<!-- 選擇排序欄位 -->
<br>
<a href="insert.php" class="btn btn-primary position-fixed bottom-0 end-0">+</a>

<form action="query.php" method="post">

  <select name="order" class="form-select" aria-label="選擇排序欄位">

    <option value="" <?=($order=='')?'selected':''?>>選擇排序欄位</option>

    <option value="company" <?=($order=="company")?"selected":""?>>求才廠商</option>

    <option value="content" <?=($order=="content")?"selected":""?>>求才內容</option>

    <option value="pdate" <?=($order=="pdate")?"selected":""?>>刊登日期</option>

  </select>

  <input placeholder="搜尋廠商及內容" class="form-control" type="text" name="searchtxt" value="<?=$searchtxt?>">

  <div class="row g-3 align-items-center">

    <div class="col-auto">

      <label for="start_date" class="col-form-label">開始日期</label>

    </div>

    <div class="col-auto">

      <input id = "start_date" class="form-control" type="date" name="start_date" value=<?=$start_date?>>

    </div>

    <div class="col-auto">

      <label for="end_date" class="col-form-label">結束日期</label>

    </div>

    <div class="col-auto">

      <input id = "end_date" class="form-control" type="date" name="end_date" value=<?=$end_date?>>

    </div>

  </div>

  <input class="btn btn-primary" type="submit" value="搜尋">

</form>


<div class="container">

<table class="table table-bordered table-striped">

 <tr>

  <td>求才廠商</td>

  <td>求才內容</td>

  <td>日期</td>

 </tr>

 <?php

 while($row = mysqli_fetch_assoc($result)) {?>

 <tr>

  <td><?=$row["company"]?></td>

  <td><?=$row["content"]?></td>

  <td><?=$row["pdate"]?></td>
  <td><a href="delete.php?postid=<?=$row["postid"]?>" class="btn btn-primary">刪除</a>

 </tr>

 <?php

  }

 ?>

</table>

</div>

<?php

  $conn = null; 

  

}

//catch exception

catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}

require_once "footer.php";

?>