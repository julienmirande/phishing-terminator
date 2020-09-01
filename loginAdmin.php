<?php
session_start();
if (isset($_SESSION['email']) && isset($_SESSION['password']))
{
  header('Location: reportValidation.php?statut=connected');
}
?>

<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="icon" type="image/png" href="images/logo.png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="skin/login.css">
</head>
<!-- Set tabindex to work around Chromium issue 304532 -->
<body tabindex="1" >

  <div id="div1" class="fa" style="font-size:50px"></div>

  <div class="container">
    <div class="login-container">
      <?php
      if (isset($_POST['register']))
      {
        try {
          $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
          $filter = ['Email' => $_POST['email']];
          $options = [];
          $query = new \MongoDB\Driver\Query($filter, $options);
          $users = $manager->executeQuery("PhishingDB.Admins", $query);
          $nbusers = 0;
          foreach ($users as $user) {
            $nbusers = $nbusers +1;
          }
          if ($nbusers > 0)
          {
            header('Location: registerAdmin.php?error=alreadyUsed');
            exit();
          }
        } catch (MongoDB\Driver\Exception\Exception $e) {
        $filename = basename(__FILE__);
        echo "The $filename script has experienced an error.\n";
        echo "It failed with the following exception:\n";
        echo "Exception:", $e->getMessage(), "\n";
        echo "In file:", $e->getFile(), "\n";
        echo "On line:", $e->getLine(), "\n";
      } catch (MongoDB\Driver\Exception\BulkWriteException $e){
      }

      try {
        $hash = password_hash($_POST['password'],PASSWORD_BCRYPT,['cost' => 13]);
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        $bulk = new MongoDB\Driver\BulkWrite;
        $newUser = ['_id' => new MongoDB\BSON\ObjectId,
        'Email' => $_POST['email'],
        'Password' => $hash
      ];
      $bulk->insert($newUser);
      $result = $manager->executeBulkWrite('PhishingDB.Admins', $bulk);
      echo '<div class="alert alert-success" role="alert">
      You account has been successfully created ! Try again !
      </div>';
    } catch (MongoDB\Driver\Exception\Exception $e) {
      $filename = basename(__FILE__);
      echo "The $filename script has experienced an error.\n";
      echo "It failed with the following exception:\n";
      echo "Exception:", $e->getMessage(), "\n";
      echo "In file:", $e->getFile(), "\n";
      echo "On line:", $e->getLine(), "\n";
    } catch (MongoDB\Driver\Exception\BulkWriteException $e){
      echo '<div class="alert alert-danger" role="alert">
      Error insertion ! Try again !
      </div>';
    }

  }
  else if (isset($_GET['error']) && $_GET['error'] == 'disconnected') {
    echo '<div class="alert alert-success" role="alert">
    You have been successfully disconnected !
    </div>';
  }
  else if (isset($_GET['error']) && $_GET['error'] == 'undifinedUser') {
    echo '<div class="alert alert-danger" role="alert">
    Undifined User ! Try again !
    </div>';
  }
  else if (isset($_GET['error']) && $_GET['error'] == 'badPassword'){
    echo '<div class="alert alert-danger" role="alert">
    Bad password ! Try again !
    </div>';
  }
  else if (isset($_GET['error']) && $_GET['error'] == 'emptyFields'){
    echo '<div class="alert alert-danger" role="alert">
    Empty fields! Try again !
    </div>';
  }


  ?>
  <div id="output"></div>
  <img id="gearing"  src="images/gearing1.png" class="rounded mx-auto d-block" alt="..." width="200px" height="200px" >
  <h2 style="color: black ;padding-bottom: 15px;margin: 0px">Log in page for admin !</h2>
  <div class="form-box">
    <form name="form" id="form" class="form-horizontal" enctype="multipart/form-data" action="reportValidation.php" method="POST">

      <div class="input-group">
        <span class="input-group-addon"><i class="glyphicon">@</i></span>
        <input id="email" type="text" class="form-control" name="email" value="" placeholder="enter your email">
      </div>

      <div class="input-group">
        <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
        <input id="password" type="password" class="form-control" name="password" placeholder="enter your password">
      </div>

      <div class="form-group">
        <!-- Button -->
        <div class="col-sm-12 controls">
          <button type="submit" name="connexion" value="Connexion" class="btn btn-danger btn-block login"><i class="glyphicon glyphicon-log-in"></i> Log in</button>
        </div>
      </div>
    </form>
    <div>
      <h4> Not registered yet ?<a href="registerAdmin.php"> Click here </a> </h4>
    </div>
  </div>
</div>

</div>

<script>
function rotate() {
  var a;
  a = document.getElementById("gearing");
  a.src = "images/gearing1.png";
  setTimeout(function () {
    a.src = "images/gearing1.png";
  }, 500);
  setTimeout(function () {
    a.src = "images/gearing2.png";
  }, 1000);
  setTimeout(function () {
    a.src = "images/gearing3.png";
  }, 1500);
  setTimeout(function () {
    a.src = "images/gearing4.png";
  }, 2000);
  setTimeout(function () {
    a.src = "images/gearing5.png";
  }, 2500);
}
rotate();
setInterval(rotate, 2500);
</script>

</body>



</html>
