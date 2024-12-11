<?php
// 顯示錯誤訊息（僅用於開發環境）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php'; // 引入資料庫連線

// 確保請求方式為 POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 接收表單資料
    $id = $_POST['id']; // 從隱藏欄位接收 ID
    $payment_status = $_POST['payment_status']; // 繳費狀態
    $admission = $_POST['admission']; // 繳費日期

    // 驗證資料是否完整
    if (empty($id) || ($payment_status != '已繳費' && $payment_status != '未繳費') || empty($admission)) {
        echo json_encode(['success' => false, 'message' => '資料不完整或格式不正確']);
        exit();
    }

    // 驗證日期格式
    $date_regex = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($date_regex, $admission)) {
        echo json_encode(['success' => false, 'message' => '日期格式錯誤']);
        exit();
    }

    // 更新資料庫
    $sql = "UPDATE member SET payment_status = ?, admission = ? WHERE stu_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL 預備語句錯誤：' . $conn->error]);
        exit();
    }

    // 綁定參數
    $stmt->bind_param("sss", $payment_status, $admission, $id);

    // 執行更新
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '資料更新成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '資料庫更新失敗：' . $stmt->error]);
    }

    // 關閉連線
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => '無效的請求方式']);
}
?>