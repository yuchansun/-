<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stu_id = $_POST['stu_id'] ?? '';  // 用 ?? 來確保如果沒傳遞會給空字串

    if (empty($stu_id)) {
        // 如果 stu_id 沒有被傳遞，顯示錯誤訊息並終止執行
        echo "Message: 學生ID無效";
        exit();  // 停止執行
    }

    // 這裡繼續處理更新活動的邏輯 ...
    
    // 取得選擇的活動
    $selected_activity_ids = $_POST['activity_ids'] ?? [];

    // 刪除該學生的所有活動參與紀錄
    mysqli_query($conn, "DELETE FROM activity_participants WHERE stu_id = '$stu_id'");

    // 插入新的活動參與資料
    foreach ($selected_activity_ids as $activity_id) {
        mysqli_query($conn, "INSERT INTO activity_participants (stu_id, activity_id) VALUES ('$stu_id', '$activity_id')");
    }

    // 重定向回成員列表頁面
    header("Location: 成員活躍度追蹤.php");
    exit();
}
?>
