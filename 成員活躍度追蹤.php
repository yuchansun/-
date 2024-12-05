<?php

require_once "header.php";


session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}
try {
  require_once 'db.php';


// 排序及搜尋邏輯
//  $order 
 $order = $_POST["order"]??"";
 $searchtxt = $_POST["searchtxt"]??"";
 $searchtxt = mysqli_real_escape_string($conn, $searchtxt); //使用mysqli_real_escape_string()來避免語法錯誤

//  $date
$start_date = $_POST["start_date"]??"";
$end_date = $_POST["end_date"]??"";

// $sql="select * from member";
//  $condition = "";


// 基本 SQL 查詢
$sql = "SELECT 
m.stu_id,
m.name,
GROUP_CONCAT(DISTINCT p.position_name) AS positions,
GROUP_CONCAT(DISTINCT a.activity_name ORDER BY a.activity_id) AS activities,
COUNT(DISTINCT ap.activity_id) AS participated_activities,  -- 計算學員參加的活動次數
(SELECT COUNT(*) FROM activities) AS total_activities        -- 計算總活動數
FROM 
member m
LEFT JOIN 
positions p ON m.stu_id = p.stu_id
LEFT JOIN 
activity_participants ap ON m.stu_id = ap.stu_id
LEFT JOIN 
activities a ON ap.activity_id = a.activity_id
GROUP BY 
            m.stu_id, m.name";

//  $sql="select * from member where name = '$searchtxt'";
//  $condition = $searchtxt ? "where name like '%$searchtxt%' or activities like '%$searchtxt%'":"";
//  $sql="select * from member $condition";
 
if ($searchtxt) {
    $sql .= " HAVING m.name LIKE '%$searchtxt%' OR activities LIKE '%$searchtxt%'";
}

if ($start_date){
  if ($condition){$condition.=" and pdate >= '$start_date'";}
  else{
  $condition .=" where pdate >= '$start_date'";
  }
  if ($end_date){
    $condition .=" and pdate <= '$end_date' ";
  }
  $sql="select * from member $condition";
}
else {

if ($end_date){
  if ($condition){$condition.=" and pdate >= '$end_date'";}
  else{
  $condition .=" where pdate <= '$end_date'";}
  $sql="select * from members $condition";
}
}
if ($order){
  $sql.=" order by $order";
}
// if ($order){
//     $sql.=" order by $order";
//   }
  
  $result = mysqli_query($conn, $sql);

// // 組合SQL並執行查詢
// $sql = "SELECT * FROM member $condition";
// $result = mysqli_query($conn, $sql);
// ?>


<!-- 選擇排序欄位 -->
<br>
<a href="insert.php" class="btn btn-primary position-fixed bottom-0 end-0">+</a>

<form action="成員活躍度追蹤.php" method="post">

  <select name="order" class="form-select" aria-label="選擇排序欄位">

    <option value="" <?=($order=='')?'selected':''?>>選擇排序欄位</option>

    <option value="stu_id" <?=($order=="stu_id")?"selected":""?>>學號</option>

    <option value="name" <?=($order=="name")?"selected":""?>>姓名</option>

    <option value="positions" <?=($order=="positions")?"selected":""?>>擔任幹部</option>

    <option value="activities" <?=($order=="activities")?"selected":""?>>活動</option>



  </select>

  <input placeholder="搜尋姓名及活動" class="form-control" type="text" name="searchtxt" value="<?=$searchtxt?>">

  <div class="row g-3 align-items-center">
<br>
  </div>

  <input class="btn btn-primary" type="submit" value="搜尋">

</form>


<div class="container">

<table class="table table-bordered table-striped">

 <tr>

  <td>學號</td>

  <td>姓名</td>

  <td>擔任幹部</td>

  <td>活動</td>

  <td>參與次數</td>

  <td>活躍度</td>
  
  <th>管理</th>

 </tr>

 <?php

 while($row = mysqli_fetch_assoc($result)) {?>

 <tr>

  <td><?=$row["stu_id"]?></td>

  <td><?=$row["name"]?></td>

  <td><?=$row["positions"]?></td>

  <td><?=$row["activities"]?></td>

  <td><?=$row["participated_activities"]?></td>

  <?php
          // 計算活躍度
          $participated_activities = $row['participated_activities'];
          $total_activities = $row['total_activities'];
          $activity_rate = $total_activities > 0 ? ($participated_activities / $total_activities) * 100 : 0;
?>
  <td><?=$activity_rate?>%</td> <!-- 顯示活躍度百分比 -->

  <td>
  <!-- 編輯幹部資料 -->
  <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPositionModal<?=$row['stu_id']?>">編輯幹部</button>
  
    <!-- 編輯活動連結 --><!-- <a href="edit_activity.php?stu_id=<?=$row['stu_id']?>" class="btn btn-info">編輯活動</a> -->
 <!-- 編輯活動按鈕，觸發 Modal -->
 <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editActivityModal<?=$row['stu_id']?>">編輯活動</button>


 <!-- 編輯幹部 Modal -->
<div class="modal fade" id="editPositionModal<?=$row['stu_id']?>" tabindex="-1" aria-labelledby="editPositionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPositionModalLabel">編輯幹部職位</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="update_position.php" method="POST">
          <input type="hidden" name="stu_id" value="<?=$row['stu_id']?>">
          
          <label for="positions">幹部職位</label>
          <input type="text" name="positions" class="form-control" value="<?=$row['positions']?>" required>

          <button type="submit" class="btn btn-primary mt-3">更新幹部職位</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="editActivityModal<?=$row['stu_id']?>" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">編輯活動</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="edit_activity.php">
                    <input type="hidden" name="stu_id" value="<?=$row['stu_id']?>">

                    <h6>選擇參加的活動</h6>

                    <?php 
                    // 取得所有活動
                    $activities_result = mysqli_query($conn, "SELECT * FROM activities");
                    // 取得學生已參與的活動 ID
                    $student_activities = []; 
                    $student_activities_result = mysqli_query($conn, "SELECT activity_id FROM activity_participants WHERE stu_id = '".$row['stu_id']."'");
                    while ($act_row = mysqli_fetch_assoc($student_activities_result)) {
                        $student_activities[] = $act_row['activity_id'];
                    }
                    
                    // 顯示活動選項
                    while ($activity = mysqli_fetch_assoc($activities_result)): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="activity_ids[]" value="<?=$activity['activity_id']?>" 
                            <?= in_array($activity['activity_id'], $student_activities) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activity_ids[]"><?=$activity['activity_name']?></label>
                        </div>
                    <?php endwhile; ?>

                    <button type="submit" class="btn btn-primary mt-3">更新活動</button>
                </form>
            </div>
        </div>
    </div>
</div>
</td>

 <?php

  }

 ?>

</table>


 <!-- 新增活動按鈕 -->
 <!-- <a href="add_activity.php" class="btn btn-primary position-fixed bottom-0 end-0 m-3">新增活動</a> -->
 <!-- 管理幹部按鈕 -->
 <!-- <a href="manage_positions.php" class="btn btn-secondary position-fixed bottom-0 end-0 m-3" style="bottom: 80px;">管理幹部</a> -->

</div>

<?php

  $conn = null; 

  

}


catch(Exception $e) {

  echo 'Message: ' .$e->getMessage();

}

require_once "footer.php";

?>