<!DOCTYPE html>
<html>
  <head>
    <?php
      include "elements/sitehead.php";
      include "elements/apphead.php";
    ?>
  </head>
  <body>
    <nav>
      <?php
        $page = "paybill.php";
        require "elements/appnav.php";
      ?>
    </nav>
    <header>
      <div class="feedpanel" hidden>
      </div>
      <h1 class="heading">Pay Bill</h1>
    </header>
    <article>
      <h2>Bill to pay: </h2>
      <div class="billpanel">
        <?php require "elements/showbill.php"; ?>
      </div>
      <?php
        if (!$thisShare->isPaid()){
          echo '<form action="processpayment.php" method="post">
            <label>Amount outstanding: </label><h3 id="due_amount">&pound;'.number_format($thisShare->getOutstandingAmount()/100, 2).'</h3>
            <label>Amount to pay: <label><input type="number" name="amount" min="0.00" step="0.01" max="'.number_format($thisShare->getOutstandingAmount()/100, 2).'"><br>
            <input type="hidden" name="share_id" value="'.$thisShare->getId().'">
            <input type="submit" value="Pay">
          </form>';
        } else {
          echo "<h3>You have fully paid this bill</h3>";
        }
      ?>
    </article>
    <?php include "elements/popup.php" ?>
  </body>
</html>
