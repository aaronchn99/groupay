<?php
  foreach ($user->getShares() as $share){
    if ($share->getId() == $_GET["share_id"]){
      $thisShare = $share;
      break;
    }
  }
?>
<h2><?php echo hsc($thisShare->getName()); ?></h2>
<p>
  <?php echo hsc($thisShare->getDetails()); ?>
</p>
<div id="owner_date">
  <h3>Set by
    <?php
      $ownerid = $thisShare->getPayeeId();

      $db = new Database();
      $query = "SELECT * FROM Users WHERE UserID = :ownerid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":ownerid", $ownerid, SQLITE3_INTEGER);
      $ownerData = $db->stmtQuerySingle($stmt);

      echo hsc($ownerData["FirstName"]." ".$ownerData["LastName"]);
    ?>
  </h3>
  <h3>Due: <?php echo $thisShare->getDueDateString("d-m-y"); ?></h3>
</div>
<h3>Total due: &pound;<?php echo number_format($thisShare->getTotalAmount()/100, 2); ?></h3>
<div class="progressbarlabels">
  <label>Paid</label><label>Remaining</label>
</div>
<div id="progressbar">
  <div id="remainingbar"><div id="paidbar" style="width:<?php echo (($thisShare->getPaidAmount()/$thisShare->getTotalAmount())*100)."%" ?>;"><label></label></div></div>
</div>
<div class="progressbarlabels">
  <label>&pound;<?php echo number_format($thisShare->getPaidAmount()/100, 2); ?></label><label>&pound;<?php echo number_format($thisShare->getOutstandingAmount()/100, 2); ?></label>
</div>
