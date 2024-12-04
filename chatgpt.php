// 顯示成員的資料和參加的活動次數
<?php
$sql = "SELECT m.stu_id, m.name, COUNT(ap.activity_id) AS participation_count
        FROM members m
        LEFT JOIN activity_participants ap ON m.stu_id = ap.stu_id
        GROUP BY m.stu_id";

$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
    echo "學號: " . $row["stu_id"] . " 姓名: " . $row["name"] . " 參加活動次數: " . $row["participation_count"] . "<br>";
}


// 新增活動
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_activity"])) {
    $activity_name = $_POST["activity_name"];
    $activity_date = $_POST["activity_date"];
    $description = $_POST["description"];
    
    $sql = "INSERT INTO activities (activity_name, activity_date, description) 
            VALUES ('$activity_name', '$activity_date', '$description')";
    if (mysqli_query($conn, $sql)) {
        echo "活動新增成功！";
    } else {
        echo "錯誤：" . mysqli_error($conn);
    }
}


// 新增參加者
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_participant"])) {
    $activity_id = $_POST["activity_id"];
    $stu_id = $_POST["stu_id"];
    
    $sql = "INSERT INTO activity_participants (activity_id, stu_id) 
            VALUES ('$activity_id', '$stu_id')";
    if (mysqli_query($conn, $sql)) {
        echo "成員已成功加入活動！";
    } else {
        echo "錯誤：" . mysqli_error($conn);
    }
}


// 查詢成員參與過的活動
$stu_id = $_GET["stu_id"];
$sql = "SELECT a.activity_name, a.activity_date 
        FROM activities a 
        JOIN activity_participants ap ON a.activity_id = ap.activity_id
        WHERE ap.stu_id = '$stu_id'";

$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo "活動名稱: " . $row["activity_name"] . " 日期: " . $row["activity_date"] . "<br>";
}


// 新增幹部職位紀錄
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_position"])) {
    $stu_id = $_POST["stu_id"];
    $position_name = $_POST["position_name"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    
    $sql = "INSERT INTO positions (stu_id, position_name, start_date, end_date) 
            VALUES ('$stu_id', '$position_name', '$start_date', '$end_date')";
    if (mysqli_query($conn, $sql)) {
        echo "幹部紀錄新增成功！";
    } else {
        echo "錯誤：" . mysqli_error($conn);
    }
}


?>

<form method="POST" action="manage_activities.php">
    <input type="text" name="activity_name" placeholder="活動名稱">
    <input type="date" name="activity_date">
    <textarea name="description" placeholder="活動描述"></textarea>
    <button type="submit" name="add_activity">新增活動</button>
</form>

<form method="POST" action="manage_participants.php">
    <select name="activity_id">
        <!-- 顯示活動列表 -->
        <?php
        $sql = "SELECT * FROM activities";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['activity_id']}'>{$row['activity_name']}</option>";
        }
        ?>
    </select>
    <input type="text" name="stu_id" placeholder="學號">
    <button type="submit" name="add_participant">新增參加者</button>
</form>

