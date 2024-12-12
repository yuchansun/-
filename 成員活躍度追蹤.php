<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>成員活躍度追蹤</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- 內容區域 -->

    <!-- 引入 jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 引入 Bootstrap JavaScript 和 Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <?php

    require_once "header.php";

    session_start();
    if (!isset($_SESSION["account"])){
       header("Location:login.php");
       exit();
    }

    try {
        require_once 'db.php';

        // 初始化變數
        $order = $_POST["order"] ?? "";
        $searchtxt = $_POST["searchtxt"] ?? "";
        $start_date = $_POST["start_date"] ?? "";
        $end_date = $_POST["end_date"] ?? "";
        $activity_filter = $_POST["activity_filter"] ?? "";

        $searchtxt = mysqli_real_escape_string($conn, $searchtxt);

        // 基本 SQL 查詢
        $sql = "SELECT 
                    m.stu_id,
                    m.name,
                    GROUP_CONCAT(DISTINCT p.position_name) AS positions,
                    GROUP_CONCAT(DISTINCT a.activity_name ORDER BY a.activity_id) AS activities,
                    COUNT(DISTINCT ap.activity_id) AS participated_activities,
                    (SELECT COUNT(*) FROM activities) AS total_activities
                FROM 
                    member m
                LEFT JOIN 
                    positions p ON m.stu_id = p.stu_id
                LEFT JOIN 
                    activity_participants ap ON m.stu_id = ap.stu_id
                LEFT JOIN 
                    activities a ON ap.activity_id = a.activity_id
                GROUP BY 
                    m.stu_id, m.name";

        // 添加 HAVING 條件
        $havingConditions = [];
        if ($activity_filter) {
            $havingConditions[] = "FIND_IN_SET('$activity_filter', GROUP_CONCAT(a.activity_id)) > 0";
        }
        if ($searchtxt) {
            $havingConditions[] = "(m.name LIKE '%$searchtxt%' OR activities LIKE '%$searchtxt%' OR positions LIKE '%$searchtxt%')";
        }
        if (!empty($havingConditions)) {
            $sql .= " HAVING " . implode(" AND ", $havingConditions);
        }

        // 添加排序條件
        if ($order) {
            $sql .= " ORDER BY $order";
        }

        $result = mysqli_query($conn, $sql);
    ?>

    <div class="container">
        <!-- 搜尋表單 -->
        <form action="成員活躍度追蹤.php" method="post" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <select name="order" class="form-select" aria-label="排序欄位">
                        <option value="" <?=($order == '') ? 'selected' : ''?>>排序欄位</option>
                        <option value="stu_id" <?=($order == "stu_id") ? 'selected' : ''?>>學號</option>
                        <option value="name" <?=($order == "name") ? 'selected' : ''?>>姓名</option>
                        <option value="participated_activities" <?=($order == "participated_activities") ? 'selected' : ''?>>參與次數</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="activity_filter" class="form-select" id="activity_filter">
                        <option value="">所有活動</option>
                        <?php
                        $activities_query = "SELECT activity_id, activity_name FROM activities";
                        $activities_result = mysqli_query($conn, $activities_query);
                        while ($activity = mysqli_fetch_assoc($activities_result)) {
                            $selected = ($activity_filter == $activity['activity_id']) ? 'selected' : '';
                            echo "<option value=\"{$activity['activity_id']}\" $selected>{$activity['activity_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input placeholder="搜尋姓名、幹部或活動" class="form-control" type="text" name="searchtxt" value="<?=$searchtxt?>">
                </div>
                <div class="col-md-2">
                    <input class="btn btn-secondary" type="submit" value="搜尋">
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addActivityModal">新增一項活動</button>
                </div>
            </div>
        </form>

        <!-- 資料表格 -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>學號</th>
                    <th>姓名</th>
                    <th>擔任幹部</th>
                    <th>活動</th>
                    <th>參與次數</th>
                    <th>活躍度</th>
                    <th>管理</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?=$row["stu_id"]?></td>
                        <td><?=$row["name"]?></td>
                        <td><?=$row["positions"]?></td>
                        <td><?=$row["activities"]?></td>
                        <td><?=$row["participated_activities"]?></td>
                        <td>
                            <?php
                            $participated_activities = $row['participated_activities'];
                            $total_activities = $row['total_activities'];
                            $activity_rate = $total_activities > 0 ? ($participated_activities / $total_activities) * 100 : 0;
                            ?>
                            <?=$activity_rate?>%
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPositionModal<?=$row['stu_id']?>">編輯幹部</button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editActivityModal<?=$row['stu_id']?>">編輯活動</button>
                        </td>
                    </tr>

                    <!-- 編輯幹部 Modal -->
                    <div class="modal fade" id="editPositionModal<?=$row['stu_id']?>" tabindex="-1" aria-labelledby="editPositionModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editPositionModalLabel">編輯幹部職位</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="update_position.php" method="POST">
                                        <input type="hidden" name="stu_id" value="<?=$row['stu_id']?>">
                                        <label for="positions">幹部職位</label>
                                        <input type="text" name="positions" class="form-control" value="<?=$row['positions']?>">
                                        <button type="submit" class="btn btn-primary mt-3">更新幹部職位</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 編輯活動 Modal -->
                    <div class="modal fade" id="editActivityModal<?=$row['stu_id']?>" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editActivityModalLabel">編輯活動</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="edit_activity.php">
                                        <input type="hidden" name="stu_id" value="<?=$row['stu_id']?>">
                                        <h6>選擇參加的活動</h6>
                                        <?php 
                                        $activities_result = mysqli_query($conn, "SELECT * FROM activities");
                                        $student_activities = []; 
                                        $student_activities_result = mysqli_query($conn, "SELECT activity_id FROM activity_participants WHERE stu_id = '".$row['stu_id']."'");
                                        while ($act_row = mysqli_fetch_assoc($student_activities_result)) {
                                            $student_activities[] = $act_row['activity_id'];
                                        }
                                        while ($activity = mysqli_fetch_assoc($activities_result)): ?>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="activity_ids[]" value="<?=$activity['activity_id']?>" 
                                                <?= in_array($activity['activity_id'], $student_activities) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="activity_ids[]"><?=$activity['activity_name']?></label>
                                            </div>
                                        <?php endwhile; ?>
                                        <button type="submit" class="btn btn-primary mt-3">更新活動</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- 新增活動 Modal -->
    <div class="modal fade" id="addActivityModal" tabindex="-1" aria-labelledby="addActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActivityModalLabel">新增活動</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_activity.php" method="POST">
                        <div class="form-group">
                            <label for="activity_name">活動名稱</label>
                            <input type="text" class="form-control" id="activity_name" name="activity_name" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="activity_date">活動日期</label>
                            <input type="date" class="form-control" id="activity_date" name="activity_date" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="students">選擇學生參加活動</label><br>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <?php
                                $sql = "SELECT stu_id, name FROM member";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<div class='form-check'>
                                            <input type='checkbox' class='form-check-input' name='students[]' value='{$row['stu_id']}'>
                                            <label class='form-check-label'>{$row['stu_id']} - {$row['name']}</label>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">新增活動</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
        $conn = null; 
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">錯誤: ' . $e->getMessage() . '</div>';
    }
    require_once "footer.php";
    ?>
</body>
</html>
