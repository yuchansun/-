<?php
require_once 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 取得資料
    $admission = $_POST['admission'];
    $payment_status = $_POST['payment_status'];

    // 防止 SQL 注入
    $admission = mysqli_real_escape_string($conn, $admission);
    $payment_status = mysqli_real_escape_string($conn, $payment_status);

    // 更新資料庫
    $sql = "UPDATE member SET admission,payment_status = ? WHERE postid = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $payment_status, $admission);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $response = ['success' => true, 'message' => '繳費狀態更新成功'];
        } else {
            $response = ['success' => false, 'message' => '資料庫更新失敗'];
        }
    } else {
        $response = ['success' => false, 'message' => 'SQL 錯誤，請檢查語法'];
    }

    mysqli_close($conn);
}

// 輸出回應 JSON
header('Content-Type: application/json');
echo json_encode($response);
?>



