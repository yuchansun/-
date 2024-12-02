<?php require_once "header.php"?>
<?php

session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}


$membershipFee=2000;

$program_price = array(

  array(300, 150, 5500),//非會員

  array(0, 0, 3000) //會員

);

$price = 0;

//檢查是否取得POST內容

if ($_POST){ //如果POST有內容，進行以下的登入檢查

  $membership = $_POST["membershipFee"]??1;

  $programs = $_POST["program"]??[];

  //計算費用

  $price += $membership*$membershipFee;

  foreach( $programs as $program ) {  

    $price += $program_price[$membership][$program];

  }

}

?>

  <form action="fee.php" method="post">

    <div>

      會費:

      <input type="radio" name="membershipFee" value=1 /> 繳交

      <input type="radio" name="membershipFee" value=0 /> 不繳交

    </div>

    <div>

      活動:

      <input type="checkbox" name="program[]" value=0 /> 一日資管營

      <input type="checkbox" name="program[]" value=1 /> 迎新茶會

      <input type="checkbox" name="program[]" value=2 /> 迎新宿營

    </div>

    <input type="submit" value="確定" />



  </form>



  費用:<?=$price?>

  <a href="fee.php"><button>重新計算</button></a>

  <?php require_once "footer.php"?>