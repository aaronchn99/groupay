<?php
  $cwd = dirname(__FILE__);
  require $cwd."/elements/apphead.php";
  $db = new Database();

  // Get line to delete
  $alertid = $_GET["line"];
  $userid = $user->getId();

  // Delete alert from FeedAlerts table
  $query = "DELETE FROM FeedAlerts WHERE RecipientID = :userid AND AlertID = :alertid;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
  $stmt->bindValue(":alertid",$alertid,SQLITE3_INTEGER);
  $stmt->execute();

?>
