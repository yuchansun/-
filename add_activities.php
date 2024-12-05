<?php
require_once 'db.php';

// 檢查表單是否已提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = $_POST['activity_name'];
    $activity_date = $_POST['activity_date'];
    $students = $_POST['students'];  // 選擇的學生

    // 插入活動資料
    $sql = "INSERT INTO activities (activity_name, activity_date) VALUES ('$activity_name', '$activity_date')";
    if (mysqli_query($conn, $sql)) {
        // 取得插入活動的 ID
        $activity_id = mysqli_insert_id($conn);
        
        // 插入參加者資料
        foreach ($students as $stu_id) {
            $insert_participant = "INSERT INTO activity_participants (activity_id, stu_id) VALUES ('$activity_id', '$stu_id')";
            mysqli_query($conn, $insert_participant);
        }
        
        echo "活動新增成功！";
    } else {
        echo "活動新增失敗：" . mysqli_error($conn);
    }
}
?>
