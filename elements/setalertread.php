<?php
  $cwd = dirname(__FILE__);
  require $cwd."/getuser.php";
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  $db = new Database();

  $userid = $user->getId();
  $alertid = $_GET["alertid"];

  $query = "UPDATE FeedAlerts SET IsRead = 1 WHERE RecipientID = :userid AND AlertID = :alertid;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
  $stmt->bindValue(":alertid",$alertid,SQLITE3_INTEGER);
  $stmt->execute();

  $query = "SELECT COUNT(*) FROM FeedAlerts WHERE RecipientID = :userid AND IsRead = 0;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
  $result = $db->stmtQuerySingle($stmt);
  $unreadCount = $result["COUNT(*)"];

  $response["unread_count"] = $unreadCount;
  $response["readalert"] = $alertid;
  echo json_encode($response);
?>
