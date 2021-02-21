<?php
  require "Database.php";
  require "elements/getuser.php";
  require "elements/antixss.php";
  $mode = $_POST["settings_mode"];

  $db = new Database();
  if ($mode == "account"){ // Save changes made to account settings

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

    $responseData = $_POST;
    $responseData["success"] = true;

    exit(json_encode($responseData));

  } else if ($mode == "leavegroup") { // If user want to leave group

    $membersIds = $user->getGroup()->getMembersIds();
    $todaysDate = new Date($_POST["today"], "y-m-d");
    $groupName = $user->getGroup()->getName();

    // Set current group to null in database
    $query = "DELETE FROM Shares WHERE PayerID = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $user->getId(), SQLITE3_INTEGER);
    $stmt->execute();

    // Set current group to null in database
    $query = "UPDATE Users SET GroupID = null WHERE UserID = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $user->getId(), SQLITE3_INTEGER);
    $stmt->execute();

    // Write notifications to send to new member
    // Compose feed alert
    $title = "Member has left the Group";
    $body = hsc($user->getFirstname())." ".hsc($user->getLastname())." has left this group.";

    // Send notification to every other group member
    foreach($membersIds as $memberid){
      if ($memberid != $user->getId()) {
        // Write feed alert to Database
        $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,:isread,:recipient);";
        $stmt = $db->prepare($query);
        $stmt->bindValue(":title",$title,SQLITE3_TEXT);
        $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
        $stmt->bindValue(":body",$body,SQLITE3_TEXT);
        $stmt->bindValue(":isread",0,SQLITE3_INTEGER);
        $stmt->bindValue(":recipient",$memberid,SQLITE3_INTEGER);
        $stmt->execute();
      }
    }

    $title = "You have left the Group";
    $body = "You have left ".hsc($groupName).".";

    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,:isread,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":isread",0,SQLITE3_INTEGER);
    $stmt->bindValue(":recipient",$user->getId(),SQLITE3_INTEGER);
    $stmt->execute();

    $responseData["success"] = true;

    exit(json_encode($responseData));
  }
?>
