<?php
  $cwd = dirname(__FILE__);
  require $cwd."/getuser.php";
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  $db = new Database();

  $userid = $user->getId();
  $query = "SELECT * FROM FeedAlerts WHERE RecipientID = :userid;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
  $feeds = $stmt->execute();

  $htmlContent = "";
  $unreadCount = 0;
  $responseData = array();

  while($feed = $feeds->fetchArray()){
    $htmlContent = "<a class='closemessage' href='javascript:deleteMessage(".$feed["AlertID"].")'><i class='material-icons'>close</i></a>
                    <h3>".$feed["Title"]."</h3><label>".$feed["Date"]."</label><p>".$feed["Body"]."</p>".$htmlContent;
    if ($feed["IsRead"] == 0){
      $unreadCount++;
      $htmlContent = "<li id='line".$feed["AlertID"]."' class='unread'>".$htmlContent;
    } else {
      $htmlContent = "<li id='line".$feed["AlertID"]."'>".$htmlContent;
    }
  }
  if ($htmlContent == ""){
    $htmlContent = "<h2>No Notifications</h2>";
  }

  $htmlContent ="<script type='text/javascript' src='scripts/feedfunctions.js'></script>
  <ul>".$htmlContent."</ul>";
  $responseData["content"] = $htmlContent;
  $responseData["unread_count"] = $unreadCount;

  echo json_encode($responseData);
?>
