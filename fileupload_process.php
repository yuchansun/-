<?php
$target_dir = "uploads/";



$filename = $target_dir.basename($_FILES["fileupload"]["name"]);

move_uploaded_file($_FILES["fileupload"]["tmp_name"], $filename);
?>