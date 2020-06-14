<?php
session_start();
require "../config/database.php";


$i = $_POST["pic_id"];
$all_pic = make_query("SELECT * FROM pictures", "query");
$all_pic = $all_pic->fetchAll(PDO::FETCH_ASSOC);
$pic_id = $all_pic[$i - 1]['id'];

if (isset($_SESSION['id']) && $_SESSION['id'] != "")
{
  $com = htmlspecialchars($_POST["comment"]);
  $id = $_SESSION['id'];
  $ret = make_query("INSERT INTO comments (comm, userid, imgid) VALUES ('$com', '$id', '$pic_id')", "prepare");
  $ret->execute(array($com, $id, $pic_id));
  $picture_db = make_query("SELECT * FROM pictures WHERE `id` = '$pic_id'", "query");
  $picture_db = $picture_db->fetch(PDO::FETCH_ASSOC);
  $userid = $picture_db['userid'];
  $user_db_pic = make_query("SELECT * FROM users WHERE `id` = '$userid'", "query");
  $user_db_pic = $user_db_pic->fetch(PDO::FETCH_ASSOC);
  $user_db_curr = make_query("SELECT * FROM users WHERE `id` = '$id'", "query");
  $user_db_curr = $user_db_curr->fetch(PDO::FETCH_ASSOC);
  if ($picture_db['userid'] != $id && $user_db_pic['notif'] == 1)
  {
    $login_curr = $user_db_curr['firstname'] . " " . $user_db_curr['lastname'];
    $publication = $picture_db['link'];
    $img_data = file_get_contents($publication);
    $img_data = base64_encode($img_data);
    $to = $user_db_pic['email'];
    $subject = "Photogru: Notification !";
    $img = "data:image/png;base64," . $img_data;
    $addr = $_SERVER['REMOTE_ADDR'];
    $message = "
      <html>
       <head>
       </head>
       <body>
         <h1>$login_curr a commenté votre publication !</h1>
         <p>$com</p>
       </body>
      </html>
    ";
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/html; charset=utf-8';
    $headers[] = "To: < $to >";
    $headers[] = "From: Camagru <noreply@localhost>";
    mail($to, $subject, $message, implode("\r\n", $headers));
  }
}

?>
