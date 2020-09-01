<?php
//use PHPMailer\PHPMailer;
session_start();

if (isset($_SESSION['email']) && isset($_SESSION['password']))
{
  header('Location: report.php?statut=connected');
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
      use PHPMailer\PHPMailer\PHPMailer;
      use PHPMailer\PHPMailer\Exception;

      require 'mailer/src/Exception.php';
      require 'mailer/src/PHPMailer.php';
      require 'mailer/src/SMTP.php';
      if (isset($_POST['register']))
      {
        try {

          $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
          $filter = ['Email' => $_POST['email']];
          $options = [];
          $query = new \MongoDB\Driver\Query($filter, $options);
          $users = $manager->executeQuery("PhishingDB.Users", $query);
          $nbusers = 0;
          foreach ($users as $user) {
            $nbusers = $nbusers +1;
          }
          if ($nbusers > 0)
          {
            header('Location: registerUser.php?error=alreadyUsed');
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
          $email = $_POST['email'];
          $token = md5(uniqid($email, true));
          //require("PHPMailer/PHPMailer.php");
          //  require("PHPMailer/SMTP.php");

          $mail = new PHPMailer;
          $mail->isSMTP();
          //Enable SMTP debugging
          // 0 = off (for production use)
          // 1 = client messages
          // 2 = client and server messages
          //$mail->SMTPDebug = 2;
          //Set the hostname of the mail server
          $mail->Host = 'smtp.gmail.com';
          // use
          // $mail->Host = gethostbyname('smtp.gmail.com');
          $mail->SMTPSecure = 'ssl';

          // if your network does not support SMTP over IPv6
          //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
          $mail->Port = 465;
          //Set the encryption system to use - ssl (deprecated) or tls
          //Whether to use SMTP authentication
          $mail->SMTPAuth = true;
          $mail->SMTPOptions = array(
            'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            )
          );
          //Username to use for SMTP authentication - use full email address for gmail
          $mail->Username = "phishingterminator@gmail.com";
          //Password to use for SMTP authentication
          $mail->Password = "Azertyuiop!";
          //Set who the message is to be sent from
          $mail->setFrom('from@example.com', 'PhishingTerminator');
          //Set an alternative reply-to address
          $mail->addReplyTo('replyto@example.com', 'First Last');
          //Set who the message is to be sent to
          $mail->addAddress($email, $email);
          //Set the subject line
          $mail->Subject = 'Please verify your email';
          //Read an HTML message body from an external file, convert referenced images to embedded,
          //convert HTML into a basic plain-text alternative body
          $mail->msgHTML('Click here to verify your email: <a href="http://localhost:8000/confirm.php?email='.$email.'&token='.$token.'"> Click here ! </a>');

          //Replace the plain text body with one created manually
          $mail->AltBody = 'This is a plain-text message body';

          //send the message, check for errors
          if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
          } else {
            //echo "Message sent!";
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
          }

          $hash = password_hash($_POST['password'],PASSWORD_BCRYPT,['cost' => 13]);
          $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
          $bulk = new MongoDB\Driver\BulkWrite;
          $newUser = ['_id' => new MongoDB\BSON\ObjectId,
          'Email' => $_POST['email'],
          'Password' => $hash,
          'IsValidated' => 'false',
          'Token' => $token
        ];
        $bulk->insert($newUser);
        $result = $manager->executeBulkWrite('PhishingDB.Users', $bulk);
        echo '<div class="alert alert-success" role="alert">
        Your account has been successfully created ! Please check your emails to verify your account !
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
    else if (isset($_GET['error']) && $_GET['error'] == 'emailNotVerified'){
      echo '<div class="alert alert-danger" role="alert">
      Please verify your email before! Try again !
      </div>';
    }
    else if (isset($_GET['error']) && $_GET['error'] == 'emailVerified'){
      echo '<div class="alert alert-success" role="alert">
      Your email has been successfully verified !
      </div>';
    }

    ?>
    <div id="output"></div>
    <img id="gearing"  src="skin/customer-service.png" class="rounded mx-auto d-block" alt="..." width="200px" height="200px" >
    <h2 style="color: black ;padding-bottom: 15px;margin: 0px">Log in page for users !</h2>
    <div class="form-box">
      <form name="form" id="form" class="form-horizontal" enctype="multipart/form-data" action="report.php" method="POST">

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
      <?php
      require_once 'google-api-php-client-2.2.1/vendor/autoload.php';

      $client = new Google_Client();
      const CLIENT_ID = '210947497498-kdvj7td2ep3ij0anrma1b6gm755kb98t.apps.googleusercontent.com';
      const CLIENT_SECRET = '05ZcMKYsyMhydfClXukVvLcw';
      const REDIRECT_URI = "http://localhost:8000/loginUser.php";

      /*
       * INITIALIZATION
       *
       * Create a google client object
       * set the id,secret and redirect uri
       * set the scope variables if required
       * create google plus object
       */
      $client = new Google_Client();
      $client->setClientId(CLIENT_ID);
      $client->setClientSecret(CLIENT_SECRET);
      $client->setRedirectUri(REDIRECT_URI);
      $client->setScopes('email');
      $plus = new Google_Service_Plus($client);
      /*
       * PROCESS
       *
       * A. Pre-check for logout
       * B. Authentication and Access token
       * C. Retrive Data
       */
      /*
       * A. PRE-CHECK FOR LOGOUT
       *
       * Unset the session variable in order to logout if already logged in
       */
      if (isset($_REQUEST['logout'])) {
         session_unset();
      }
      /*
       * B. AUTHORIZATION AND ACCESS TOKEN
       *
       * If the request is a return url from the google server then
       *  1. authenticate code
       *  2. get the access token and store in session
       *  3. redirect to same url to eleminate the url varaibles sent by google
       */
      if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $client->getAccessToken();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
      }
      /*
       * C. RETRIVE DATA
       *
       * If access token if available in session
       * load it to the client object and access the required profile data
       */
      if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        $client->setAccessToken($_SESSION['access_token']);
        $me = $plus->people->get('me');
        // Get User data
        $id = $me['id'];
        $name =  $me['displayName'];
        $email =  $me['emails'][0]['value'];
        $profile_image_url = $me['image']['url'];
        $cover_image_url = $me['cover']['coverPhoto']['url'];
        $profile_url = $me['url'];
        $_SESSION['email'] = $email;


      } else {
        // get the login url
        $authUrl = $client->createAuthUrl();
      }
      ?>

      <!-- HTML CODE with Embeded PHP-->
      <div>
          <?php
          /*
           * If login url is there then display login button
           * else print the retieved data
          */
          if (isset($authUrl)) {
              echo "<a class='login' href='" . $authUrl . "'><img src='https://i.stack.imgur.com/XzoRm.png' height='50px'/></a>";
          } else {
              $email = $email;
              echo "ID: {$id} <br>";
              echo "Name: {$name} <br>";
              echo "Email: {$email } <br>";
              echo "Image : {$profile_image_url} <br>";
              echo "Cover  :{$cover_image_url} <br>";
              echo "Url: {$profile_url} <br><br>";
              echo "<a class='logout' href='?logout'><button>Logout</button></a>";
              header('Location: report.php');
          }
          ?>
      </div>
      <div>
        <h4> Not registered yet ?<a href="registerUser.php"> Click here </a> </h4>
      </div>
    </div>
  </div>

</div>
</body>
</html>
