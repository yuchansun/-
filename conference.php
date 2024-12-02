<?php require_once "header.php"?>

<?php

session_start();
if (!isset($_SESSION["account"])){
   header("Location:login.php");
exit();
}



if ($_POST){

  $program_price = array(0, 150, 100, 60);

  $name = $_POST["name"]??"N/A";



  //new syntax in php 7

  $programlist = $_POST["program"]?? [0];

  $price = 0;

  foreach( $programlist as $program ) {  

    $price += $program_price[$program];

  }

  echo "您要繳交 $price 元 <br/>";

}

// else {

//   header("Location: conference.html");

// }

?>



<form action="conference.php" method="post">

    <div class="row">

      <div class="col-6">
      <?php echo  $_SESSION["account"] ?>
<!-- 
        <div class="form-floating mb-3">

          <input type="text" class="form-control" id="_name" name="name" placeholder="您的姓名" required>

          <label for="_name">Name</label>

         
          

        </div> -->

      </div>

    </div>

    <div class="row">



      <div class="col">

        <div class="form-check">

          <input class="form-check-input" type="checkbox" value="1" name="program[]" id="program_1" checked="checked">

          <label class="form-check-label" for="program_1">

            上午場 ($150)

          </label>

        </div>

      </div>

      <div class="col">

        <div class="form-check">

          <input class="form-check-input" type="checkbox" value="2" name="program[]" id="program_2">

          <label class="form-check-label" for="program_2">

            下午場 ($100)

          </label>

        </div>

      </div>

      <div class="col">

        <div class="form-check">

          <input class="form-check-input" type="checkbox" value="3" name="program[]" id="program_3" checked="checked">

          <label class="form-check-label" for="program_3">

            午餐 ($60)

          </label>

        </div>

      </div>

    </div>

    <input class="btn btn-primary" type="submit" value="Submit" />

  </form>
  <?php require_once "footer.php"?>