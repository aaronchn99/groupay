<!DOCTYPE html>
<html>
  <head>
    <?php
      include "elements/sitehead.php";
      $cwd = dirname(__FILE__);
      include $cwd."/elements/apphead.php";
      if ($user->getGroup() !== null) {
        $user->getGroup()->updateMembers();
      }
    ?>
    <script src="scripts/newbillformscripts.js"></script>
  </head>
  <body>
    <nav>
      <?php
        $cwd = dirname(__FILE__);
        $page = "addbill.php";
        require $cwd."/elements/appnav.php";
      ?>
    </nav>
    <header>
      <div class="feedpanel" hidden>
      </div>
      <h1 class="heading">Add Bill</h1>
    </header>
    <article>
      <?php
        // Show new bill form if the user is in a group
        if ($user->getGroup() !== null){

          $membersIds = $user->getGroup()->getMembersIds();
          $memberList = "";
          $payerSliders = "";
          foreach($membersIds as $memberid){
            $memberRecord = Group::findMemberRecordById($memberid);
            $memberList .= "<label class='checkbox'>".hsc($memberRecord["FirstName"]." ".$memberRecord["LastName"])."
                              <input type='checkbox' name='user_".$memberid."' value='pay'>
                              <span class='checkmark'></span>
                            </label>";
            $payerSliders .= '<label id="share'.$memberid.'" hidden>'
                                .hsc($memberRecord["FirstName"]." ".$memberRecord["LastName"]).': &pound;<input type="number" min="0" max="0" step="0.01" class="sharevaluelabel">
                                <input type="range" min="0" max=0 name="share'.$memberid.'" disabled>
                              </label><br>';
          }

          echo '<h2 class="error"></h2>
          <form id="newbillform" action="processnewbill.php" method="post">
            <input type="hidden" name="today">
            <div class="formfield"><label>Name*: </label><input type="text" name="bill_name" maxlength="30" required></div>
            <div class="formfield"><label>Description: </label><br><textarea name="desc"></textarea></div>
            <div class="formfield"><label>Choose payers*: </label><br>
              <div id="payers_field">
                '.$memberList.'
              </div>
            </div>
            <div class="formfield"><label>Amount (&pound;, Up to &pound;999999)*: </label><input type="number" min="0.00" max="999999" step="0.01" name="amount" value="0.00" required></div>
            <div class="formfield"><label>Set payment shares*: </label><br>
              <div id="shares" hidden>
                '.$payerSliders.'
                <input type="button" onclick="resetPayerSliders();" value="Share equally">
                <label class="checkbox">Manual shares
                  <input type="checkbox" id="manualshare">
                  <span class="checkmark"></span>
                </label>
              </div>
            </div>
            <div class="formfield"><label>Due date*: </label><input type="date" name="due"></div>
            <div class="formfield"><div></div><input type="submit" value="Add Bill"></div><br>
          </form><p>*: Required fields</p><h2 class="error"></h2>';
        // Otherwise, show a message that user cannot add new bills
        } else {
          echo "<h3>You cannot add bills, as you are currently not in a Group</h3>";
        }
      ?>
    </article>
    <?php include "elements/popup.php" ?>
  </body>
</html>
