<?php
require_once "header.php";
session_start(); // 啟用 session

// 檢查登入狀態
if (!isset($_SESSION["account"])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';




// 接收查詢條件
$start_date = $_POST["start_date"] ?? "";
$end_date = $_POST["end_date"] ?? "";
$searchtxt = $_POST["searchtxt"] ?? "";
$order = $_POST["order"] ?? "";

// 防止 SQL 注入
$searchtxt = mysqli_real_escape_string($conn, $searchtxt);
$order = mysqli_real_escape_string($conn, $order);
$start_date = mysqli_real_escape_string($conn, $start_date);
$end_date = mysqli_real_escape_string($conn, $end_date);

// 處理日期範圍
if ($start_date && $end_date && $end_date < $start_date) {
    $temp_date = $end_date;
    $end_date = $start_date;
    $start_date = $temp_date;
}

// 設置查詢條件
$conditions = [];
if ($searchtxt) {
    $conditions[] = "(name LIKE '%$searchtxt%' OR stu_id LIKE '%$searchtxt%')";
}
if ($start_date && $end_date) {
    $conditions[] = "admission BETWEEN '$start_date' AND '$end_date'";
} elseif ($start_date) {
    $conditions[] = "admission >= '$start_date'";
} elseif ($end_date) {
    $conditions[] = "admission <= '$end_date'";
}
//
$condition_sql = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

// 排序條件
$allowed_columns = ['name', 'stu_id', 'contact', 'admission', 'payment_status'];
$order_sql = ($order && in_array($order, $allowed_columns)) ? "ORDER BY $order" : "";

// 查詢會員資料
$sql = "SELECT * FROM member $condition_sql $order_sql";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("查詢失敗：" . mysqli_error($conn));
}

// 計算已繳費和未繳費的會員數
$paid_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM member WHERE payment_status = '已繳費'"));
$unpaid_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM member WHERE payment_status = '未繳費'"));
?>

<style>
  .search-container {
    display: flex;
    align-items: center; /* 垂直置中 */
    gap: 15px; /* 控制元素間的距離 */
    flex-wrap: wrap; /* 讓內容在較小螢幕時自動換行 */
  }

  .search-container select,
  .search-container input,
  .search-container button {
    height: 40px; /* 統一高度 */
    padding: 5px 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px; /* 圓角效果 */
  }

  .search-container select {
    width: 150px;
  }

  .search-container input[type="text"] {
    flex: 1; /* 讓文字搜尋框自動延展 */
  }

  .search-container input[type="date"] {
    width: 150px; /* 日期框固定寬度 */
  }

  .search-container button {
    width: 100px; /* 按鈕固定寬度 */
    background-color: #6c757d;
    color: white;
    border: none;
    cursor: pointer;
  }

  .search-container button:hover {
    background-color: #5a6268;
  }

 
  .search-container button {
    width: 55px; /* 調小按鈕寬度 */
    height: 40px; /* 調小按鈕高度 */
    font-size: 14px; /* 調小字體大小 */
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 5px; /* 圓角 */
    cursor: pointer;
  }

  .search-container button:hover {
    background-color: #5a6268;
  }
</style>
 
</style>

<!-- 查詢表單 -->
<br>
<div class="container">
<form action="pay.php" method="post" class="mb-4 ">
  <div class="search-container">
    <!-- 下拉選單 -->
    <div class="row g-3">
    <div class="col-md-4">
    <select name="order">
      <option value="" <?= ($order == '') ? 'selected' : '' ?>>選擇排序欄位</option>
      <option value="name" <?= ($order == "name") ? "selected" : "" ?>>姓名</option>
      <option value="stu_id" <?= ($order == "stu_id") ? "selected" : "" ?>>學號</option>
      <option value="contact" <?= ($order == "contact") ? "selected" : "" ?>>電話</option>
      <option value="admission" <?= ($order == "admission") ? "selected" : "" ?>>繳費日期</option>
      <option value="payment_status" <?= ($order == "payment_status") ? "selected" : "" ?>>繳費狀態</option>
    </select>
    </div>
    <!-- 搜尋文字輸入框 -->
    <div class="col-md-1"></div>
  <div class="col-md-7">
  
    <input type="text" placeholder="搜尋名稱或學號" name="searchtxt" value="<?= htmlspecialchars($searchtxt) ?>">
  </div>
  </div>
    <!-- 日期範圍 -->
    <div class="row g-3">
    <div class="col-md-6">
    
   


    <label for="start_date" >開始日期</label>
    <input id="start_date" type="date" name="start_date" value="<?= $start_date ?>">

    <label for="end_date" >結束日期</label>
    <input id="end_date" type="date" name="end_date" value="<?= $end_date ?>">

    <!-- 搜尋按鈕 -->

    <button type="submit">搜尋</button><br><br>
    </form>
  </div>

<br>

<div class="container">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>姓名</th>
        <th>學號</th>
        <th>電話</th>
        <th>繳費日期</th>
        <th>繳費狀態</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr id="row-<?= htmlspecialchars($row['stu_id']) ?>" 
            data-payment-status="<?= htmlspecialchars($row['payment_status']) ?>" 
            data-admission="<?= htmlspecialchars($row['admission']) ?>">
          <td><?= htmlspecialchars($row["name"]) ?></td>
          <td><?= htmlspecialchars($row["stu_id"]) ?></td>
          <td><?= htmlspecialchars($row["contact"]) ?></td>
          <td><?= htmlspecialchars($row["admission"]) ?></td>
          <td><?= htmlspecialchars($row["payment_status"]) ?></td>
          <td>
            <button type="button" 
                    class="btn btn-outline-secondary edit-btn" 
                    data-id="<?= htmlspecialchars($row["stu_id"]) ?>" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editModal">
              修改
            </button>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- 模態框 -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">修改繳費狀態與日期</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" method="post" action="">
          <p id="modalValue"></p>
          <input type="hidden" id="studentId" name="stu_id">
          <div class="form-group">
            <label for="paidStatus">繳費狀態</label>
            <select id="paidStatus" class="form-select" name="payment_status">
              <option value="已繳費">已繳費</option>
              <option value="未繳費">未繳費</option>
            </select>
          </div>
          <div class="form-group mt-2">
            <label for="admissionDate">繳費日期</label>
            <input type="date" id="admissionDate" class="form-control" name="admission">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">確認</button>
      </div>
        </form>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
        const studentId = this.getAttribute('data-id');
        const row = document.getElementById(`row-${studentId}`);
        const paymentStatus = row.getAttribute('data-payment-status');
        const admissionDate = row.getAttribute('data-admission');

        // 設定模態框中的值
        document.getElementById('studentId').value = studentId;
        document.getElementById('paidStatus').value = paymentStatus;
        document.getElementById('admissionDate').value = admissionDate;

        // 更新模態框標題或提示
        const modalValue = document.getElementById('modalValue');
        modalValue.textContent = `正在修改學生 ID：${studentId}`;
        
        // 動態更新表單提交目標 (可選)
        document.getElementById('updateForm').action = `update.pay.php?id=${studentId}`;
    });
});


</script>

<!-- 圖表 -->
<br>
<br>
<canvas id="feeChart" width="200" height="200" style="max-width: 300px; max-height: 300px;display: block; margin: 0 auto;"></canvas>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<?php
mysqli_free_result($result);
mysqli_close($conn);

require_once "footer.php";
?>
