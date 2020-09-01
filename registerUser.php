<?php
session_start();
if (isset($_SESSION['email']) && isset($_SESSION['password']))
{
  header('Location: report.php?statut=connected');
}
?>

<!DOCTYPE html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in</title>
  <link rel="icon" type="image/png" href="images/logo.png" />
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js"></script>
  <script src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <script src="ValidationFormScript.js"></script>
  <link rel="stylesheet" type="text/css" href="skin/login.css">
</head>
<body tabindex="1">

  <div id="div1" class="fa" style="font-size:50px"></div>

  <div class="container">
    <div class="login-container">

      <?php
      if (isset($_GET['error']) && $_GET['error'] == 'alreadyUsed'){
        echo '<div class="alert alert-danger" role="alert">
        This mail is already used ! Try again !
        </div>';
      }
       ?>
      <div id="output"></div>
      <img id="gearing"  src="skin/customer-service.png" class="rounded mx-auto d-block" alt="..." width="200px" height="200px" >
      <h2 style="color: black ;padding-bottom: 15px;margin: 0px">Sign in page for user !</h2>
      <form class="form-horizontal" id="form1" action="loginUser.php" method="POST">
        <fieldset>
          <!-- Text input-->
          <div class="form-group">
              <div class="input-group"> <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                <input id="Email" name="email" type="text" placeholder="Enter Your Email" class="form-control input-md">
              </div>
          </div>

          <!-- Password input-->
          <div class="form-group">
              <div class="input-group"> <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                <input id="password" name="password" type="password" placeholder="Enter Your Password" class="form-control input-md">
              </div>
          </div>

          <!-- Password input-->
          <div class="form-group">
              <div class="input-group"> <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                <input id="password_again" name="password_again" type="password" placeholder="Confirm you password" class="form-control input-md">
              </div>
          </div>

          <!-- Button -->

          <div class="form-group">
            <!-- Button -->
            <div class="col-sm-12 controls">
              <button type="submit" id="Submit" name="register" value="Register" class="btn btn-danger btn-block login"><i class="glyphicon glyphicon-log-in"></i> Sign in</button>
            </div>
          </div>
        </fieldset>
      </form>

    


      <div>
        <h4> Return to login page: <a href="loginUser.php"> Click here ! </a> </h4>
      </div>
    </div>
  </div>

</div>

</body>
</html>
