<?php
  include "elements/apphead.php";
  foreach ($user->getShares() as $share){
    if ($share->getId() == $_POST["share_id"]){
      $thisShare = $share;
      break;
    }
  }

  $thisShare->pay($_POST["amount"]*100);
  header("Location:paybill.php?share_id=".$thisShare->getId());
?>
