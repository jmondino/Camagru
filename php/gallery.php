<html>
<head>

<link rel="stylesheet" href="css/gallery.css">
</head>
<body>

<div id="must_connected_like" class="must_connected">
  <p class="error_msg">Vous devez être connecté pour pouvoir aimer.</p>
</div>
<div id="must_connected_comment"class="must_connected">
  <p class="error_msg">Vous devez être connecté pour pouvoir commenter.</p>
</div>

<div class="page_container">
  <div class="content_wrap">
<?php

$all_pic = make_query("SELECT * FROM pictures", "query");
$all_pic = $all_pic->fetchAll(PDO::FETCH_ASSOC);
$nb_pic = count($all_pic);
$i = $nb_pic;

if ($nb_pic > 0)
{
  while ($i > 0)
  {
    $pic_id = $all_pic[$i - 1]['id'];
    $pic = make_query("SELECT * FROM pictures WHERE `id` = '$pic_id'", "query");
    $pic = $pic->fetch(PDO::FETCH_ASSOC);
    $userid = $pic['userid'];
    $user = make_query("SELECT * FROM users WHERE `id` = '$userid'", "query");
    $user = $user->fetch(PDO::FETCH_ASSOC);
    $name = $user['firstname'] . " " . $user['lastname'];
    $link = $pic['link'];
    echo "<div class=\"gallery_content\">
            <div class=\"header_content\">
              <p class=\"user\">$name</p>
            </div>
            <div class=\"gallery_img_place\">
              <img src=\"$link\" class=\"gallery_img\" onclick=\"display_big('$link')\">
            </div>";
            $ret = make_query("SELECT COUNT(*) FROM comments WHERE `imgid` = '$pic_id'", "query");
            $ret = $ret->fetch(PDO::FETCH_ASSOC);
            $nb_com = $ret['COUNT(*)'];
            $ret = make_query("SELECT COUNT(*) FROM likes WHERE `pictureid` = '$pic_id'", "query");
            $ret = $ret->fetch(PDO::FETCH_ASSOC);
            $nb_likes = $ret['COUNT(*)'];
            echo "
            <div class=\"count_likes_com\">
              <div class=\"count_likes\">
                <p id=\"nb_like$i\">$nb_likes j'aime</p>
              </div>
              <div class=\"count_coms\">";
              if ($nb_com < 2)
                echo "<p id=\"nb_com$i\" onclick=\"
                if (document.getElementById('comment_place$i').style.display == 'block')
                  document.getElementById('comment_place$i').style.display = 'none';
                else {
                  document.getElementById('comment_place$i').style.display = 'block'
                }
                  \">$nb_com commentaire</p>";
              else
                echo "<p id=\"nb_com$i\" onclick=\"
                if (document.getElementById('comment_place$i').style.display == 'block')
                  document.getElementById('comment_place$i').style.display = 'none';
                else {
                  document.getElementById('comment_place$i').style.display = 'block'
                }
                  \">$nb_com commentaires</p>";
              echo "
              </div>
            </div>
            <div class=\"likes_coms\">
              <div class=\"like_place\">
                <form method=\"post\">
                  <div class=\"tamere\">";
                  if (isset($_SESSION['id']))
                  {
                    $id = $_SESSION['id'];
                    $ret = make_query("SELECT * FROM likes WHERE `userid` = '$id' AND `pictureid` = '$pic_id'", "query");
                    $ret = $ret->fetch(PDO::FETCH_ASSOC);
                    if ($ret)
                    {
                      echo "<i id=\"thumb$i\" class=\"fa fa-thumbs-up blue\"><input id=\"like$i\" name=\"like$i\" type=\"button\" value=\"J'aime\" class=\"like blue\"";
                      if (isset($_SESSION['id']) && $_SESSION['id'] != "")
                       echo "onclick=\"add_like('$i')\"";
                      else
                        echo "onclick=\"show_error(1)\"";
                      echo "></i>";
                     }
                    else
                    {
                      echo "<i id=\"thumb$i\" class=\"fa fa-thumbs-up\"><input id=\"like$i\" name=\"like$i\" type=\"button\" value=\"J'aime\" class=\"like\"";
                      if (isset($_SESSION['id']) && $_SESSION['id'] != "")
                       echo "onclick=\"add_like('$i')\"";
                      else
                        echo "onclick=\"show_error(1)\"";
                      echo "></i>";
                    }
                  }
                  else {
                    echo "<i id=\"thumb$i\" class=\"fa fa-thumbs-up\"><input id=\"like$i\" name=\"like$i\" type=\"button\" value=\"J'aime\" class=\"like\"";
                    if (isset($_SESSION['id']) && $_SESSION['id'] != "")
                     echo "onclick=\"add_like('$i')\"";
                    else
                        echo "onclick=\"show_error(1)\"";
                    echo "></i>";
                  }
                  echo "
                  </div>
                </form>
              </div>
              <div class=\"coms\">
                <i class=\"fa fa-comment\" onclick=\"
                if (document.getElementById('comment_place$i').style.display == 'block')
                  document.getElementById('comment_place$i').style.display = 'none';
                else {
                  document.getElementById('comment_place$i').style.display = 'block'
                }
                  \"> Commenter</i>
              </div>
            </div>
            <div id=\"comment_place$i\" class=\"comment_place\">
                <input id=\"comment$i\" type=\"text\" class=\"comment_post\" placeholder=\"Votre commentaire..\"";
                if (isset($_SESSION['id']) && $_SESSION['id'] != "")
                {
                  $user = make_query("SELECT * FROM users WHERE `id` = '$id'", "query");
                  $user = $user->fetch(PDO::FETCH_ASSOC);
                  $login = $user['firstname'] . " " . $user['lastname'];
                  echo "onchange=\"add_comment('$i', '$login')\"";
                }
                else
                  echo "onchange=\"show_error(2, '$i')\"";
                echo ">";
              $comment_db = make_query("SELECT * FROM comments", "query");
              $comment_db = $comment_db->fetchAll(PDO::FETCH_ASSOC);
              $nb_com = count($comment_db);
              $j = 0;
              if ($nb_com > 0)
              {
                while ($j < $nb_com)
                {
                  $com_id = $comment_db[$j]['id'];
                  $coms = make_query("SELECT * FROM comments WHERE `imgid` = '$pic_id' AND `id` = '$com_id'", "query");
                  $coms = $coms->fetch(PDO::FETCH_ASSOC);
                  $userid = $coms['userid'];
                  $user = make_query("SELECT * FROM users WHERE `id` = '$userid'", "query");
                  $user = $user->fetch(PDO::FETCH_ASSOC);
                  $login = $user['firstname'] . " " . $user['lastname'];
                  $comment = $coms['comm'];
                  if ($comment)
                    echo "
                    <div class=\"Comment_container\">
                      <div id=\"comment_container$i\" class=\"comment\">
                        <span class=\"comment_login\">$login </span><span class=\"comment_text\"> $comment</span>
                      </div>
                    </div>
                    ";
                  $j++;
                }
              }
              echo "
            </div>
          </div>";
          $i--;
    }
}
else {
  echo "<p class=\"nothing\">Aucune publication.</p>";
}

?>

</div>

<footer>
    <hr>
    <p>© 2020 jmondino 42 student</p>
</footer>
</div>


<script src="js/gallery.js"></script>
</body>
</html>
