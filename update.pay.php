<?php
session_start();
require_once 'db.php';  


// 檢查表單資料
if (!isset($_POST['payment_status']) || !isset($_POST['admission']) || empty($_POST['payment_status']) || empty($_POST['admission'])) {
    $_SESSION['error'] = '缺少繳費狀態或繳費日期';
    header("Location: pay.php");
    exit();
}

$payment_status = $_POST['payment_status'];
$admission = $_POST['admission'];
$member = mysqli_real_escape_string($conn, $_POST['member']);  // 防止SQL注入

// 檢查是否已經有該學生的資料
$sql_check = "SELECT * FROM member WHERE payment_status = ? AND admission = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if (!$stmt_check) {
    $_SESSION['error'] = 'SQL Prepare Error: ' . mysqli_error($conn);
    header("Location: pay.php");
    exit();
}

mysqli_stmt_bind_param($stmt_check, 'ss', $payment_status, $admission);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (!$result_check) {
    $_SESSION['error'] = 'SQL Execute Error: ' . mysqli_error($conn);
    header("Location: pay.php");
    exit();
}

if (mysqli_num_rows($result_check) > 0) {
    // 如果有該資料，執行更新
    $sql_update = "UPDATE member SET payment_status = ? WHERE payment_status = ? AND admission = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, 'sss', $member, $payment_status, $admission);
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['message'] = '繳費狀態和繳費日期更新成功';
    } else {
        $_SESSION['error'] = '更新失敗：' . mysqli_error($conn);
    }
} else {
    // 如果沒有該資料，執行插入
    $sql_insert = "INSERT INTO member (payment_status, admission) VALUES (?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, 'ss', $payment_status, $member);
    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['message'] = '繳費狀態和繳費日期新增成功';
    } else {
        $_SESSION['error'] = '新增失敗：' . mysqli_error($conn);
    }
}

// 跳轉回原來的頁面
header("Location: pay.php");  
exit();
?>
