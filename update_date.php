<?php
require_once 'db.php';

// 確認活動 ID 和新的日期是否存在
if (isset($_POST['activity_id']) && isset($_POST['activity_date'])) {
    $activity_id = $_POST['activity_id'];
    $new_date = $_POST['activity_date'];

    // 更新活動的日期
    $sql = "UPDATE activities SET activity_date = ? WHERE activity_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_date, $activity_id);

    if ($stmt->execute()) {
        echo "日期更新成功!";
        header("Location: 活動資料.php"); // 重定向回活動資料頁面
    } else {
        echo "更新失敗: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
