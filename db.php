<?php

$servername = "localhost:3306";

$dbname = "系學會";

$dbUsername = "root";

$dbPassword = "";



$conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
// echo "成功連線到資料庫";


?>

