<?php
if (isset($_POST['logout'])) {
  if ($_SESSION['id'] && $_SESSION['id'] != "")
  {
    $_SESSION['id'] = "";
    header("location: index.php");
  }
}

?>
