<?php
  require "elements/getuser.php";
  require 'Database.php';
  require "elements/antixss.php";
  $dueDate = new Date($_POST["due"],"y-m-d");
  $createDate = new Date($_POST["today"], "y-m-d");

  $query = "INSERT INTO Bills VALUES(null,:name,:owner,:details,:due);";
  $db = new Database();
  $stmt = $db->prepare($query);
  $stmt->bindValue(":name",$_POST["bill_name"],SQLITE3_TEXT);
  $stmt->bindValue(":owner",$user->getId(),SQLITE3_INTEGER);
  $stmt->bindValue(":details",$_POST["desc"],SQLITE3_TEXT);
  $stmt->bindValue(":due",$dueDate->getDateString("d-m-y"),SQLITE3_TEXT);
  $stmt->execute();

  $results = $db->querySingle("SELECT last_insert_rowid() FROM Bills;");
  $billId = $results["last_insert_rowid()"];

  foreach ($_POST as $key => $value) {  // Checks each post data
    if (substr($key,0,5) === "share"){ // Checks if data is share amount
      // Insert new Share
      $query = "INSERT INTO Shares VALUES(null,:billid,:payerid,0,:due,0);";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":billid",$billId,SQLITE3_INTEGER);
      $stmt->bindValue(":payerid",substr($key,5),SQLITE3_INTEGER);
      $stmt->bindValue(":due",$value,SQLITE3_INTEGER);
      $stmt->execute();

      $shareOwner = intval(substr($key,5));
      if ($shareOwner !== $user->getId()){ // Only write notifications for other payers
        // Write notification
        $title = "New Bill: ".hsc($_POST["bill_name"]);
        $date = $createDate->getDateString("d-m-y");
        $body = hsc($user->getFirstname())." ".hsc($user->getLastname())
                            ." has just added a new bill. Check the dashboard for more information.";
        $isread = 0;
        // Write to database
        $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,:isread,:recipient);";
        $stmt = $db->prepare($query);
        $stmt->bindValue(":title",$title,SQLITE3_TEXT);
        $stmt->bindValue(":date",$date,SQLITE3_TEXT);
        $stmt->bindValue(":body",$body,SQLITE3_TEXT);
        $stmt->bindValue(":isread",$isread,SQLITE3_INTEGER);
        $stmt->bindValue(":recipient",$shareOwner,SQLITE3_INTEGER);
        $stmt->execute();

        // Write browser and/or email alerts
        // Get recipient's data
        $query = "SELECT * FROM Users WHERE UserID = :userid";
        $stmt = $db->prepare($query);
        $stmt->bindValue(":userid",$shareOwner,SQLITE3_INTEGER);
        $userData = $db->stmtQuerySingle($stmt);

        if ($userData["EmailAlert"] != 0){  // If email alerts are enabled for payer
          $title = "New Bill: ".st($_POST["bill_name"]);
          $body = st($user->getFirstname())." ".st($user->getLastname())
                              ." has just added a new bill. Check the dashboard for more information.";
          mail($userData["Email"], $title, $body,"From:Groupay-noreply");
        }

        if ($userData["BrowserAlert"] != 0) { // If browser alerts are enabled for payer
          $alert = st($user->getFirstname())." ".st($user->getLastname())
                              ." has just added a new bill. Check the dashboard for more information.";

          $query = "INSERT INTO BrowserAlerts VALUES(null,:message,:recipient);";
          $stmt = $db->prepare($query);
          $stmt->bindValue(":message",$alert,SQLITE3_TEXT);
          $stmt->bindValue(":recipient",$shareOwner,SQLITE3_INTEGER);
          $stmt->execute();
        }
      }
    }
  }

  header("Location:dashboard.php");
?>
