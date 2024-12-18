<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <!-- 引入 Google 字型 -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Noto+Sans+TC:wght@400;700&display=swap" rel="stylesheet">

    <style>
        #feeChart {
            width: 80%;  /* 設定寬度 */
            max-width: 800px;  /* 最大寬度 */
            height: auto;  /* 高度自動調整 */
            margin-bottom: 20px;
        }

        #nav {
            background-color: #e2e2e9; /* 修改背景顏色 */
        }

        .nav-link {
            color: #284777; /* 修改文字顏色 */
            font-weight: bold; /* 設定文字為粗體 */
            font-family: 'Arial', 'Helvetica', sans-serif; /* 使用本地字體 */
        }

        /* 設定圖片大小 */
        .navbar-brand img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
         /* 修改“我的網站”文字顏色 */
         .navbar-brand {
            color: #284777; /* 這裡設定文字顏色為紅色，您可以更改為任何顏色 */
            font-weight: bold; /* 設定文字為粗體 */
        }
    </style>
</head>

<body>

    <nav id="nav" class="navbar navbar-expand-sm navbar-dark">
        <div class="container-fluid">
            <!-- 左邊圖片與文字 -->
            <a class="navbar-brand" href="#">
                <img src="https://upload.wikimedia.org/wikipedia/zh/thumb/d/da/Fu_Jen_Catholic_University_logo.svg/800px-Fu_Jen_Catholic_University_logo.svg.png" alt="Logo"> <!-- 用您的圖片 URL 替代此處 -->
                系學會會員管理系統
            </a>

            <!-- 導覽列的其他項目右對齊 -->
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link" href="index.php">首頁</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pay.php">會費管理</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="成員活躍度追蹤.php">成員活躍度追蹤</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="活動資料.php">活動資料</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">登出</a>
                </li>
            </ul>
        </div>
    </nav>

</body>

</html>
