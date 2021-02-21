<?php include "elements/autologin.php"; ?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "elements/sitehead.php"; ?>
  </head>
  <body>
    <header>
      <?php require "elements/siteheader.php"; ?>
    </header>
    <nav>
      <?php
        $page = "login.php";
        require "elements/sitenav.php";
      ?>
    </nav>
    <article>
      <h2>Login</h2>
      <form action="auth.php" method="post">
        <label>Username</label>
        <input type="text" name="username"><br>
        <label>Password</label>
        <input type="password" name="password"><br>
        <input type="submit" name="auth_mode" value="Login">
      </form><br>
      <h2 class="error">
        <?php
          if (isset($_SESSION["auth_err"])){
            if ($_SESSION["auth_err"] == "wrongpass"){
              echo "Wrong Password<br>";
              session_destroy();
            }
            if ($_SESSION["auth_err"] == "usernotexist"){
              echo "User does not exist<br>";
              session_destroy();
            }
            if ($_SESSION["auth_err"] == "needlogin"){
              echo "You need to login first<br>";
              session_destroy();
            }
            if ($_SESSION["auth_err"] == "notloginip"){
              echo "You did not login on this machine<br>";
            }
          }
        ?>
      </h2>
    </article>
  </body>
</html>
