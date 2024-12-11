<?php
// 顯示錯誤
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php'; // 引入資料庫連線

// 確保請求方式為 POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 接收表單資料
    $id = intval($_POST['id']);
    $payment_status = intval($_POST['payment_status']);
    $admission = $_POST['admission'];

    // 驗證資料
    if (empty($id) || ($payment_status !== 0 && $payment_status !== 1) || empty($admission)) {
        echo json_encode(['success' => false, 'message' => '資料不完整或格式不正確']);
        exit();
    }

    var_dump($_POST);
    exit();

    // 檢查日期格式是否正確
    $date_regex = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($date_regex, $admission)) {
        echo json_encode(['success' => false, 'message' => '日期格式錯誤']);
        exit();
    }

    // 更新資料庫
    $sql = "UPDATE member SET payment_status = ?, admission = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL 預備語句錯誤：' . $conn->error]);
        exit();
    }

    $stmt->bind_param("isi", $payment_status, $admission, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => '資料庫更新失敗：' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => '無效的請求方式']);
}
?>

<script>
document.getElementById('updateForm').addEventListener('submit', function (event) {
  event.preventDefault();

  var formData = new FormData(this);

  fetch('update.pay.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // 更新成功邏輯
        alert('更新成功！');
      } else {
        alert('更新失敗：' + data.message);
      }
    })
    .catch(error => {
      console.error('Fetch Error:', error); // 在開發者工具檢查錯誤
      alert('發生錯誤，請稍後再試');
    });
});
</script>