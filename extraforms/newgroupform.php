<?php
  $cwd = dirname(__FILE__);
  require $cwd."/../elements/getuser.php";
?>
<script src="scripts/newgroupformscript.js"></script>
<div class="popupcontainer" id="newgroupformdiv" style="display:none;">
  <h2>Create a New Group</h2>
  <form autocomplete="off">
    <input type="text" name="groupname" placeholder="Group Name">
    <div id="membersearch" class="fielddropdown">
        <input type="search" name="membersearch" placeholder="Find New Members">
        <div class="items">

        </div>
    </div>
    <ul id="memberlist">

    </ul>
    <input type="hidden" name="userid" value="<?php echo $user->getId(); ?>">
    <input type="submit" value="Create Group">
  </form>
  <p class="error" hidden></p>
</div>
