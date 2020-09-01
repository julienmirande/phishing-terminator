<?php
    if(!isset($_GET['email']) || !isset($_GET['token'])){
        header('Location : registerUser.php?error=emailNotVerified');
        exit();
    }else{
        $email = $_GET['email'];
        $token = $_GET['token'];
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        $filter = ['Email' => $email];
        $options = [];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $users = $manager->executeQuery("PhishingDB.Users", $query);
        $nbusers = 0;
        foreach ($users as $user) {
          $nbusers = $nbusers +1;

          if ($user->Email == $email)
          {
            if($user->Token == $token)
            {
              $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
              $bulk = new MongoDB\Driver\BulkWrite;
              $filter = ['Email' => $email];
              $object = ['_id' => $user->_id,
              'Email' => $user->Email,
              'Password' => $user->Password,
              'IsValidated' => 'true',
              'Token' => $user->Token
            ];
              $options = [];
              $bulk->update($filter,$object,$options);
              $result = $manager->executeBulkWrite('PhishingDB.Users', $bulk);
              $URL = 'loginUser.php?error=emailVerified';
              echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
              echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
            }
          }
        }

    }
