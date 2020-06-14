<?php
require "../config/database.php";
session_start();

$login_used = false;
$mail_used = false;

if (isset($_POST['submit']) && $_POST['submit'] == "Terminer l'inscription")
{
	$birthDate = $_POST['birth_day']."/".$_POST['birth_month']."/".$_POST['birth_year'];
	$birthDate = explode("/", $birthDate);
	$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));

	if ($_POST['birth_day'] < 10)
		$day = "0".$_POST['birth_day'];
	else
		$day = $_POST['birth_day'];
	if ($_POST['birth_month'] < 10)
		$month = "0".$_POST['birth_month'];
	else
		$month = $_POST['birth_month'];
	$birthDate = $day."/".$month."/".$_POST['birth_year'];
	$mail = htmlspecialchars($_POST['mail']);
	$login = htmlspecialchars($_POST['login']);
	$passwd = htmlspecialchars($_POST['passwd']);
	$hash_passwd = hash('whirlpool', $passwd);
	$hash_passwd = hash('whirlpool', $hash_passwd);
	$firstname = htmlspecialchars($_POST['firstname']);
	$lastname = htmlspecialchars($_POST['lastname']);
	$ret = make_query("SELECT * FROM users WHERE `login` = '$login'", "prepare");
	$ret->execute(array($login));
	$ret2 = make_query("SELECT * FROM users WHERE `email` = '$mail'", "prepare");
	$ret2->execute(array($mail));
	$ret = $ret->fetch(PDO::FETCH_ASSOC);
	$ret2 = $ret2->fetch(PDO::FETCH_ASSOC);
	if ($ret)
		$login_used = true;
	if ($ret2)
		$mail_used = true;
	if (!$login_used && !$mail_used)
	{
		$token = openssl_random_pseudo_bytes(20, $truc);
		$token = bin2hex($token);
		$ret = make_query("INSERT INTO users (firstname, lastname, email, password, login, birth, age, token) VALUES ('$firstname', '$lastname', '$mail', '$hash_passwd', '$login', '$birthDate', '$age', '$token')", "prepare");
		$ret->execute(array($firstname, $lastname, $mail, $hash_passwd, $login, $birthDate, $age, $token));
		$to  = $mail;
		$subject = "Bienvenue sur Photogru !";
		$message = '
		<html>
		 <head>
		 </head>
		 <body>
			 <h1>Bienvenue sur Photogru ' . $firstname . ' !</h1>
			 <a href="http://localhost:8080/camagru/index.php?token='. $token .'"><p>Cliquez ici pour activer votre compte !</p></a>
		 </body>
		</html>
		';
	 	$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-Type: text/html; charset=utf-8';
    $headers[] = "To: < $mail >";
		$headers[] = "From: Camagru <noreply@localhost>";

		mail($to, $subject, $message, implode("\r\n", $headers));
		header("location: ../index.php");
		}
}

?>

<html>
<head>
	<title>S'inscrire</title>
	<link rel="stylesheet" href="../css/singup.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="topnav">
    <div class="logo">
        <a href="../index.php"><img src="../ressources/photo.gru.png" class="img"></a>
    </div>
</div>

<img src="../ressources/signup.jpg" class="img2">

<div class="form-sub">
    <div class="content-sub">
        <div class="header">
        <div class="title">Créez votre compte</div>
        </div>
        <div class="row">
            <div class="elements">
                <form action="" method="post">
                        <label for="login" class="text">Nom de compte*</label>
                        <input type="text" id="login" class="champ" placeholder="Nom de compte" name="login" value="<?php if (isset($_POST['login'])) echo $_POST['login']?>" required>
												<p class="error_login" id="error_login">Nom de compte non disponible</p>
												<?php
												if ($login_used) {
													echo "<script>
														document.getElementById(\"error_login\").style.display = \"block\";
														document.getElementById(\"login\").style.backgroundColor = \"#e4442c\";
														document.getElementById(\"login\").style.borderColor = \"#e4442c\";
														document.getElementById(\"login\").style.color = \"white\";
														document.getElementById(\"login\").onkeyup = function() {
															document.getElementById(\"login\").style.backgroundColor = \"white\";
															document.getElementById(\"login\").style.borderColor = \"#c7c3b4\";
															document.getElementById(\"login\").style.color = \"black\";
															document.getElementById(\"error_login\").style.display = \"none\";
														}
													</script>";
												}
												?>
										<div class="each">
                        <label for="passwd" class="text">Mot de passe*</label>
                        <input type="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="champ" placeholder="Mot de passe" name="passwd" required>
											</div>
                    <div id="message">
                        <h3>Le mot de passe doit au moins contenir:</h3>
                        <p id="letter" class="invalid">Une <b>miniscule</b></p>
                        <p id="capital" class="invalid">Une <b>majuscule</b></p>
                        <p id="number" class="invalid">Un <b>nombre</b></p>
                        <p id="length" class="invalid">Un minimum de <b>8 caracteres</b></p>
                    </div>
                    <div class="each">
                        <label for="confirm" class="text">Confirmation du mot de passe*</label>
                        <input type="password" id="confirm_password" class="champ" placeholder="Confirmation du mot de passe" name="confirm" required>
                    </div>
                    <div class="each">
                        <label for="mail" class="text">E-mail*</label>
												<input type="email" id="mail" class="champ mail" placeholder="E-mail" name="mail" value="<?php if (isset($_POST['mail'])) echo $_POST['mail']?>"required>
												<p class="error_mail" id="error_mail">Mail non disponible</p>
												<?php
												if ($mail_used) {
													echo "<script>
														document.getElementById(\"error_mail\").style.display = \"block\";
														document.getElementById(\"mail\").style.backgroundColor = \"#e4442c\";
														document.getElementById(\"mail\").style.borderColor = \"#e4442c\";
														document.getElementById(\"mail\").style.color = \"white\";
														document.getElementById(\"mail\").onkeyup = function() {
															document.getElementById(\"mail\").style.backgroundColor = \"white\";
															document.getElementById(\"mail\").style.borderColor = \"#c7c3b4\";
															document.getElementById(\"mail\").style.color = \"black\";
															document.getElementById(\"error_mail\").style.display = \"none\";
														}
													</script>";
												}
												?>
										</div>
                    <div class="each">
                        <label for="firstname" class="text">Prénom*</label>
                        <input type="text" class="champ" placeholder="Prénom" name="firstname" value="<?php if (isset($_POST['firstname'])) echo $_POST['firstname']?>"required>
                    </div>
                    <div class="each">
                        <label for="lastname" class="text">Nom*</label><br>
                        <input type="text" class="champ" placeholder="Nom" name="lastname" value="<?php if (isset($_POST['lastname'])) echo $_POST['lastname']?>"required>
                    </div>
                    <div class="each">
                        <p class="text">Date de naissance*</p>
                        <div class="day">
                            <select class="select" name="birth_day" required>
                                <option value="">Jour</option>
                            <?php
                               for($i = 1; $i <= 31; ++$i)
                               {
                                   echo "<option value=\"$i\">";
                                   if($i < 10)
                                      echo "0";
                                   echo "$i";
                                   echo "</option>";
                               }
                            ?>
 							</select>
                            <select class="select" name="birth_month" required>
                                <option value="">Mois</option>
                                <option value="1">Janvier</option>
                                <option value="2">Février</option>
                                <option value="3">Mars</option>
                                <option value="4">Avril</option>
                                <option value="5">Mai</option>
                                <option value="6">Juin</option>
                                <option value="7">Juillet</option>
                                <option value="8">Août</option>
                                <option value="9">Septembre</option>
                                <option value="10">Octobre</option>
                                <option value="11">Novembre</option>
                                <option value="12">Décembre</option>
                            </select>
                            <select class="select" name="birth_year" required>
                                <option value="">Année</option>
								<?php
                                    for($i = 2019; $i >= 1900; $i--)
                                    {
                                        echo "<option value=\"$i\">";
                                        echo "$i";
                                        echo "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="terminate">
                        <input class="button" type="submit" name="submit" value="Terminer l'inscription" required>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<footer>
    <hr>
    <p>© 2020 jmondino 42 student</p>
</footer>

<script src="../js/confirm_passwd.js"></script>
<script src="../js/strength_passwd.js"></script>

</body>
</html>
