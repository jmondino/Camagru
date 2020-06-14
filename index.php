<?php
session_start();
require "config/database.php";

$exist = make_query("SELECT * FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name = 'users' LIMIT 1;", "query");
$exist = $exist->fetch(PDO::FETCH_ASSOC);
if (!$exist)
  header("location: config/setup.php");
$fake_login = false;
$fake_passwd = false;
$active = true;

if (isset($_GET['token']))
{
  $token = htmlspecialchars($_GET['token']);
  $ret = make_query("UPDATE users SET active=1 WHERE `token` = '$token'", "prepare");
  $ret->execute(array($token));
}

if (isset($_POST['submit']) && $_POST['submit'] == "se connecter")
{
  $login = htmlspecialchars($_POST['account']);
  $passwd = htmlspecialchars($_POST['passwd']);
  $hash_passwd = hash('whirlpool', $passwd);
  $hash_passwd = hash('whirlpool', $hash_passwd);
  $ret = make_query("SELECT * FROM users WHERE `login` = '$login'", "prepare");
  $ret->execute(array($login));
  $ret = $ret->fetch(PDO::FETCH_ASSOC);
  if (!$ret)
    $fake_login = true;
  else if ($ret["password"] != $hash_passwd)
    $fake_passwd = true;
  else if ($ret['active'] != 1){
    $active = false;
  }
  if (!$fake_passwd && !$fake_login && $active) {
    $_SESSION['id'] = $ret['id'];
  }
}

?>

<html>
<head>

<title>Photogru</title>
<link rel="stylesheet" href="css/signin.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<div id="all2">
  <div id="close2" class="close2">x</div>
  <div id="img_big_container" class="img_big_container">
    <img src="" id="big_img">
  </div>
</div>

<div id="all">
  <div class="login_form">
    <div class="content">
    <div class="header" id="header">
      <div class="title">S'identifier
        <div id="close" class="close">x</div>
      </div>
    </div>
    <form method="post">
    <div class="form">
      <div class="fill">
        <?php
            if ($fake_login)
              echo "<div class=\"error_log\" id=\"error_log\">Le nom de compte est incorrect</div>";
            if ($fake_passwd && !$fake_login)
              echo "<div class=\"error_log\" id=\"error_log\">Le mot de passe est incorrect</div>";
            if (!$active)
              echo "<div class=\"error_log\" id=\"error_log\">Le compte n'est pas activé</div>";
            if ($fake_login || $fake_passwd || !$active) {
              echo "<script>
                document.getElementById('all').style.display = 'block';
                document.getElementById('error_log').style.display = 'block';
                </script>";
            }
           ?>
        <div class="each">
          <label for="account" class="text">Nom de compte</label><br>
          <input type="text" class="champ" placeholder="Nom de compte" name="account" value="<?php if (isset($_POST['account'])) echo $_POST['account']?>" required>
        </div>
        <div class="each">
          <label for="passwd" class="text">Mot de passe</label><br>
          <input type="password" class="champ" placeholder="Mot de passe" name="passwd" required><br><br>
          <a href="php/recover_password.php?page=1" class="forget">Mot de passe oublié ?</a>
        </div>
          <div class="terminate">
            <input class="button" type="submit" name="submit" value="se connecter" required>
          </div>
        </div>
      </div>
    </form>
  </div>
  </div>
</div>

<?php

if (isset($_SESSION['id']) && $_SESSION['id'] != "")
  include ("php/connected_header.php");
else
  include ("php/header.php");

if (isset($_GET['camera']) && $_GET['camera'] == 1)
  include ('php/photo.php');
else
  include ('php/gallery.php');
?>


<script src="js/login.js"></script>

</body>
</html>
