<?php
session_start();
require_once 'db.php';  // 確保資料庫連接正確

// 確認是否有傳遞活動名稱和活動日期
if (!isset($_POST['activity_name']) || empty($_POST['activity_name']) || !isset($_POST['activity_date']) || empty($_POST['activity_date'])) {
    $_SESSION['error'] = '缺少活動資料';
    header("Location: 成員活躍度追蹤.php");
    exit();
}

$activity_name = mysqli_real_escape_string($conn, $_POST['activity_name']); // 防止SQL注入
$activity_date = $_POST['activity_date'];  // 活動日期可以直接使用，因為它是從輸入框提交的

// 插入活動資料
$sql_insert_activity = "INSERT INTO activities (activity_name, activity_date) VALUES ('$activity_name', '$activity_date')";
if (mysqli_query($conn, $sql_insert_activity)) {
    // 取得插入活動的 ID
    $activity_id = mysqli_insert_id($conn);

    // 檢查是否有選擇學生參加活動
    if (isset($_POST['students']) && !empty($_POST['students'])) {
        $students = $_POST['students'];  // 選中的學生學號陣列

        // 將選擇的學生參加活動
        foreach ($students as $stu_id) {
            $sql_insert_participant = "INSERT INTO activity_participants (activity_id, stu_id) VALUES ('$activity_id', '$stu_id')";
            if (!mysqli_query($conn, $sql_insert_participant)) {
                echo "學生參加活動資料插入失敗：" . mysqli_error($conn);
            }
        }

        $_SESSION['message'] = '活動新增成功，並成功新增參與學生！';
    } else {
        $_SESSION['error'] = '未選擇任何學生參加活動';
    }
} else {
    $_SESSION['error'] = '活動新增失敗：' . mysqli_error($conn);
}

// 回到原來的頁面
header("Location: 成員活躍度追蹤.php");  
exit();
?>
