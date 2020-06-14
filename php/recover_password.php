<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] != "" || !isset($_GET['page']))
  header ("location: ../index.php");
require ("../config/database.php");
$page = $_GET['page'];

/* PAGE 1 : authentification --------------------- */

$fake_login = false;
$fake_mail = false;

if (isset($_POST['submit_1']) && $_POST['submit_1'] == "valider")
{
  $code = openssl_random_pseudo_bytes(4, $truc);
  $code = bin2hex($code);
  $login = htmlspecialchars($_POST['login']);
  $mail = htmlspecialchars($_POST['mail']);
  $ret = make_query("SELECT * FROM users WHERE `login` = '$login'", "prepare");
  $ret->execute(array($login));
  $ret = $ret->fetch(PDO::FETCH_ASSOC);
  if (!$ret)
    $fake_login = true;
  else if ($ret['email'] != $mail)
    $fake_mail = true;
  else {
    $to  = $mail;
    $subject = "Récupération du mot de passe";
    $message = '
    <html>
     <head>
     </head>
     <body>
       <p>Bonjour ' . $ret['firstname'] . ',</p><br>
       <p>Vous avez demandé à recevoir un nouveau mot de passe pour votre compte <b>' .$login. '</b>.</p>
       <p>Il vous suffit de <b>copier le code ci-dessous puis de le renseigner sur la page de récupération correspondante.</b></p><br><br>
       <center><b style="font-size: 26px;">' .$code. '</b></center>
     </body>
    </html>
    ';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=utf-8';
    $headers[] = "To: < $mail >";
    $headers[] = "From: Camagru <noreply@localhost>";

    mail($to, $subject, $message, implode("\r\n", $headers));
    $ret = make_query("UPDATE users SET `recover` = '$code' WHERE `email` = '$mail'", "prepare");
    $ret->execute(array($code, $mail));
    header("Location: ".$_SERVER['PHP_SELF']."?page=2&mail=".$mail."&login=".$login);
  }
}

/* PAGE 2 : code confirmation -------------------- */

$fake_code = false;

if ($page == 2) {
  if (((!isset($_GET['mail']) || !isset($_GET['login'])) || $_GET['mail'] == "" || $_GET['login'] == ""))
    header("Location: ".$_SERVER['PHP_SELF']."?page=1");
  if (isset($_GET['mail']) && $_GET['mail'] != "")
  {
      $mail = htmlspecialchars($_GET['mail']);
      $login = htmlspecialchars($_GET['login']);
      $ret = make_query("SELECT * FROM users WHERE `email` = '$mail'", "prepare");
      $ret->execute(array($mail));
      $ret = $ret->fetch(PDO::FETCH_ASSOC);
      if (!$ret || $ret['login'] != $login)
        header("Location: ".$_SERVER['PHP_SELF']."?page=1");
      $code = $ret['recover'];
  }

  if (isset($_POST['submit_2']) && $_POST['submit_2'] == "valider")
  {
    if ($code != htmlspecialchars($_POST['code'])) {
      $fake_code = true;
    }
    else {
      header("Location: ".$_SERVER['PHP_SELF']."?page=3&mail=".$mail."&login=".$login."&code=".$code);
    }
  }
}

/* PAGE 3 : changing password -------------------- */

$confirm = true;

if ($page == 3) {
    if (((!isset($_GET['mail']) || !isset($_GET['login']) || !isset($_GET['code'])) || $_GET['mail'] == "" || $_GET['login'] == "") || $_GET['code'] == "")
      header("Location: ".$_SERVER['PHP_SELF']."?page=1");
    if (isset($_GET['code']) && $_GET['code'] != "")
    {
      $code = htmlspecialchars($_GET['code']);
      $login = htmlspecialchars($_GET['login']);
      $mail = htmlspecialchars($_GET['mail']);
      $ret = make_query("SELECT * FROM users WHERE `recover` = '$code'", "prepare");
      $ret->execute(array($code));
      $ret = $ret->fetch(PDO::FETCH_ASSOC);
      if (!$ret || $ret['login'] != $login || $ret['email'] != $mail)
        header("Location: ".$_SERVER['PHP_SELF']."?page=1");
    }

    if (isset($_POST['submit_3']) && $_POST['submit_3'] == "valider")
    {
      $password = htmlspecialchars($_POST['password']);
      $hash_passwd = hash('whirlpool', $password);
      $hash_passwd = hash('whirlpool', $hash_passwd);
      if (htmlspecialchars($_POST['confirm']) != htmlspecialchars($_POST['password']))
        $confirm = false;
      else {
          $ret = make_query("UPDATE users SET `password` = '$hash_passwd' WHERE `recover` = '$code'", "prepare");
          $ret->execute(array($hash_passwd, $code));
          header("location: ../index.php");
      }
    }
}

?>

<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/recover_password.css">


</head>
<body>

<div class="topnav">
    <div class="logo">
        <a href="../index.php"><img src="../ressources/photo.gru.png" class="img"></a>
    </div>
</div>

<div class="info">Récupération du mot de passe par E-mail</div>
<hr class="top_one">

<?php
if ($page == 1) {
  echo "
<div class=\"container\">
  <div class=\"content\">
    <span>Identifiez le compte concerné :</span>
    <hr class=\"other_one\">
    <form method=\"post\">
      <div class=\"each\">
        <label for=\"login\" class=\"text\">Votre nom de compte*</label><br>
        <input class=\"champ\" type=\"text\" placeholder=\"Votre nom de compte\" name=\"login\" value=\"";if (isset($_POST['login'])) echo $_POST['login']; echo "\" required>";
          if ($fake_login)
            echo "<p class=\"error_msg\">Le nom de compte est incorrect</p>";
    echo "
      </div>
      <hr class=\"other_one\">
      <div class=\"each\">
        <label for=\"mail\" class=\"text\">Votre adresse email*</label><br>
        <input class=\"champ\" type=\"email\" placeholder=\"Votre adresse email\" name=\"mail\" value=\""; if (isset($_POST['mail'])) echo $_POST['mail']; echo "\" required>";
          if ($fake_mail)
            echo "<p class=\"error_msg\">L'adresse email est incorrect</p>";
    echo "
      </div>
      <hr class=\"other_one\">
      <div class=\"terminate\">
        <input type=\"submit\" name=\"submit_1\" value=\"valider\" class=\"button\" required>
      </div>
    </form>
  </div>
</div>";
}

if ($page == 2) {
  echo "
  <div class=\"container\">
    <div class=\"content\">
      <p>Nous venons de vous envoyer un e-mail qui vous permettra de choisir un nouveau mot de passe.</p>
      <p>L'e-mail a été envoyé à : <b>". $mail ."</b><p>
      <form method=\"post\">
        <div class=\"eachs\">
          <label for=\"code\">Le code à 8 caracteres reçu par mail :</label><br>
          <input class=\"champ\" name=\"code\" placeholder=\"Le code à 8 caracteres reçu par mail\" type=\"text\" required>";
          if ($fake_code)
            echo "<p class=\"error_msg\">Le code est incorrect</p>";
          echo "
        </div>
        <div class=\"terminate\">
          <input type=\"submit\" name=\"submit_2\" value=\"valider\" class=\"button\" required>
        </div>
      </form>
    </div>
  </div>
  ";
}

if ($page == 3) {
  echo "
<div class=\"container\">
  <div class=\"content\">
    <span>Entrez votre nouveau mot de passe :</span>
    <hr class=\"other_one\">
    <form method=\"post\">
      <div class=\"each\">
        <label for=\"password\" class=\"text\">Nouveau mot de passe*</label><br>
        <input class=\"champ\" id=\"password\" type=\"password\" placeholder=\"Nouveau mot de passe\" pattern=\"(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}\" name=\"password\" required>
        <div id=\"message\">
            <h3>Le mot de passe doit au moins contenir:</h3>
            <p id=\"letter\" class=\"invalid\">Une <b>miniscule</b></p>
            <p id=\"capital\" class=\"invalid\">Une <b>majuscule</b></p>
            <p id=\"number\" class=\"invalid\">Un <b>nombre</b></p>
            <p id=\"length\" class=\"invalid\">Un minimum de <b>8 caracteres</b></p>
        </div>
      </div>
      <hr class=\"other_one\">
      <div class=\"each\">
        <label for=\"confirm\" class=\"text\">Confirmez le nouveau mot de passe*</label><br>
        <input class=\"champ\" type=\"password\" placeholder=\"Confirmez le nouveau mot de passe\" name=\"confirm\" required>";
          if (!$confirm)
            echo "<p class=\"error_msg\">Les mots de passe ne correspondent pas</p>";
    echo "
      </div>
      <hr class=\"other_one\">
      <div class=\"terminate\">
        <input type=\"submit\" name=\"submit_3\" value=\"valider\" class=\"button\" required>
      </div>
    </form>
  </div>
</div>";
}

?>

<footer>
    <hr>
    <p>© 2020 jmondino 42 student</p>
</footer>

<script src="../js/strength_passwd.js"></script>
</body>
</html>
