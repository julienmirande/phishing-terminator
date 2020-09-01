<?php
    session_start();
    $email = $_GET['email'];
    $string = 'report.php?email='.$email;
    header('Location:'.$string);
?>
