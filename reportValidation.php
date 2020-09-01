<?php
if (!isset($_POST['email']) || !isset($_POST['password']))
{
  session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Validation</title>
  <link rel="icon" type="image/png" href="images/logo.png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="skin/reportValidation.css">
</head>
<!-- Set tabindex to work around Chromium issue 304532 -->
<body onload="checkSession()" tabindex="1" >
  <div class="container">
    <?php
    function fillDatabase() {
      $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
      $bulk = new MongoDB\Driver\BulkWrite;
      for ($i = 1; $i <= 2; $i++) {
        $document = ['_id' => new MongoDB\BSON\ObjectId,
        'Reasons' => "Not a phishing website",
        'Signs' => 'signs'.$i,
        'Message' => 'message'.$i,
        'URL' => 'URL'.$i,
        'Email' => 'email'.$i
      ];
      $bulk->insert($document);
    }

    for ($j = 1; $j <= 2; $j++) {
      $document = ['_id' => new MongoDB\BSON\ObjectId,
      'Reasons' => "Phishing not detected",
      'Signs' => 'signs'.$j,
      'Message' => 'message'.$j,
      'URL' => 'URL'.$j,
      'Email' => 'email'.$i
    ];
    $bulk->insert($document);
  }
  $result = $manager->executeBulkWrite('PhishingDB.Reports', $bulk);
}

function deleteDatabase() {
  $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
  $bulkRemover = new MongoDB\Driver\BulkWrite;
  $bulkRemover->delete([]);
  $result = $manager->executeBulkWrite('PhishingDB.Reports', $bulkRemover);
}

if ((isset($_POST['email']) && isset($_POST['password'])) || (isset($_SESSION['email']) && isset($_SESSION['password']))) {
  if (isset($_POST['email']) && isset($_POST['password']))
  {

    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
    $filter = ['Email' => $_POST['email']];
    $options = [];
    $query = new \MongoDB\Driver\Query($filter, $options);
    $users = $manager->executeQuery("PhishingDB.Admins", $query);
    $nbusers = 0;
    foreach ($users as $user) {
      $nbusers = $nbusers +1;

      if ($user->Email == $_POST['email'])
      {
        if (password_verify($_POST['password'],$user->Password))
        {
          session_start();
          $_SESSION['email'] = $_POST['email'];
          $_SESSION['password'] = $_POST['password'];

        }
        else {
          header('Location: loginAdmin.php?error=badPassword');
        }
      }
      else {
        header('Location: loginAdmin.php?error=undifinedUser');
      }
    }

    if ( $nbusers == 0)
    {
      header('Location: loginAdmin.php?error=undifinedUser');
    }
  }

  try {
    //fillDatabase();
    //deleteDatabase();
      if (isset($_POST['Accepted']) && $_POST['Accepted'] == 'true')
      {
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        $bulkRemover = new MongoDB\Driver\BulkWrite;
        $bulkRemover->delete(['_id' => new MongoDB\BSON\ObjectId($_GET['idReport'])]);
        $result = $manager->executeBulkWrite('PhishingDB.Reports', $bulkRemover);
      }
      else if (isset($_POST['Accepted']) && $_POST['Accepted'] == 'false')
      {
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        $bulkRemover = new MongoDB\Driver\BulkWrite;
        $bulkRemover->delete(['_id' => new MongoDB\BSON\ObjectId($_GET['idReport'])]);
        $result = $manager->executeBulkWrite('PhishingDB.Reports', $bulkRemover);
      }
      // else {
      //   echo "<script type='text/javascript'>alert('Select a report !');</script>";
      // }


  } catch (MongoDB\Driver\Exception\Exception $e) {
    $filename = basename(__FILE__);
    echo "The $filename script has experienced an error.\n";
    echo "It failed with the following exception:\n";
    echo "Exception:", $e->getMessage(), "\n";
    echo "In file:", $e->getFile(), "\n";
    echo "On line:", $e->getLine(), "\n";
  }
  ?>

    <div class="container containerTop" >
      <img id="gearing"  src="images/administrator.png" class="rounded mx-auto d-block imgTop" alt="..." >
      <h2 class="titleGeneral">Welcome to the dashboard !</h2>
      <h4 class="titleGeneral">You are connected as <?php   echo $_SESSION['email']; ?> !</h4>
      <h5 class="titleGeneral"><a href="logoutAdmin.php" style="color: floralwhite">Logout here! </a></h5>
    </div>

    <div class="container containerGeneral">
      <div class="container">
        <div class="row">
          <div class="divContainerReports">
            <div class="row">
              <div class="col-sm-6">
                <h2 class="titleContainer">List of reports:</h2>
              </div>
              <div class="col-sm-6" >
                <div class="dropdown dropdownContainer">
                  <button class="btn btn-default dropdown-toggle"  type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?php
                    if (isset($_GET["selected"]) && $_GET["selected"] == "notPhishing") {
                      echo 'Not a phishing website';
                    }
                    else if (isset($_GET["selected"]) && $_GET["selected"] == "notDetected"){
                      echo 'Phishing not detected';
                    }
                    else {
                      echo 'All';
                    }
                    ?>
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu " aria-labelledby="dropdownMenu1">
                    <li><a href="reportValidation.php?selected=all">All</a></li>
                    <li><a href="reportValidation.php?selected=notPhishing">Not a phishing website</a></li>
                    <li><a href="reportValidation.php?selected=notDetected">Phishing not detected</a></li>
                  </ul>
                </div>
              </div>
            </div>


            <nav class="navDiv">
              <ul>
                <div class="list-group" id="list-tab" role="tablist">
                  <?php
                  try {
                    $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
                    if (isset($_GET["selected"]) && $_GET["selected"] == "notPhishing") {
                      $filter = ['Reasons' => "Not a phishing website"];
                      $options = [];
                      $query = new \MongoDB\Driver\Query($filter, $options);
                      $parameter = "selected=notPhishing";
                    }
                    else if (isset($_GET["selected"]) && $_GET["selected"] == "notDetected"){
                      $filter = ['Reasons' => "Phishing not detected"];
                      $options = [];
                      $query = new \MongoDB\Driver\Query($filter, $options);
                      $parameter = "selected=notDetected";
                    }
                    else {
                      $filter = [];
                      $options = [];
                      $query = new \MongoDB\Driver\Query($filter, $options);
                      $parameter = "selected=all";
                    }
                    $reportsList = $manager->executeQuery("PhishingDB.Reports", $query);
                    $count = 0;
                    foreach ($reportsList as $reports) {
                      $count = $count +1;
                      echo '<a href="reportValidation.php?idReport=' .$reports->_id. '&' .$parameter. '" class="list-group-item clearfix" id="' .$reports->_id. '" >Report n°'.$reports->_id ;
                      echo '<span class="pull-right">';
                      echo '<span class="btn btn-xs btn-default" >';
                      echo '<span class="glyphicon glyphicon-play" aria-hidden="true"></span>';
                      echo '</span>';
                      echo '</span>';
                      echo '</a>';
                    }
                    if ($count == 0){
                      echo '<div class="nothingFound">No reports remaining in database ...</div>';
                    }
                  } catch (MongoDB\Driver\Exception\Exception $e) {
                    $filename = basename(__FILE__);
                    echo "The $filename script has experienced an error.\n";
                    echo "It failed with the following exception:\n";
                    echo "Exception:", $e->getMessage(), "\n";
                    echo "In file:", $e->getFile(), "\n";
                    echo "On line:", $e->getLine(), "\n";
                  }
                  ?>
                </div>
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <?php
      if (isset($_GET["idReport"])  && !isset($_POST["Accepted"]))
      {
        ?>
        <div class="col-sm-6 divContainerInfo" >
          <h2 class="titleContainer">Details</h2>
          <div class="row" style="margin-left: 20px">
            <div class="col-sm-12">
              <div class="panel panel-default">
                <div class="panel-heading">Reason</div>
                <?php
                try {
                  $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
                  $id = new \MongoDB\BSON\ObjectId($_GET["idReport"]);
                  $filter = ['_id' => $id];
                  $options = [];
                  $query = new \MongoDB\Driver\Query($filter, $options);
                  $reportsList = $manager->executeQuery("PhishingDB.Reports", $query);
                  foreach ($reportsList as $reports) {
                    echo '<div class="panel-body">'.$reports->Reasons.'</div>';
                    echo '</div>';
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">Signs</div>';
                    echo '<div class="panel-body">'.$reports->Signs.'</div>';
                    echo '</div>';
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">URL</div>';
                    echo '<div class="panel-body"><a href="'.$reports->URL.'" >'.$reports->URL.'</a></div>';
                    echo '</div>';
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">Message</div>';
                    echo '<div class="panel-body">'.$reports->Message.'</div>';
                    echo '</div>';
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading">Email</div>';
                    echo '<div class="panel-body">'.$reports->Email.'</div>';
                  }
                } catch (MongoDB\Driver\Exception\Exception $e) {
                  $filename = basename(__FILE__);
                  echo "The $filename script has experienced an error.\n";
                  echo "It failed with the following exception:\n";
                  echo "Exception:", $e->getMessage(), "\n";
                  echo "In file:", $e->getFile(), "\n";
                  echo "On line:", $e->getLine(), "\n";
                }
                ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-5 divScreenshot" style="display : inline">
          <div class="container" style="display : inline">
          <h2 class="titleContainer">Screenshot</h2>
          <br>
          <?php
          try {
            $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
            $id = new \MongoDB\BSON\ObjectId($_GET["idReport"]);
            $filter = ['_id' => $id];
            $options = [];
            $query = new \MongoDB\Driver\Query($filter, $options);
            $reportsList = $manager->executeQuery("PhishingDB.Reports", $query);
            foreach ($reportsList as $reports) {
              echo "<img id='myImg' src='data:image/jpeg;base64,".$reports->Picture."' alt='...' class='imgScreenshot'/>";
              echo "<div id='myModal' class='modal'>";
              echo "<span class='close' >Close &times;</span>";
              echo "<img class='modal-content' id='img01'>";
              echo "<div id='caption'></div>";
              echo "</div>";


            }
          } catch (MongoDB\Driver\Exception\Exception $e) {
            $filename = basename(__FILE__);
            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";
            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
          }
          ?>


        <?php
        echo '<form action="reportValidation.php?selected='.$_GET["selected"].'" method="post">'
        ?>
          <button  name="Accepted" value="true" id="acceptButton" class="btn btn-success buttonValidation" >
            <span class="glyphicon glyphicon glyphicon-ok"></span> Accept
          </button>
        </form>
        <?php
        echo '<form action="reportValidation.php?selected='.$_GET["selected"].'" method="post">'
        ?>
          <button  name="Accepted" value="false" id="removeButton" class="btn btn-danger buttonReject" >
            <span class="glyphicon glyphicon glyphicon-remove"></span> Remove
          </button>
        </form>

      </div>

      <?php
    }
    echo '</div>';
  }
  else {
    header('Location: loginAdmin.php?error=emptyFields');
  }
  ?>

  <!-- The actual snackbar -->
  <div id="snackbar" style="background-color:red">Vous êtes déjà connecté !</div>

  <script src="reportValidation.js"></script>
</body>
</html>
