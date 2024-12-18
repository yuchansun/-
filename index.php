<?php
ob_start(); // Start output buffering
session_start();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>成員活躍度與繳費紀錄</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- 自訂 CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 12px;
            padding: 20px;
        }
        .timeline {
            position: relative;
        }
        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            left: 20px;
            width: 2px;
            height: 100%;
            background-color: #ddd;
        }
        .timeline-item {
            position: relative;
            padding: 1rem 0;
            padding-left: 50px;
            /* color: #000; */
        }
        .timeline-item::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 14px;
            width: 12px;
            height: 12px;
            background-color: #3e4759;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #3e4759;
        }
        .timeline-time {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .timeline-content {
            font-size: 1rem;
        }
        .timeline-content strong {
            font-weight: bold;
        }
        .timeline-content .text-muted {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.85rem;
            color: #6c757d;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
            text-align: left;
        }
        .chart-text {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .chart-subtext {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .chart-legend {
            display: flex;
            margin-top: 1rem;
        }
        .chart-legend div {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        .chart-legend span {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .legend-paid {
            background-color: #415f91;
        }
        .legend-unpaid {
            background-color: #d6e3ff;
        }
        .donut-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .chart-container {
            width: 120px;
            height: 120px;
        }
        .progress {
            height: 20px;
            background-color: #e9ecef;
        }
        .progress-bar {
            font-size: 0.9rem;
            line-height: 20px;
            width: 0; /* Initial width */
            transition: width 1.5s ease;
            background-color: #284777  !important;
        }
     
        .card-header.bg-primary.bg-upcoming{
        background-color: #415f91 !important; /* 您想要的新顏色 */
        border-radius: 5px;
      
        }
        .card-header.bg-secondary{
        background-color: #284777 !important; /* 您想要的新顏色 */
        border-radius: 5px!important;
        }

        /* .progress-bar.bg-primary {
            background-color: #3e4759;
        } */
        /* .bs-progress-bar-bg{
            background-color: #0a58ca;
        }  */
        .progress-bar.animate {
            animation: fillProgress 1.5s forwards ease-in-out;
        }
        @keyframes fillProgress {
            from {
                width: 0;
            }
            to {
                width: var(--progress-width);
            }
        }
        .table tbody tr {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <?php
    require_once "header.php";

    // 確保使用者已登入
    if (!isset($_SESSION["account"])) {
        header("Location: login.php");
        exit();
    }

    // 資料庫連線
    require_once "db.php";

    $today = date("Y-m-d");

    // 查詢已完成的活動
    $sql_completed = "SELECT * FROM activities WHERE activity_date < '$today' ORDER BY activity_date ASC";
    $sql_upcoming = "SELECT * FROM activities WHERE activity_date >= '$today' ORDER BY activity_date ASC";

    $result_completed = mysqli_query($conn, $sql_completed);
    $result_upcoming = mysqli_query($conn, $sql_upcoming);

    // 繳費數據查詢
    $paid_count = 0;
    $unpaid_count = 0;
    $result_paid = mysqli_query($conn, "SELECT COUNT(*) as count FROM member WHERE payment_status = '已繳費'");
    $result_unpaid = mysqli_query($conn, "SELECT COUNT(*) as count FROM member WHERE payment_status = '未繳費'");
    
    if ($row = mysqli_fetch_assoc($result_paid)) {
        $paid_count = $row['count'];
    }
    if ($row = mysqli_fetch_assoc($result_unpaid)) {
        $unpaid_count = $row['count'];
    }
    ?>


    <div class="container mt-5">
        <div class="row d-flex justify-content-between">
        <!-- 已完成活動 -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h4>已完成活動</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php while ($row = mysqli_fetch_assoc($result_completed)) { ?>
                                <div class="timeline-item">
                                    <div class="timeline-time">
                                        <?= htmlspecialchars($row['activity_date']) ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong><?= htmlspecialchars($row['activity_name']) ?></strong>
                                        <span class="text-muted">已完成</span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
           

            <!-- 即將到來活動 -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-primary bg-upcoming text-white">
                        <h4>即將到來活動</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php while ($row = mysqli_fetch_assoc($result_upcoming)) { ?>
                                <div class="timeline-item">
                                    <div class="timeline-time">
                                        <?= htmlspecialchars($row['activity_date']) ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong><?= htmlspecialchars($row['activity_name']) ?></strong>
                                        <span class="text-muted">即將到來</span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        <!-- 右側 -->
        <div class="col-md-6 mx-auto">

            <!-- 繳費紀錄區塊 -->
            <div class="card d-flex flex-row align-items-center">
                <!-- 左側文字內容 -->
                <div class="flex-grow-1">
                    <h4 class="chart-text">總繳費狀態</h4>
                    <p class="chart-subtext text-success">目前統計</p>
                    <div class="chart-legend">
                        <div><span class="legend-paid"></span>已繳費</div>
                        <div><span class="legend-unpaid"></span>未繳費</div>
                    </div>
                </div>
                <!-- 右側圓餅圖 -->
                <div class="chart-container">
                    <div id="payment-chart"></div>
                </div>
            </div>

            <!-- 保留原本的程式碼 成員活躍度排行榜-->
        <div class="mt-5">
            <div class="col-md-12 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-center">成員活躍度排行榜</h3>
                    </div>
                    <table class="table table-striped card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>成員</th>
                                <th>參與次數</th>
                                <th>活躍度</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 活躍度查詢邏輯
                            try {
                                $sql = "SELECT 
                                            m.name AS member_name, 
                                            COUNT(DISTINCT ap.activity_id) AS participated_activities,
                                            (SELECT COUNT(*) FROM activities) AS total_activities
                                        FROM 
                                            member m
                                        LEFT JOIN 
                                            activity_participants ap ON m.stu_id = ap.stu_id
                                        GROUP BY 
                                            m.stu_id
                                        ORDER BY 
                                            participated_activities DESC
                                        LIMIT 5"; // 限制只顯示前五名

                                $result = mysqli_query($conn, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $participated_activities = $row['participated_activities'];
                                    $total_activities = $row['total_activities'];
                                    $activity_rate = $total_activities > 0 ? ($participated_activities / $total_activities) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['member_name']) ?></td>
                                        <td><?= $participated_activities ?></td>
                                        <td class="w-50">
                                            <div class="progress">
                                                <div class="progress-bar  animate" style="--progress-width: <?= $activity_rate ?>%;">
                                                    <?= number_format($activity_rate, 2) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php
                            } catch (Exception $e) {
                                echo '<tr><td colspan="3" class="text-danger">錯誤: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

        
    </div>

    
    <!-- 初始化 ApexCharts 繪製圓餅圖 -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [<?= $paid_count ?>, <?= $unpaid_count ?>],
                chart: {
                    type: 'donut',
                    height: 150,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                labels: ['已繳費', '未繳費'],
                colors: ['#415f91', '#d6e3ff'],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '12px'
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#payment-chart"), options);
            chart.render();
        });
    </script>
    <!-- Fix for session issues -->
    <?php ob_end_flush(); ?>

    <!-- 引入 jQuery 和 Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
