<?php
  $cwd = dirname(__FILE__);
  include_once $cwd."/../elements/antixss.php";
  $cwd = dirname(__FILE__);
  include_once $cwd."/../Database.php";
  $cwd = dirname(__FILE__);
  include_once $cwd."/../elements/getuser.php";

  $user->updateUser();
  if ($user->getGroup() !== null) {
    $user->getGroup()->updateMembers();
  }

  if ($user->getGroup() !== null && !$user->isAdmin()) {
    // When user already in group and not Admin
    echo '<p>You are already in a Group, and not the Admin. You cannot create a new Group or change Group settings.</p>';
  } elseif ($user->isAdmin()){
    // When user is Admin
    $group = $user->getGroup();
    $memberListHtml = "";
    foreach($group->getMembersIds() as $memberid){
      $memberRecord = Group::findMemberRecordById($memberid);
      $memberListHtml .= "<li>".hsc($memberRecord["FirstName"]." ".$memberRecord["LastName"]);
      if ($memberRecord["UserID"] == $user->getId()){
        $memberListHtml .= " - You";
      } else {
        $memberListHtml .= '<input type="hidden" name="userid" value="'.$memberid.'">
                            <input type="button" name="kickuser" value="Kick">';
                            // <input type="button" name="setadmin" value="Set Admin">
      }
    }
    echo '
    <script src="scripts/admingroupsettings.js" type="text/javascript"></script>
    <form id="groupnameform">
      <div class="formfield">
        <label class="fieldname">Group Name: </label><input type="text" name="groupname" value='.hsc($group->getName()).' maxlength="20">
      </div>
      <div class="formfield">
        <h3 class="fieldname"></h3>
        <div>
          <input type="submit" name="savename" value="Save Changes">
          <input type="button" name="resetname" value="Reset Form">
        </div>
      </div>
    </form>
    <div class="formfield">
      <label class="fieldname">Group Members: </label>
      <ul id="currentmembers">'.
      $memberListHtml
      .'</ul>
    </div>
    <div class="formfield">
      <label class="fieldname">Add New Member: </label>
      <div id="addmember" class="fielddropdown">
        <input type="search" name="addmember" placeholder="Find New Members">
        <div class="items flowup">
        </div>
      </div>
    </div>
    <div class="formfield">
      <label class="fieldname">Delete Group: </label><input type="button" name="deletegroup" value="Delete Group">
    </div>';
  } else {
    // When user is not in a group
    echo '
    <script src="scripts/nongroupusersettings.js" type="text/javascript"></script>
    <div class="formfield">
      <label class="fieldname">Create a New Group: </label><input type="button" name="newgroup" value="Create Group">
    </div>
    <form id="joingroupform">
      <div class="formfield">
        <label>Join a Group: </label>
        <select name="group">
          <option value="select">Select Group</option>';

          $db = new Database();
          $query = "SELECT * FROM Groups;";
          $results = $db->query($query);
          var_dump($results);

          while ($group = $results->fetchArray()){
            echo "<option value='".$group["GroupID"]."'>".hsc($group["GroupName"])."</option>";
          }

    echo '</select>
          <input type="submit" value="Join Group">
      </div>
    </form>';
  }
?>
