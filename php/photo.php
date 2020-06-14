<?php

global $dossier;
global $fichier;
if (isset($_SESSION['id']) && $_SESSION['id'] != "")
  $id = $_SESSION['id'];
else {
  header("location: /camagru/index.php");
}
$dossier = "ressources/tmp/";
$name = "img_tmp".$id.".png";
$fichier = basename($name);

if (isset($_POST['submit_photo']) && $_POST['submit_photo'] == "Envoyer")
{
  $file = $_FILES["import_img"];
  $legalExtensions = array("jpg", "png");
  $legalSize = "1000000"; // 1 mo
  $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
  $size = $file['size'];
  if (in_array($extension, $legalExtensions)) {
    if ($size <= $legalSize) {
      if (file_exists($dossier.$fichier))
        unlink($dossier.$fichier);
      if (move_uploaded_file($_FILES['import_img']['tmp_name'], $dossier . $fichier))
        echo "";
    }
    else
      echo "Image trop lourde";
  }
  else
    echo "Extension non supportée";
}

if (isset($_POST['shoot']) && $_POST['shoot'] == "Enregistrer")
{
  $filtre = $_POST['filtre'];
  if (!file_exists($dossier . $fichier))
    echo "";
  else if (isset($_SESSION['id']) && $_SESSION['id'] != "")
  {
    if ($filtre == "aucun")
    {
      $name = openssl_random_pseudo_bytes(20, $truc);
      $name = bin2hex($name);
      $gallery = "ressources/gallery/";
      $link = $gallery . $name . ".png";
      if (rename($dossier . $fichier, $link))
      {
        if (file_exists($dossier.$fichier))
          unlink($dossier.$fichier);
        make_query("INSERT INTO pictures (link, userid) VALUES ('$link', '$id')", "query");
        echo "<script>
          document.getElementById(\"success\").style.display = \"flex\";
          $(\"#success\").fadeOut(5000);
        </script>";
      }
    }
    else {
      $name = openssl_random_pseudo_bytes(20, $truc);
      $name = bin2hex($name);
      $gallery = "ressources/gallery/";
      $link = $gallery . $name . ".png";

      list($width_src, $height_src, $type) = getimagesize($dossier . $fichier);
      list($width_f, $height_f) = getimagesize("ressources/filtres/" . $filtre . ".png");

      if ($type == 2)
        $source = imagecreatefromjpeg($dossier . $fichier);
      else if ($type == 3)
        $source = imagecreatefrompng($dossier . $fichier);
      else
      {
        echo "image non supporté";
        return ;
      }
      $filtre = imagecreatefrompng("ressources/filtres/" . $filtre . ".png");
      $filtre_resize = imagecreatetruecolor($width_src, $height_src);
      imagealphablending($filtre_resize, false);
      imagesavealpha($filtre_resize, true);

      imagecopyresampled($filtre_resize, $filtre, 0, 0, 0, 0, $width_src, $height_src, $width_f, $height_f);
      imagecopyresampled($source, $filtre_resize, 0, 0, 0, 0, $width_src, $height_src, $width_src, $height_src);
      imagepng($source, $link);

      if (file_exists($dossier.$fichier))
        unlink($dossier.$fichier);
      make_query("INSERT INTO pictures (link, userid) VALUES ('$link', '$id')", "query");
      echo "<script>
        document.getElementById(\"success\").style.display = \"flex\";
        $(\"#success\").fadeOut(5000);
      </script>";
    }
  }
}

?>

<html>
<head>

<link rel="stylesheet" href="css/photo.css">

</head>
<body onresize="set_width()">

<div id="success" class="success">
  <p>Image enregistrée avec succès</p>
</div>

<div class="all_page">
  <div class="wrapper">
  <div id="filtres">
    <form method="post" enctype="multipart/form-data">
      <p>
        <input type="radio" name="filtre" value="noel" onclick="send_data('noel')">
        <label for="noel">Noël</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="halloween" onclick="send_data('halloween')">
        <label for="halloween">Halloween</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="ocean" onclick="send_data('ocean')">
        <label for="ocean">Océan</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="fumee" onclick="send_data('fumee')">
        <label for="fumee">Fumée</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="plage" onclick="send_data('plage')">
        <label for="plage">Plage</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="trou_noir" onclick="send_data('trou_noir')">
        <label for="trou_noir">Espace</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="forest" onclick="send_data('forest')">
        <label for="forest">Forêt</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="feu" onclick="send_data('feu')">
        <label for="feu">Feu</label>
      </p>
      <p>
        <input type="radio" name="filtre" value="aucun" onclick="send_data('aucun')" checked>
        <label for="aucun">Aucun</label>
      </p>
  </div>

    <div class="img_place_photo">
      <img id="img_photo" class="img_photo"
      src="
      <?php
      if(file_exists($dossier.$fichier))
        echo $dossier.$fichier;
      ?>
      ">
      <img id="filtre_photo" class="filtre_photo" src="">
    </div>
    <div class="pick">
      <input type="file" accept="image/*" id="import_img" name="import_img">
      <label class="label_import" for="import_img">Choisir un fichier</label>
      <input type="submit" name="submit_photo" class="submit_photo" value="Envoyer">
    </div>
      <input type="submit" name="shoot" class="shoot" value="Enregistrer">
      </form>
  <br>
  <br>
  <br>
  <br>
  <div id="webcam">
    <div class="video_section">
      <video id="video"></video>
      <button id="startbutton">Prendre une photo</button>
    </div>
    <canvas id="canvas" style="display: none;"></canvas>
  </div>

    <p>Anciens montages :</p>
    <div class="old_montage">
      <?php
        $pictures = make_query("SELECT link FROM pictures WHERE `userid` = '$id' ORDER BY id DESC", "query");
        $pictures = $pictures->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pictures as $key => $value) {
          echo "<img class=\"old_pic\" src=\"" . $value['link'] . "\">";
        }
      ?>
    </div>
  </div>

<footer>
    <hr>
    <p>© 2020 jmondino 42 student</p>
</footer>

</div>

<script src="js/photo.js"></script>
</body>
</html>
