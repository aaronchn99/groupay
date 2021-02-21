<?php
  include "elements/autologin.php";
  require "Database.php";
  $db = new Database();
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "elements/sitehead.php"; ?>
    <?php require "elements/antixss.php"; ?>
  </head>
  <body>
    <header>
      <?php require "elements/siteheader.php"; ?>
    </header>
    <nav>
      <?php
        $page = "register.php";
        require "elements/sitenav.php";
      ?>
    </nav>
    <article>
      <h2>Register</h2>
      <form action="auth.php" method="post">
        <label>Username: </label>
        <input type="text" name="username" maxlength="20"><br>
        <label>First Name: </label>
        <input type="text" name="firstname"><br>
        <label>Last Name: </label>
        <input type="text" name="lastname"><br>
        <label>Password: </label>
        <input type="password" name="password"><br>
        <label>Confirm Password: </label>
        <input type="password" name="pass_confirm"><br>
        <label>E-Mail address: </label>
        <input type="email" name="email"><br>
        <label>Payment Group: </label>
        <select name="group" placeholder="Select Group">
          <?php
            $query = "SELECT * FROM Groups;";
            $results = $db->query($query);

            while ($group = $results->fetchArray()){
              echo "<option value='".$group["GroupID"]."'>".hsc($group["GroupName"])."</option>";
            }
          ?>
          <option>Join Later</option>
        </select><br>
        <label class='checkbox'>Tick to get email alerts about new bills
          <input type='checkbox' name='email_alerts' value="1">
          <span class='checkmark'></span>
        </label><br>
        <label class='checkbox'>Tick to get browser alerts about new bills
          <input type="checkbox" name="browser_alerts" value="1">
          <span class='checkmark'></span>
        </label><br>
        <input type="submit" name="auth_mode" value="Register">
      </form>
      <h2 class='error'>
        <?php
          if (isset($_SESSION["auth_err"])){
            if ($_SESSION["auth_err"] == "userexists"){
              echo "Username already used<br>";
              session_destroy();
            }
            if ($_SESSION["auth_err"] == "emailused"){
              echo "Email already used<br>";
              session_destroy();
            }
            if ($_SESSION["auth_err"] == "diffpass"){
              echo "Passwords do not match<br>";
              session_destroy();
            }
          }
        ?>
      </h2>
    </article>
  </body>
</html>
