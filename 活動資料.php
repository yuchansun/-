<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活動資料</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        button {
            padding: 8px 16px;
            background-color: #6C757D;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #45a049;
        }
        .collapse-content {
            margin-top: 10px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .btn.btn-secondary{
            background-color: #6C757D;
  
        }
        
    </style>
</head>
<body>

<?php
require_once "header.php";
session_start();
if (!isset($_SESSION["account"])){
    header("Location: login.php");
    exit();
}
require_once 'db.php';

// 取得所有活動資料，包括地點和描述
$sql = "SELECT activity_id, activity_name, activity_date, activity_location,activity_pic FROM activities";
$result = $conn->query($sql);

// 存放所有活動資料
$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    echo "沒有找到任何活動資料";
}
$conn->close();
?>

<br><br>
  

<div class="container">
  <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>活動名稱</th>
            <th>日期</th>
            <th>詳細資訊</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($events as $event): ?>
        <tr>
            <td><?php echo htmlspecialchars($event['activity_name']); ?></td>
            <td><?php echo htmlspecialchars($event['activity_date']); ?></td>
            <td>
                <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $event['activity_id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $event['activity_id']; ?>">
                    詳細資訊
                </button>
            </td>
            <td>
                <!-- 修改按鈕，觸發 modal -->
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $event['activity_id']; ?>">
                    修改日期
                </button>
            </td>
        </tr>
        <tr class="collapse" id="collapse-<?php echo $event['activity_id']; ?>">
            <td colspan="4" class="collapse-content">
                <p><strong>活動名稱:</strong> <?php echo htmlspecialchars($event['activity_name']); ?></p>
                <p><strong>日期:</strong> <?php echo htmlspecialchars($event['activity_date']); ?></p>
                <p><strong>地點:</strong> <?php echo htmlspecialchars($event['activity_location']); ?></p>
                <p><strong>更多資訊:</strong> <a href="https://www.instagram.com/fjuim/p/C_OGO8kyu2L/?img_index=1">點此查看詳細頁面</a></p>
                <p><strong>活動圖片:</strong></p>

                <?php
                    // 如果活動圖片為 BLOB，將其轉換為 Base64 編碼
                    if (!empty($event['activity_pic'])) {
                        $image_data = $event['activity_pic']; // 這是從資料庫中讀取的 BLOB 資料
                        $base64_image = base64_encode($image_data); // 將 BLOB 轉換為 Base64
                        // 假設圖片是 PNG 格式（根據需要調整為 JPEG 或其他格式）
                        echo '<img src="data:image/jpg;base64,' . $base64_image . '" alt="活動圖片" class="img-fluid">';
                    } else {
                        echo '<p>沒有活動圖片。</p>';
                    }
                ?>
            </td>
        </tr>

        <!-- Modal: 修改日期 -->
        <div class="modal fade" id="editModal-<?php echo $event['activity_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel-<?php echo $event['activity_id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-<?php echo $event['activity_id']; ?>">修改活動日期</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 日期修改表單 -->
                        <form action="update_date.php" method="POST">
                            <div class="mb-3">
                                <label for="activity_date-<?php echo $event['activity_id']; ?>" class="form-label">新的活動日期</label>
                                <input type="date" class="form-control" id="activity_date-<?php echo $event['activity_id']; ?>" name="activity_date" value="<?php echo $event['activity_date']; ?>" required>
                                <input type="hidden" name="activity_id" value="<?php echo $event['activity_id']; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">更新日期</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
    </tbody>
  </table>
</div>


<!-- 引入 jQuery 和 Bootstrap JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
