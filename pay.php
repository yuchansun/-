<?php
require_once "header.php";
session_start(); // 啟用 session

// 檢查登入狀態
if (!isset($_SESSION["account"])) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';


$role = $_SESSION['role'] ?? 'U';

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
$paid_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM member WHERE payment_status = 1"));
$unpaid_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM member WHERE payment_status = 0"));
?>

<!-- 查詢表單 -->
<form action="pay.php" method="post">
  <select name="order" class="form-select">
    <option value="" <?= ($order == '') ? 'selected' : '' ?>>選擇排序欄位</option>
    <option value="name" <?= ($order == "name") ? "selected" : "" ?>>姓名</option>
    <option value="stu_id" <?= ($order == "stu_id") ? "selected" : "" ?>>學號</option>
    <option value="contact" <?= ($order == "contact") ? "selected" : "" ?>>電話</option>
    <option value="admission" <?= ($order == "admission") ? "selected" : "" ?>>繳費日期</option>
    <option value="payment_status" <?= ($order == "payment_status") ? "selected" : "" ?>>繳費狀態</option>
  </select>
  <input placeholder="搜尋名稱或學號" class="form-control" type="text" name="searchtxt" value="<?= htmlspecialchars($searchtxt) ?>">
  <div class="row g-3 align-items-center">
    <div class="col-auto">
      <label for="start_date" class="col-form-label">開始日期</label>
    </div>
    <div class="col-auto">
      <input id="start_date" class="form-control" type="date" name="start_date" value="<?= $start_date ?>">
    </div>
    <div class="col-auto">
      <label for="end_date" class="col-form-label">結束日期</label>
    </div>
    <div class="col-auto">
      <input id="end_date" class="form-control" type="date" name="end_date" value="<?= $end_date ?>">
    </div>
  </div>
  
  <input class="btn btn-secondary" type="submit" value="搜尋">
</form>

<!-- 顯示查詢結果 -->
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
        <tr id="row-<?= $row['id'] ?>">
          <td><?= htmlspecialchars($row["name"]) ?></td>
          <td><?= htmlspecialchars($row["stu_id"]) ?></td>
          <td><?= htmlspecialchars($row["contact"]) ?></td>
          <td><?= htmlspecialchars($row["admission"]) ?></td>
          <td><?= htmlspecialchars($row["payment_status"]) ?></td>
          <td>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal" 
                    data-id="<?= $row['id'] ?>" 
                    data-status="<?= $row['payment_status'] ?>"
                    data-admission="<?= $row['admission'] ?>">
              修改
            </button>
           
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
        <h5 class="modal-title" id="editModalLabel">修改繳費狀態</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" method="post">
          <input type="hidden" id="id" name="id">
          <div class="form-group">
            <label for="paidStatus">繳費狀態</label>
            <select id="paidStatus" class="form-select" name="payment_status">
              <option value="1">已繳費</option>
              <option value="0">未繳費</option>
            </select>
          </div>
          <div class="form-group mt-2">
            <label for="admissionDate">繳費日期</label>
            <input type="date" id="admissionDate" class="form-control" name="admission">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">確認修改</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
  var editModal = document.getElementById('editModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; 
    var id = button.getAttribute('data-id');
    var status = button.getAttribute('data-status');
    var admission = button.getAttribute('data-admission');

    var modalId = editModal.querySelector('#id');
    var modalStatus = editModal.querySelector('#paidStatus');
    var modalAdmission = editModal.querySelector('#admissionDate');

    modalId.value = id;
    modalStatus.value = status;
    modalAdmission.value = admission;
  });

  document.getElementById('updateForm').addEventListener('submit', function(event) {
    event.preventDefault(); 

    var formData = new FormData(this);

    fetch('update.pay.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        var row = document.getElementById('row-' + formData.get('id'));
        row.querySelector('.payment-status').innerHTML = (formData.get('payment_status') == 1) ? '已繳費' : '未繳費';
        var modal = bootstrap.Modal.getInstance(editModal);
        modal.hide();
      } else {
        alert("更新失敗：" + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("發生錯誤，請稍後再試");
    });
  });
</script>

<!-- 圖表 -->
<canvas id="feeChart" width="200" height="200" style="max-width: 300px; max-height: 300px;"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // 從 PHP 端接收會費數據
  const paid = <?php echo $paid_count; ?>;
  const unpaid = <?php echo $unpaid_count; ?>;

  // 設置圓餅圖的資料
  const data = {
    labels: ['已繳會費', '未繳會費'],
    datasets: [{
      data: [paid, unpaid],
      backgroundColor: ['#36A2EB', '#FF6384'],
      hoverBackgroundColor: ['#2196F3', '#FF3D56']
    }]
  };

  // 設置圖表選項
  const options = {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      tooltip: {
        callbacks: {
          label: function(tooltipItem) {
            let label = tooltipItem.label || '';
            if (label) {
              label += ': ' + tooltipItem.raw + '人';
            }
            return label;
          }
        }
      },
      datalabels: {
        color: '#fff', // 標籤顏色
        font: {
          weight: 'bold',
          size: 14
        },
        formatter: (value, context) => {
          const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
          const percentage = ((value / total) * 100).toFixed(1);
          return value + '人 (' + percentage + '%)';
        }
      }
    }
  };

  // 渲染圓餅圖
  const ctx = document.getElementById('feeChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: data,
    options: options
  });
</script>

</div>

<?php
mysqli_free_result($result);
mysqli_close($conn);

require_once "footer.php";
?>
