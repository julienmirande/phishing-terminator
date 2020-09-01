<?php

  session_start();

?>

<!DOCTYPE html>

<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Report</title>
  <link rel="icon" type="image/png" href="images/logo.png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="skin/report.css">

</head>
<!-- Set tabindex to work around Chromium issue 304532 -->
<body onload="checkSession()" tabindex="1" >
  <?php
  //var_dump($_SESSION);

  if (isset($_POST["reasons"]) && isset($_POST["signs"]) && isset($_POST["url"]) && isset($_POST["message"]))
  {
    try {
      $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
      $bulk = new MongoDB\Driver\BulkWrite;
      $newReport = ['_id' => new MongoDB\BSON\ObjectId,
      'Reasons' => $_POST["reasons"],
      'Signs' => $_POST["signs"],
      'Message' => $_POST["message"],
      'URL' => $_POST["url"],
      'Email' => $_POST["email"],
      'Picture' => $_POST["picture"]
    ];
    $bulk->insert($newReport);
    $result = $manager->executeBulkWrite('PhishingDB.Reports', $bulk);
    //$fs = new MongoGridFS('mongodb://localhost:27017', $screenshot);
    //$fs->put()
    echo '<div class="alert alert-success" role="alert">
    Your report has been successfully sent ! You will be disconnect in 3 seconds ...
    </div>';
    header('Refresh:3; url=logoutUser.php');
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

if ((isset($_POST['email']) && isset($_POST['password'])) || (isset($_SESSION['email']) && isset($_SESSION['password'])) || (isset($_SESSION['email'])) ){
  if (isset($_POST['email']) && isset($_POST['password']))
  {

    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $filter = ['Email' => $_POST['email']];
    $options = [];
    $query = new \MongoDB\Driver\Query($filter, $options);
    $users = $manager->executeQuery("PhishingDB.Users", $query);
    $nbusers = 0;
    foreach ($users as $user) {
      $nbusers = $nbusers +1;

      if ($user->Email == $_POST['email'])
      {
        if (password_verify($_POST['password'],$user->Password) )
        {
          if ( $user->IsValidated == 'true')
          {
            //session_start();
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['password'] = $_POST['password'];
          }
          else {
            header('Location: loginUser.php?error=emailNotVerified&url='.$_POST['url']);
          }


        }
        else {
          header('Location: loginUser.php?error=badPassword&url='.$_POST['url']);
        }
      }
      else {
        header('Location: loginUser.php?error=undifinedUser&url='.$_POST['url']);
      }
    }

    if ( $nbusers == 0)
    {
      header('Location: loginUser.php?error=undifinedUser&url='.$_POST['url']);
    }
  }

  ?>

  <div class="container" style="background: #545454;border-radius: 8px;text-align: center;height:30%">
    <img src="skin/customer-service.png" class="rounded mx-auto d-block" alt="..." width="200px" height="200px" >
    <h2 style="color: floralwhite;padding-bottom: 15px;margin: 0px">Welcome to the report page!</h2>
    <h4 class="titleGeneral" style="color: floralwhite;">You are connected as <?php  if (isset($_SESSION["email"])) echo $_SESSION['email']?> !</h4>
    <h5 class="titleGeneral"><a href="logoutUser.php" style="color: floralwhite">Logout here! </a></h5>
  </div>
  <div class="container" style="background: #f2f2f2;height: 70%">

    <div class="container">
      <div class="row" >
        <div class="col-sm-4" id="containerReason" style="background: white;margin-top: 15px;border: 2px solid grey;border-radius: 8px;">
          <h2 style="margin-top: 20px;margin-left: 20px ">Reasons</h2>
          <label class="containerrb" style="margin-left: 20px">Not a phishing website
            <input type="radio"  class="reason" value="Not a phishing website" name="reason">
            <span class="checkmarkrb"></span>
          </label>
          <br>

          <label class="containerrb" style="margin-left: 20px;margin-bottom: 25px">Phishing not detected
            <input type="radio" class="reason" value="Phishing not detected" name="reason">
            <span class="checkmarkrb"></span>
          </label>


        </div>
        <div class="col-sm-1" style="background: #f2f2f2;margin-top: 15px;">



        </div>
        <div class="col-sm-7" id="containerSigns" style="margin-top: 15px;background: white;border: 2px solid grey;border-radius: 8px;">
          <h2 style="margin-top: 20px;margin-left: 20px ">Which signs?</h2>
          <div class="row" style="margin-left: 20px">
            <div class="col-sm-4"><label class="containercb">Wrong company name
              <input type="checkbox" id="sign1" value="Wrong company name">
              <span class="checkmark"></span>
            </label></div>
            <div class="col-sm-4"> <label class="containercb">Pictures
              <input type="checkbox" id="sign2" value="Pictures">
              <span class="checkmark"></span>
            </label></div>
            <div class="col-sm-4"><label class="containercb">URL
              <input type="checkbox" id="sign3" value="URL">
              <span class="checkmark"></span>
            </label></div>
          </div>
          <div class="row" style="margin-left: 20px">
            <div class="col-sm-4"><label class="containercb">Forwarding
              <input type="checkbox" id="sign4" value="Forwarding">
              <span class="checkmark"></span>
            </label></div>
            <div class="col-sm-4"> <label class="containercb">Suspect mail
              <input type="checkbox" id="sign5" value="Suspect mail">
              <span class="checkmark"></span>
            </label></div>
            <div class="col-sm-4"><label class="containercb">Pop-ups spam
              <input type="checkbox" id="sign6" value="Pop-ups spam">
              <span class="checkmark"></span>
            </label></div>
          </div>
        </div>
        <br>

      </div>
      <div class="row">
        <div class="col-sm-12" style="background: white;margin-top: 15px;border: 2px solid grey;border-radius: 8px;">
          <form style="margin-top: 15px;margin-bottom: 15px">
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="emailInput" value='<?php if (isset($_SESSION["email"])) echo $_SESSION["email"]?>' aria-describedby="emailHelp" readonly>
              <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="exampleFormControlTextarea1">Tell us more about the problem (3000 caracs max)</label>
              <textarea class="form-control" id="textarea" rows="3"></textarea>
            </div>

            <div class="container" style="margin: 0 auto;text-align: center">
              <div align="center" class="g-recaptcha" data-sitekey="6LftoFQUAAAAACFRdtxfS0dRpY-igfH2e3xYiX14"></div>
            </div>


          </form>
        </div>
      </div>

      <div class="row">
        <div class="divScreenshot">
          <h2 class="titleContainer">Screenshot</h2>
          <br>
          <?php
          if(!empty($_POST['url']) || !empty($_SESSION['url'])){


            if(!empty($_POST['url'])){
              $siteURL = $_POST['url'];
            } else if (!empty($_SESSION['url'])){
              $siteURL = $_SESSION['url'];
            }



            if(filter_var($siteURL, FILTER_VALIDATE_URL)){

              $stream_opts = [
                "ssl" => [
                  "verify_peer"=>false,
                  "verify_peer_name"=>false,
                ]
              ];

              //call Google PageSpeed Insights API
              $googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$siteURL&screenshot=true",false, stream_context_create($stream_opts));

              //decode json data
              $googlePagespeedData = json_decode($googlePagespeedData, true);

              //screenshot data
              $screenshot = $googlePagespeedData['screenshot']['data'];
              $screenshot = str_replace(array('_','-'),array('/','+'),$screenshot);


              echo "<input id='url' name='url' type='hidden' value='".$siteURL."'>";
              echo "<img  id='myImg' src=\"data:image/jpeg;base64,".$screenshot."\" alt=".$screenshot." class='imgScreenshot'/>";
              echo "<div id='myModal' class='modal'>";
              echo "<span class='close' >Close &times;</span>";
              echo "<img class='modal-content' id='img01'>";
              echo "<div id='caption'></div>";
              echo "</div>";
            }else{
              echo "Please enter a valid URL.";
            }
          }
          ?>
          <!-- <img id="myImg" src="images/Capture.PNG" alt="..." class="imgScreenshot"> -->

      </div>
    </div>
    <div class="col-sm-12" style="text-align: center;">
      <button type="submit" class="submit" style="background: #2aa1c0;border-color: #2aa1c0;color: white;margin-bottom: 15px;margin-top: 15px" class="btn btn-primary"   >Submit</button>
    </div>

  </div>
</div>
</div>

<?php
echo '</div>';
}
else {
  header('Location: loginUser.php?error=emptyFields&url='.$_POST['url']);
}
?>

<!-- The actual snackbar -->
<div id="snackbar" style="background-color:red">Vous êtes déjà connecté !</div>

<script src="html2canvas.js"></script>
<script src="report.js"></script>

</body>

</html>
