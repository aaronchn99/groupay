<?php
  setcookie("PHPSESSID", $_COOKIE["PHPSESSID"], 1000);
  header("Location:login.php");
  exit();
?>
