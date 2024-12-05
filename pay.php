<?php

require_once "header.php";
session_start(); // 啟用 session

if (!isset($_SESSION["account"])) { 
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'U';

$start_date = $_POST["start_date"] ?? ""; 
$end_date = $_POST["end_date"] ?? "";
$searchtxt = $_POST["searchtxt"] ?? ""; 
$order = $_POST["order"] ?? ""; 

try {
    require_once 'db.php';

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
    $allowed_columns = ['name', 'stu_id', 'contact', 'admission'];
    $order_sql = ($order && in_array($order, $allowed_columns)) ? "ORDER BY $order" : "";

    // 查詢會員資料
    $sql = "SELECT * FROM member $condition_sql $order_sql";
    $result = mysqli_query($conn, $sql);

    $current_date = date('Y-m-d');

    // 查詢會費資料表
    $paid_count = 0;
    $unpaid_count = 0;
    $sql_fee = "SELECT * FROM fee";

    $result_fee = mysqli_query($conn, $sql_fee);

    while ($row_fee = mysqli_fetch_assoc($result_fee)) {
        // 判斷繳費狀況
        if ($row_fee['admission'] <= $current_date) {
            $paid_count++;
        } else {
            $unpaid_count++;
        }
    }

    mysqli_free_result($result_fee);

} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}

?>

<!-- 查詢表單 -->
<form action="pay.php" method="post">
  <select name="order" class="form-select">
    <option value="" <?= ($order == '') ? 'selected' : '' ?>>選擇排序欄位</option>
    <option value="name" <?= ($order == "name") ? "selected" : "" ?>>姓名</option>
    <option value="stu_id" <?= ($order == "stu_id") ? "selected" : "" ?>>學號</option>
    <option value="contact" <?= ($order == "contact") ? "selected" : "" ?>>電話</option>
    <option value="admission" <?= ($order == "admission") ? "selected" : "" ?>>繳費日期</option>
  </select>
  <input placeholder="搜尋廠商及內容" class="form-control" type="text" name="searchtxt" value="<?= htmlspecialchars($searchtxt) ?>">
  <div class="row g-3 align-items-center">
    <div class="col-auto">
      <label for="start_date" class="col-form-label">已繳費（此日期之後都是已繳費）</label>
    </div>
    <div class="col-auto">
      <input id="start_date" class="form-control" type="date" name="start_date" value="<?= $start_date ?>">
    </div>
    <div class="col-auto">
      <label for="end_date" class="col-form-label">未繳費（此日期之前都是未繳費）</label>
    </div>
    <div class="col-auto">
      <input id="end_date" class="form-control" type="date" name="end_date" value="<?= $end_date ?>">
    </div>
  </div>
  <input class="btn btn-primary" type="submit" value="搜尋">
</form>

<!-- 顯示查詢結果 -->
<div class="container">
  <table class="table table-bordered table-striped">
    <tr>
      <td>姓名</td>
      <td>學號</td>
      <td>電話</td>
      <td>繳費日期</td>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row["name"] ?></td>
        <td><?= $row["stu_id"] ?></td>
        <td><?= $row["contact"] ?></td>
        <td><?= $row["admission"] ?></td>
      </tr>
    <?php } ?>
  </table>

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
