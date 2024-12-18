<?php

$target_dir = "uploads/";

$filename = "";

$msg="";

if ($_FILES){

// var_dump($_FILES);

$filename = $target_dir.basename($_FILES["fileToUpload"]["name"]);

// echo $filename;

move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename);

$error = $_FILES["fileToUpload"]["error"];



$phpFileUploadErrors = [

  0 => '上傳成功',

  1 => '檔案大小超過伺服器設定(2MB)!',

  2 => '檔案大小超過瀏覽器設定!',

  3 => '上傳檔案不完整',

  4 => '未上傳檔案',

  6 => '暫存資料夾不存在',

  7 => '無法寫入檔案',

  8 => 'PHP擴充導致檔案無法上傳',

  ];

 

$msg = $phpFileUploadErrors[$error]??"";

}

?>

<!DOCTYPE html>

<html>

<body>



<form action="fileupload.php" method="post" enctype="multipart/form-data">

    選擇圖片:

    <input type="file" name="fileToUpload" id="fileToUpload"><br>

    <input type="submit" value="上傳圖片" name="submit">

</form>

<img src="<?=$filename?>" alt="<?=$filename?>" width="30%">

<?= $msg ?>

</body>

</html>