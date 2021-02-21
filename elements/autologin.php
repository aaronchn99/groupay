<?php
  $cwd = dirname(__FILE__);
  require $cwd."/getip.php";

  session_start();
  // If login ip is set and user object created (If user logged in)
  if (isset($_SESSION["login_ip"]) && isset($_SESSION["user"])){
    // If user is accessing from ip different to login client (Prevent's session hijacking)
    if (getUserIpAddr() !== $_SESSION["login_ip"]){
      header("Location:deletecookie.php");
      exit();
    } else {  // If user has logged in and is accessing from login client
      header("Location:dashboard.php");
      exit();
    }
  }
?>
