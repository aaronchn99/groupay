<?php
  require "Database.php";
  require "elements/getuser.php";
  $mode = $_POST["settings_mode"];

  $db = new Database();
  if ($mode == "account"){

    if ($_POST["password"] == ""){
      // Use existing password hash and salt
      $query = "SELECT PasswordHash, Salt FROM Users WHERE UserID = :userid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":userid", $user->getId(), SQLITE3_TEXT);
      $userData = $db->stmtQuerySingle($stmt);

      $hash = $userData["PasswordHash"];
      $salt = $userData["Salt"];
    } else {
      // Generate salt, pepper and password hash
      $salt = time();
      $pepper = rand(1000, 10000);
      $hash = sha1($_POST["password"].$salt.$pepper);
    }

    $query = "UPDATE Users SET Username = :username,
                                FirstName = :first,
                                LastName = :last,
                                Email = :email,
                                PasswordHash = :hash,
                                Salt = :salt,
                                EmailAlert = :emailalert,
                                BrowserAlert = :browsealert WHERE UserID = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":username",$_POST["username"],SQLITE3_TEXT);
    $stmt->bindValue(":first",$_POST["firstname"],SQLITE3_TEXT);
    $stmt->bindValue(":last",$_POST["lastname"],SQLITE3_TEXT);
    $stmt->bindValue(":email",$_POST["email"],SQLITE3_TEXT);
    $stmt->bindValue(":hash",$hash,SQLITE3_TEXT);
    $stmt->bindValue(":salt",$salt,SQLITE3_TEXT);
    $stmt->bindValue(":emailalert", $_POST["email_alerts"], SQLITE3_INTEGER);
    $stmt->bindValue(":browsealert", $_POST["browser_alerts"], SQLITE3_INTEGER);
    $stmt->bindValue(":userid", $user->getId(), SQLITE3_INTEGER);
    $stmt->execute();
    echo "success";
    exit();
  } else if ($mode == "leavegroup") {
    $query = "UPDATE Users SET GroupID = null WHERE UserID = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $user->getId(), SQLITE3_INTEGER);
    $stmt->execute();
    echo "success";
    exit();
  } else if ($mode == "group") {

  }
?>
