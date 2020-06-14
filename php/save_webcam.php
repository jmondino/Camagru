<?php
session_start();

global $dossier;
global $fichier;
if (isset($_SESSION['id']))
  $id = $_SESSION['id'];
else
  return ;

$dossier = "../ressources/tmp/";
$name = "img_tmp".$id.".png";
$fichier = basename($name);

if (isset($_POST['webcam_data']))
{
  $webcam_data = $_POST['webcam_data'];
  if ($webcam_data != "")
  {
    $webcam_data = str_replace('data:image/png;base64,', '', $webcam_data);
    $webcam_data = str_replace(' ', '+', $webcam_data);
    $webcam_data = base64_decode($webcam_data);
    file_put_contents($dossier . $fichier, $webcam_data);
  }
}

?>
