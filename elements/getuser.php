<?php
  $cwd = dirname(__FILE__);
  require $cwd."/../classes/User.php";
  $cwd = dirname(__FILE__);
  require $cwd."/getip.php";
  session_start();
  // If login ip not set or user object not created (If user not logged in)
  if (!isset($_SESSION["login_ip"]) || !isset($_SESSION["user"])){
    $_SESSION["auth_err"] = "needlogin";
    header("Location:login.php");
    exit();
  // If user is accessing from ip different to login client (Prevent's session hijacking)
  } elseif (getUserIpAddr() !== $_SESSION["login_ip"]){
    header("Location:deletecookie.php");
    exit();
  } else {  // If user has logged in and is accessing from login client
    $user = $_SESSION["user"];
  }
?>
