<?php
  $cwd = dirname(__FILE__);
  require "antixss.php";
  require "getuser.php";
  require $cwd."/../Database.php";
  require "sharesortfunctions.php";

  $shares = $user->getShares();
  $i = 0;

  foreach($shares as $share) {

    if ($_POST["search"] != ""){
      // Narrow down by search query
      $query = $_POST["search"];
      // If the share's billname does not start with query string
      if (strncasecmp($query, $share->getName(), strlen($query)) != 0){
        $shares = array_merge(array_slice($shares,0,$i), array_slice($shares,$i+1));
        continue;
      }
    }
    if (!$_POST["paid"]){
      // Eliminate completed shares
      if ($share->isPaid()){
        $shares = array_merge(array_slice($shares,0,$i), array_slice($shares,$i+1));
        continue;
      }
    }
    if (!$_POST["pending"]){
      // Eliminate incompleted shares
      if (!$share->isPaid()){
        $shares = array_merge(array_slice($shares,0,$i), array_slice($shares,$i+1));
        continue;
      }
    }
    if ($_POST["from"] != "") {
      $fromDate = new Date($_POST["from"], "y-m-d");
      // Eliminate shares due before from date
      if ($share->getDueDate()->isBefore($fromDate)){
        $shares = array_merge(array_slice($shares,0,$i), array_slice($shares,$i+1));
        continue;
      }
    }
    if ($_POST["to"] != "") {
      $toDate = new Date($_POST["to"], "y-m-d");
      // Eliminate shares due after to date
      if ($share->getDueDate()->isAfter($toDate)){
        $shares = array_merge(array_slice($shares,0,$i), array_slice($shares,$i+1));
        continue;
      }
    }
    $i++;
  }

  setComparator($_POST["sort"]);

  $sortedShares = sortShares($shares, $_POST["direction"]);
?>
<?php
  if (count($sortedShares) == 0){
    if (count($user->getShares()) == 0){
      echo "<h3>No bills to pay</h3>";
    } else {
      echo "<h3>No bills match your criteria</h3>";
    }
  } else {
    foreach($sortedShares as $share){
      echo "<div id='share".$share->getId()."' class='billrows";
      if ($share->isPaid()){  // Applies paid class if fully paid
        echo " paid'";
      } else {
        echo "'";
      }
      echo "><div class='namecell'>".hsc($share->getName())."</div>
        <div class='outstandingcell'>&pound;".number_format($share->getOutstandingAmount()/100, 2)."</div>
        <div class='duecell'>".$share->getDueDateString("d-m-y")."</div><div class='paycell'>";
        if (!$share->isPaid()){ // Show pay button only if not paid yet
          echo "<a href='paybill.php?share_id=".$share->getId()."'>Pay</a>";
        }
      echo "</div></div><div class='billpanel' id='sharepanel".$share->getId()."' style='display:none'>";
      $_GET["share_id"] = $share->getId();
      $cwd = dirname(__FILE__);
      require "showbill.php";
      echo "</div>";
    }
  }
?>
