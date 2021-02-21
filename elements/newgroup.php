<?php
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  $cwd = dirname(__FILE__);
  require $cwd."/getuser.php";
  $cwd = dirname(__FILE__);
  require $cwd."/antixss.php";

  $db = new Database();

  // Create the new group
  $query = "INSERT INTO Groups VALUES(null, :groupname, :ownerid);";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":groupname", $_POST["groupname"], SQLITE3_TEXT);
  $stmt->bindValue(":ownerid", $_POST["admin"], SQLITE3_INTEGER);
  $stmt->execute();

  // Fetch the new group's ID
  $results = $db->querySingle("SELECT last_insert_rowid() FROM Groups;");
  $groupId = $results["last_insert_rowid()"];

  // Join admin to the newly created group
  $query = "UPDATE Users SET GroupID = :groupid WHERE UserID = :ownerid;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
  $stmt->bindValue(":ownerid", $_POST["admin"], SQLITE3_INTEGER);
  $stmt->execute();

  $user->updateGroup();  // Update user's group object

  $todaysDate = new Date($_POST["today"],"y-m-d");  // Date of invite issue

  foreach ($_POST["members"] as $memberId) {

    // Write feed alert title
    $title = "Group invite";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,null,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$memberId,SQLITE3_INTEGER);
    $stmt->execute();

    // Get feed message's ID and write message body
    $results = $db->querySingle("SELECT last_insert_rowid() FROM FeedAlerts;");
    $body = "You are invited to ".hsc($_POST["groupname"]).".<br>
    <input type='button' onclick='acceptInvite(".$groupId.", ".$results["last_insert_rowid()"].");' value='Accept'>
    <input type='button' onclick='declineInvite(".$groupId.", ".$results["last_insert_rowid()"].");' value='Decline'>";

    // Update message with message body
    $query = "UPDATE FeedAlerts SET Body = :body WHERE AlertID = ".$results["last_insert_rowid()"];
    $stmt = $db->prepare($query);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->execute();

    // Find new member's email address and whether he can be emailed
    $query = "SELECT * FROM Users WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $memberId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $canEmail = $results["EmailAlert"];

    // If Admin can be emailed, send the email
    if ($canEmail !== 0){
      $body = "You are invited to ".hsc($user->getGroup()->getName()).".
      Log in to your account to accept/decline the invite.";
      mail($results["Email"], $title, $body,"From:Groupay-noreply");
    }
  }

  exit("success");
?>
