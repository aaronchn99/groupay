<?php
  $cwd = dirname(__FILE__);
  require $cwd."/Database.php";
  $cwd = dirname(__FILE__);
  require $cwd."/elements/getuser.php";
  $cwd = dirname(__FILE__);
  require $cwd."/elements/antixss.php";

  function userJoinGroup($userId, $groupId){
    // Update database
    $query = "UPDATE Users SET GroupID = :groupid WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $stmt->execute();

    $user->updateGroup();  // Update user's group object

  }


  $db = new Database();

  /* The join group action
  /*
  /* POST params: action, groupid, today,
  */
  if ($_POST["action"] == "join"){

    $userId = $user->getId();
    $groupId = $_POST["groupid"];
    $todaysDate = new Date($_POST["today"],"y-m-d");
    // Send Group join request notifications to admin
    // Find admin's User ID
    $query = "SELECT OwnerID FROM Groups WHERE GroupID = :groupid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $adminId = $results["OwnerID"];

    // Write feed alert title
    $title = "Group join request";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,null,:isread,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":isread",0,SQLITE3_INTEGER);
    $stmt->bindValue(":recipient",$adminId,SQLITE3_INTEGER);
    $stmt->execute();


    $results = $db->querySingle("SELECT last_insert_rowid() FROM FeedAlerts;");
    $body = hsc($user->getFirstname())." ".hsc($user->getLastname())." wants to join your group.<br>
    <input type='button' onclick='acceptRequest(".$userId.", ".$results["last_insert_rowid()"].");' value='Accept'>
    <input type='button' onclick='declineRequest(".$userId.", ".$results["last_insert_rowid()"].");' value='Decline'>";

    $query = "UPDATE FeedAlerts SET Body = :body WHERE AlertID = ".$results["last_insert_rowid()"];
    $stmt = $db->prepare($query);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->execute();

    // Find Admin's email address and whether he can be emailed
    $query = "SELECT * FROM Users WHERE UserID = :adminid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":adminid", $adminId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $canEmail = $results["EmailAlert"];

    // If Admin can be emailed, send the email
    if ($canEmail !== 0){
      $body = hsc($user->getFirstname())." ".hsc($user->getLastname())." wants to join your group.
      Log in to your account to accept/decline the request.";
      mail($results["Email"], $title, $body,"From:Groupay-noreply");
    }
    exit("success");

  /* The join accept group action
  /*
  /* POST params: action, userid, today,
  */
  } elseif ($_POST["action"] == "joinaccept") {
    // Get new member's id and group id
    $userId = $_POST["userid"];
    $groupId = $user->getGroup()->getId();
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Update database (Join user to group)
    $query = "UPDATE Users SET GroupID = :groupid WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $stmt->execute();

    $user->updateGroup();  // Update user's group object

    // Write notifications to send to new member
    // Compose feed alert
    $title = "Join Request Accepted";
    $body = "You have been accepted to ".hsc($user->getGroup()->getName()).". You can now add new bills.";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,:isread,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":isread",0,SQLITE3_INTEGER);
    $stmt->bindValue(":recipient",$userId,SQLITE3_INTEGER);
    $stmt->execute();

    // Find new member's email address and whether he can be emailed
    $query = "SELECT * FROM Users WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $canEmail = $results["EmailAlert"];

    // If new member can be emailed, write and send the email
    if ($canEmail !== 0){
      $title = "Join Request Accepted";
      $body = "You have been accepted to ".hsc($user->getGroup()->getName()).". You can now add new bills.";
      mail($results["Email"], $title, $body,"From:Groupay-noreply");
    }
    exit("success");

  /* The join decline group action
  /*
  /* POST params: action, userid, today,
  */
  } elseif ($_POST["action"] == "joindecline"){
    $userId = $_POST["userid"];
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Write notifications to send to new member
    // Compose feed alert
    $title = "Join Request Declined";
    $body = "Your request to join ".hsc($user->getGroup()->getName())." has been declined.";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,:isread,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":isread",0,SQLITE3_INTEGER);
    $stmt->bindValue(":recipient",$userId,SQLITE3_INTEGER);
    $stmt->execute();
    exit("success");

  /* The invite action
  /*
  /* POST params: action, memberid, today
  */
  } elseif ($_POST["action"] == "invite") {
    $memberId = $_POST["memberid"];
    $groupId = $user->getGroup()->getId();
    $todaysDate = new Date($_POST["today"],"y-m-d");  // Date of invite issue

    // Write feed alert title
    $title = "Group invite";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,null,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$memberId,SQLITE3_INTEGER);
    $stmt->execute();

    // Get group name
    $query = "SELECT * FROM Groups WHERE GroupID = :groupid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $groupname = $results["GroupName"];

    // Get feed message's ID and write message body
    $results = $db->querySingle("SELECT last_insert_rowid() FROM FeedAlerts;");
    $body = "You are invited to ".hsc($groupname).".<br>
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

    exit("success");

  /* The accept invite action
  /*
  /* POST params: action, groupid, today
  */
  } elseif ($_POST["action"] == "inviteaccept") {
    $userId = $user->getId();
    $groupId = $_POST["groupid"];
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Update database
    $query = "UPDATE Users SET GroupID = :groupid WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $stmt->execute();

    $user->updateGroup();  // Update user's group object

    // Write notifications to send to group admin
    // Find group admin's user id
    $query = "SELECT OwnerID FROM Groups WHERE GroupID = :groupid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $adminId = $results["OwnerID"];

    // Compose feed alert
    $title = "Group Invite Accepted";
    $body = hsc($user->getFirstname()." ".$user->getLastname())." has accepted your invite.";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$adminId,SQLITE3_INTEGER);
    $stmt->execute();

    // Get admin's email address and whether he can be emailed
    $query = "SELECT * FROM Users WHERE UserID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $adminId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $canEmail = $results["EmailAlert"];

    // If new member can be emailed, write and send the email
    if ($canEmail !== 0){
      mail($results["Email"], $title, $body,"From:Groupay-noreply");
    }
    exit("success");

  /* The accept invite action
  /*
  /* POST params: action, groupid, today
  */
  } elseif ($_POST["action"] == "invitedecline") {
    $groupId = $_POST["groupid"];
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Write notifications to send to admin
    // Find group admin's user id
    $query = "SELECT OwnerID FROM Groups WHERE GroupID = :groupid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $groupId, SQLITE3_INTEGER);
    $results = $db->stmtQuerySingle($stmt);
    $adminId = $results["OwnerID"];

    // Compose feed alert
    $title = "Group Invite Declined";
    $body = hsc($user->getFirstname()." ".$user->getLastname())."'s invitation to join your group has been declined.";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$adminId,SQLITE3_INTEGER);
    $stmt->execute();

    exit("success");

  /* The change group name action
  /*
  /* POST params: action, groupname
  */
  } elseif ($_POST["action"] == "changename") {
    $newName = $_POST["groupname"];

    $query = "UPDATE Groups SET GroupName = :groupname where GroupID = :groupid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupname",$newName,SQLITE3_TEXT);
    $stmt->bindValue(":groupid",$user->getGroup()->getId(),SQLITE3_INTEGER);
    $stmt->execute();

    $response = array("groupname"=>$newName, "success"=>true);
    exit(json_encode($response));

  /*
    Kick action

    Post params: action, userid, today
  */
  } elseif ($_POST["action"] == "kick") {
    $userId = $_POST["userid"]; // Get kicked user's id
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Update Users (Remove kicked user's link to group)
    $query = "UPDATE Users SET GroupID = NULL WHERE UserID = :userid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $stmt->execute();
    // Update admin's group object
    $user->updateGroup();

    // Notify kicked user
    // Compose feed alert
    $title = "You have been kicked";
    $body = "You have been kicked from ".hsc($user->getGroup()->getName()).".
    You will not get any new bills from this Group
    (Please note, you will still need to pay any unpaid bills from this Group).";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$userId,SQLITE3_INTEGER);
    $stmt->execute();

    exit("success");

  /*
    Set admin action

    POST params: action, userid, today
  */
  } elseif ($_POST["action"] == "setadmin") {
    $userId = $_POST["userid"]; // Get new admin's user id
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Update Users (Change group's owner id)
    $query = "UPDATE Groups SET OwnerID = :userid WHERE GroupID = :groupid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $user->getGroup()->getId(), SQLITE3_INTEGER);
    $stmt->bindValue(":userid", $userId, SQLITE3_INTEGER);
    $stmt->execute();
    // Update admin's group object
    $user->updateGroup();

    // Notify kicked user
    // Compose feed alert
    $title = "You have set as the new admin";
    $body = "You have been kicked from ".hsc($user->getGroup()->getName()).".
    You will not get any new bills from this Group
    (Please note, you will still need to pay any unpaid bills from this Group).";

    // Write feed alert to Database
    $query = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,0,:recipient);";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":title",$title,SQLITE3_TEXT);
    $stmt->bindValue(":date",$todaysDate->getDateString("d-m-y"),SQLITE3_TEXT);
    $stmt->bindValue(":body",$body,SQLITE3_TEXT);
    $stmt->bindValue(":recipient",$userId,SQLITE3_INTEGER);
    $stmt->execute();

    exit("success");

  /*
    Delete group action

    POST params: action, today
  */
  } elseif ($_POST["action"] == "deletegroup"){
    // Query to remove member from group
    $query1 = "UPDATE Users SET GroupID = null WHERE UserID = :userid;";
    // Query to add notification
    $query2 = "INSERT INTO FeedAlerts VALUES(null,:title,:date,:body,0,:userid);";
    // Compose notification
    $title = "Group has dissolved";
    $body = hsc($user->getGroup()->getName())." has been dissolved.
    Please note that you still need to pay off all pending bills from this Group.";
    $todaysDate = new Date($_POST["today"],"y-m-d");

    // Remove all members from group
    foreach ($user->getGroup()->getMembersIds() as $memberId) {
      // Remove member from group
      $stmt = $db->prepare($query1);
      $stmt->bindValue(":userid", $memberId, SQLITE3_INTEGER);
      $stmt->execute();

      // Send notification to member informing group dissolution
      $stmt = $db->prepare($query2);
      $stmt->bindValue(":title", $title, SQLITE3_TEXT);
      $stmt->bindValue(":date", $todaysDate->getDateString("d-m-y"), SQLITE3_TEXT);
      $stmt->bindValue(":body", $body, SQLITE3_TEXT);
      $stmt->bindValue(":userid", $memberId, SQLITE3_INTEGER);
      $stmt->execute();
    }

    $query = "DELETE FROM Groups WHERE GroupID = :groupid";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":groupid", $user->getGroup()->getId(), SQLITE3_INTEGER);
    $stmt->execute();

    $user->updateUser();

    exit("success");

  }
?>
