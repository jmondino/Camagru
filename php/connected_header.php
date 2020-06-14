<?php
include "logout.php";

$i = 8;
$id = $_SESSION['id'];

$ret = make_query("SELECT * FROM users WHERE `id` = '$id'", "query");
$ret = $ret->fetch(PDO::FETCH_ASSOC);

$login = $ret['login'];

if (strlen($login) > $i) {
  $login[++$i] = ".";
  $login[++$i] = ".";
  while (++$i < strlen($login))
    $login[$i] = "\0";
}
?>

<html>
<head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/header.css">

</head>
<body>

<div class="topnav" id="myTopnav">
  <img src="ressources/photo.gru.png" class="img">

  <?php
  if (isset($_GET['camera']) && $_GET['camera'] == 1)
    echo "<a href=\"index.php\" class=\"fa fa-image\"></a>";
  else {
    echo "<a href=\"index.php?camera=1\" class=\"fa fa-camera-retro\"></a>";
  }
  ?>

  <div class="user_account" onclick="open_menu()">
    <i class="fa fa-user"></i>
    <a href="javascript:void(0);" class="name_account"><?php echo $login; ?></a>
    </a>
  </div>
</div>

<div class="menu">
  <form method="post">
    <button type="submit" class="log" name="logout">Se deconnecter</button>
  <a href="php/personal_account.php" class="name_account"><div class="log">Mon compte</div></a>
  </form>
</div>

<script src="js/connected.js"></script>
</body>
</html>
