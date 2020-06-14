<?php
session_start();
if (isset($_SESSION['id']))
  $id = $_SESSION['id'];
else {
  header("location: ../index.php");
}
require "../config/database.php";

$old_passwd = true;
$confirm = true;
$same_password = false;
$same_login = false;
$same_mail = false;

$ret = make_query("SELECT * FROM users WHERE `id` = '$id'", "query");
$ret = $ret->fetch(PDO::FETCH_ASSOC);
$current_login = $ret['login'];
$notif = $ret['notif'];

if (isset($_POST['submit_password']) && $_POST['submit_password'] == "valider")
{
  $old_passwd = htmlspecialchars($_POST['old_passwd']);
  $hash_old_passwd = hash('whirlpool', $old_passwd);
  $hash_old_passwd = hash('whirlpool', $hash_old_passwd);
  $new_passwd = htmlspecialchars($_POST['new_passwd']);
  $hash_new_passwd = hash('whirlpool', $new_passwd);
  $hash_new_passwd = hash('whirlpool', $hash_new_passwd);
  if ($hash_old_passwd != $ret['password']) {
    $old_passwd = false;
  }
  else if ($new_passwd != htmlspecialchars($_POST['confirm']))
    $confirm = false;
  else if ($hash_new_passwd == $ret['password'])
    $same_password = true;
  else {
    $ret = make_query("UPDATE users SET `password` = '$hash_new_passwd' WHERE `login` = '$current_login'", "prepare");
    $ret->execute(array($hash_new_passwd, $current_login));
    $_SESSION['id'] = "";
    header("location: ../index.php");
  }
}

if (isset($_POST['submit_mail']) && $_POST['submit_mail'] == "valider")
{
  $new_mail = htmlspecialchars($_POST['new_mail']);
  $ret2 = make_query("SELECT * FROM users WHERE `email` = '$new_mail'", "prepare");
  $ret2->execute(array($new_mail));
  $ret2 = $ret2->fetch(PDO::FETCH_ASSOC);
  if ($new_mail == $ret['email'] || $ret2)
    $same_mail = true;
  else {
    $old_mail = $ret['email'];
    $token = openssl_random_pseudo_bytes(20, $truc);
    $token = bin2hex($token);
    $ret = make_query("UPDATE users SET `token` = '$token' WHERE `email` = '$old_mail'", "prepare");
    $ret->execute(array($token, $old_mail));
    $ret = make_query("UPDATE users SET `active` = '0' WHERE `email` = '$old_mail'", "prepare");
    $ret->execute(array($old_mail));
    $ret = make_query("UPDATE users SET `email` = '$new_mail' WHERE `email` = '$old_mail'", "prepare");
    $ret->execute(array($new_mail, $old_mail));
    $to  = $new_mail;
		$subject = "Changement d'adresse mail";
		$message = '
		<html>
		 <head>
		 </head>
		 <body>
			 <h1>Bienvenue sur Photogru ' . $firstname . ' !</h1>
			 <a href="http://localhost:8080/camagru/index.php?token='. $token .'"><p>Cliquez ici pour confirmer votre nouvelle adresse mail !</p></a>
		 </body>
		</html>
		';
	 	$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-Type: text/html; charset=utf-8';
    $headers[] = "To: < $mail >";
		$headers[] = "From: Camagru <noreply@localhost>";

		mail($to, $subject, $message, implode("\r\n", $headers));
    $_SESSION['id'] = "";
    header("Location: ../index.php");
  }
}

if (isset($_POST['submit_login']) && $_POST['submit_login'] != "")
{
  $new_login = htmlspecialchars($_POST['new_login']);
  $ret2 = make_query("SELECT * FROM users WHERE `login` = '$new_login'", "prepare");
  $ret2->execute(array($new_login));
  $ret2 = $ret2->fetch(PDO::FETCH_ASSOC);
  if ($new_login == $ret['login'] || $ret2)
    $same_login = true;
  else {
    $old_login = $ret['login'];
    $ret = make_query("UPDATE users SET `login` = '$new_login' WHERE `login` = '$old_login'", "prepare");
    $ret->execute(array($new_login, $old_login));
    header("Location: ".$_SERVER['PHP_SELF']);
  }
}

if (isset($_POST['submit_delete']) && $_POST['submit_delete'] == "supprimer son compte")
{
  make_query("DELETE FROM comments WHERE `userid` = '$id'", "query");
  make_query("DELETE FROM likes WHERE `userid` = '$id'", "query");
  make_query("DELETE FROM pictures WHERE `userid` = '$id'", "query");
  make_query("DELETE FROM users WHERE `id` = '$id'", "query");
  $_SESSION['id'] = "";
  header("location: ../index.php");
}

if (isset($_POST['yes_notif']) && $_POST['yes_notif'] == "Oui")
  make_query("UPDATE users SET `notif` = '1' WHERE `id` = '$id'", "query");

if (isset($_POST['no_notif']) && $_POST['no_notif'] == "Non")
  make_query("UPDATE users SET `notif` = '0' WHERE `id` = '$id'", "query");

$ret = make_query("SELECT * FROM users WHERE `id` = '$id'", "query");
$ret = $ret->fetch(PDO::FETCH_ASSOC);
$notif = $ret['notif'];
?>

<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../css/personal_account.css">

</head>
<body>

<div class="page_container">
<div class="wrapper">
<div class="topnav">
    <div class="logo">
        <a href="../index.php"><img src="../ressources/photo.gru.png" class="img"></a>
    </div>
</div>

<div class="info">INFORMATIONS</div>
<hr class="top_one">

<div class="first">
  <strong class="text">Nom du compte :</strong>
  <?php echo "<span class=\"show\">".$current_login."</span>";?>
  <i class="fa fa-edit" onclick="show_login_modify()"></i>
</div>

<div class="login_modify" id="login_modify">
  <form method="post">
      <label for="new_login" class="text_modify">Nouveau nom de compte</label><br>
      <input type="text" class="champ" placeholder="Nouveau nom de compte" name="new_login" required>
      <?php
      if ($same_login) {
        echo "<p class=\"error_msg\">Nom de compte non disponible.</p>";
        echo "<script>
            document.getElementById('login_modify').style.display = 'block';
            </script>";
          }
        ?><br>
        <div class="terminate">
            <input class="button" type="submit" name="submit_login" value="valider" required>
        </div>
  </form>
</div>

<hr class="other_one">

<div class="each">
  <strong class="text">Identité :</strong>
  <?php echo "<span class=\"show\">".$ret['lastname']."  ".$ret['firstname']."</span>";?>
</div>

<hr class="other_one">

<div class="each">
  <strong class="text">Date de naissance :</strong>
  <?php echo "<span class=\"show\">".$ret['birth']."</span>";?>
</div>

<hr class="other_one">

<div class="each">
  <strong class="text">Mot de passe :</strong>
  <?php echo "<span class=\"show\">**********</span>";?>
  <i class="fa fa-edit" onclick="show_passwd_modify()"></i>
</div>

  <div class="passwd_modify" id="passwd_modify">
    <form method="post">
      <label for="old_passwd" class="text_modify">Votre mot de passe actuel*</label><br>
      <input type="password" id="old_passwd" class="champ" placeholder="Votre mot de passe actuel" name="old_passwd" required><br>
      <?php
      if (!$old_passwd) {
        echo "<p class=\"error_msg\">Le mot de passe est incorrect</p>";
        echo "<script>
            document.getElementById('passwd_modify').style.display = 'block';
            </script>";
          }
      ?><br>
      <label for="new_passwd" class="text_modify">Nouveau mot de passe*</label><br>
      <input type="password" id="password" class="champ" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"placeholder="Nouveau mot de passe" name="new_passwd" required><br>
      <?php
        if ($same_password) {
        echo "<p class=\"error_msg\">Le nouveau mot de passe ne peut être égal à l'ancien.</p>";
        echo "<script>
            document.getElementById('passwd_modify').style.display = 'block';
            </script>";
      }
      ?>
      <div id="message">
        <h3>Le mot de passe doit au moins contenir :</h3>
        <p id="letter" class="invalid">Une <b>miniscule</b></p>
        <p id="capital" class="invalid">Une <b>majuscule</b></p>
        <p id="number" class="invalid">Un <b>nombre</b></p>
        <p id="length" class="invalid">Un minimum de <b>8 caracteres</b></p>
      </div>
      <label for="confirm" class="text_modify">Confirmer le nouveau mot de passe*</label><br>
      <input type="password" id="confirm_password" class="champ" placeholder="Confirmer le nouveau mot de passe" name="confirm" required>
      <div class="terminate">
          <input class="button" type="submit" name="submit_password" value="valider" required>
      </div>
    </form>
    </div>

        <hr class="other_one">
      <div class="each">
        <strong class="text">E-mail :</strong>
        <?php echo "<span class=\"show\">".$ret['email']."</span>";?>
        <i class="fa fa-edit" onclick="show_mail_modify()"></i>
      </div>

      <div class="mail_modify" id="mail_modify">
        <form method="post">
            <label for="new_mail" class="text_modify">Nouvelle adresse email</label><br>
            <input type="email" class="champ" placeholder="Nouvelle adresse email" name="new_mail" required>
            <?php
            if ($same_mail) {
              echo "<p class=\"error_msg\">email non disponible.</p>";
              echo "<script>
                  document.getElementById('mail_modify').style.display = 'block';
                  </script>";
                }
              ?><br>
              <div class="terminate">
                  <input class="button" type="submit" name="submit_mail" value="valider" required>
              </div>
        </form>
      </div>

      <hr class="other_one">

      <div class="each">
        <form method="post">
          <strong class="text">Notification :</strong>
          <input type="submit" class="yes <?php if($notif == 1) echo "green"; else echo "grey"; ?>" name="yes_notif" value="Oui">
          /
          <input type="submit" class="no <?php if($notif == 0) echo "red"; else echo "grey"; ?>" name="no_notif" value="Non">
        </form>
      </div>

      <hr class="other_one">

      <form method="post">
      <input type="submit" class="delete" name="submit_delete" value="supprimer son compte">
      </form>
</div>
      <footer>
          <hr>
          <p>© 2020 jmondino 42 student</p>
      </footer>
</div>

<script src="../js/confirm_passwd.js"></script>
<script src="../js/strength_passwd.js"></script>
<script src="../js/modify_passwd.js"></script>
</body>
</html>
