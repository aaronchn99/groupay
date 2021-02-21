<?php
  $cwd = dirname(__FILE__);
  include $cwd."/getuser.php";
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  $db = new Database();

  if ($user->canBrowserAlert()){  // Only checked when browser alerts are enabled
    $alerts = array();
    $userid = $user->getId();
    // Get user's alerts
    $query = "SELECT * FROM BrowserAlerts WHERE RecipientID = :userid;";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
    $result = $stmt->execute();

    while($message = $result->fetchArray()){  // Parse results to array of alerts
      array_push($alerts, $message["Message"]);
    }
    if (count($alerts) !== 0 ){
      // Clear all of user's alerts
      $query = "DELETE FROM BrowserAlerts WHERE RecipientID = :userid;";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":userid",$userid,SQLITE3_INTEGER);
      $stmt->execute();
    // If no alerts received, set alerts to null
    } else {
      $alerts = null;
    }

    echo json_encode($alerts);  // Return array of alerts
  }
?>
