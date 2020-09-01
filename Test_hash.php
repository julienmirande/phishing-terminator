
<!DOCTYPE html>


<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="skin/popup.css">
    <link href="skin/css/bootstrap.min.css" rel="stylesheet">

    <script src="popup.js"></script>
</head>
<!-- Set tabindex to work around Chromium issue 304532 -->
<body class="nohtml" tabindex="1">
  <?php
  $pass = 'admin';
  $hash = password_hash($pass,PASSWORD_BCRYPT,['cost' => 13]);

  echo $hash;

  ?>
</body>

</html>
