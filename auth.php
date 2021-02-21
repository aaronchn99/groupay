<?php
  $cwd = dirname(__FILE__);
  require $cwd."/Database.php";
  $cwd = dirname(__FILE__);
  require $cwd."/classes/User.php";
  $cwd = dirname(__FILE__);
  require $cwd."/elements/getip.php";

  session_start();
  $db = new Database();
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Registration
  if ($_POST["auth_mode"] == "Register"){
    // Check if passwords are different
    $passCheck = $_POST["pass_confirm"];
    if ($password != $passCheck){
      $_SESSION["auth_err"] = "diffpass";
      header("Location:register.php");
      die();
    }

    // Count number of users that have same username
    $query = "SELECT COUNT(*) FROM Users WHERE Username = :username;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":username", $username, SQLITE3_TEXT);
    $result = $db->stmtQuerySingle($stmt);

    if($result["COUNT(*)"] == 0){ // If no user has same username
      // Count number of users that have same email
      $email = $_POST["email"];

      $query = "SELECT COUNT(*) FROM Users WHERE Email = :email;";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":email",$email,SQLITE3_TEXT);
      $result = $db->stmtQuerySingle($stmt);

      if($result["COUNT(*)"] == 0){ // If no user has same email
          // Generate salt, pepper and password hash
          $salt = time();
          $pepper = rand(1000, 10000);
          $hash = sha1($password.$salt.$pepper);

          // Set false values to unticked options
          if (!isset($_POST["email_alerts"])){
            $_POST["email_alerts"] = 0;
          }
          if (!isset($_POST["browser_alerts"])){
            $_POST["browser_alerts"] = 0;
          }

          if ($_POST["group"] == 0){
            $groupid = null;
          } else {
            $groupid = $_POST["group"];
          }

          // Insert new user to Users
          $query = "INSERT INTO Users VALUES(null,:name,:first,:last,:email,:hash,:salt,:emailalert,:browseralert,:groupid);";
          $stmt = $db->prepare($query);
          $stmt->bindValue(":name",$username,SQLITE3_TEXT);
          $stmt->bindValue(":first",$_POST["firstname"],SQLITE3_TEXT);
          $stmt->bindValue(":last",$_POST["lastname"],SQLITE3_TEXT);
          $stmt->bindValue(":email",$email,SQLITE3_TEXT);
          $stmt->bindValue(":hash",$hash,SQLITE3_TEXT);
          $stmt->bindValue(":salt",$salt,SQLITE3_TEXT);
          $stmt->bindValue(":emailalert",$_POST["email_alerts"],SQLITE3_INTEGER);
          $stmt->bindValue(":browseralert",$_POST["browser_alerts"],SQLITE3_INTEGER);
          $stmt->bindValue(":groupid",$groupid,SQLITE3_INTEGER);
          $result = $stmt->execute();

      } else {  // If email already used
        $_SESSION["auth_err"] = "emailused";
        header("Location:register.php");
        exit();
      }
    } else {  // If username already used
      $_SESSION["auth_err"] = "userexists";
      header("Location:register.php");
      exit();
    }
  }

  $query = "SELECT * FROM Users WHERE Username = :username";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":username", $username, SQLITE3_TEXT);
  $userData = $db->stmtQuerySingle($stmt);

  if ($userData === false){
    $_SESSION["auth_err"] = "usernotexist";
    header("Location:login.php");
    exit();
  }

  $salt = $userData["Salt"];
  $realHash = $userData["PasswordHash"];
  // $inputHash = sha1($password.$salt);

  for ($i=1000;$i<10000;$i++){
    $inputHash = sha1($password.$salt.$i);
    if ($inputHash == $realHash){
      $user = new User(
                  $userData["UserID"],
                  $userData["Username"],
                  $userData["FirstName"],
                  $userData["LastName"],
                  $userData["Email"],
                  $userData["EmailAlert"],
                  $userData["BrowserAlert"],
                  $userData["GroupID"]
                );
      $_SESSION["user"] = $user;
      $_SESSION["login_ip"] = getUserIpAddr();
      touch("notifications/user".$user->getId().".json");

      header("Location:dashboard.php");
      exit();
    }
  }
  $_SESSION["auth_err"] = "wrongpass";
  header("Location:login.php");
  exit();

  // if ($inputHash != $realHash){
  //   $_SESSION["auth_err"] = "wrongpass";
  //   header("Location:login.php");
  //   exit();
  // } else {
  //   $user = new User(
  //             $userData["UserID"],
  //             $userData["Username"],
  //             $userData["FirstName"],
  //             $userData["LastName"],
  //             $userData["Email"],
  //             $userData["EmailAlert"],
  //             $userData["BrowserAlert"],
  //             $userData["GroupID"]
  //           );
  //   $_SESSION["user"] = $user;
  //   header("Location:dashboard.php");
  //   exit();
  // }

?>
