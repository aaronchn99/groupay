<?php
  $cwd = dirname(__FILE__);
  include_once $cwd."/../elements/antixss.php";
  $cwd = dirname(__FILE__);
  require_once $cwd."/../Database.php";
  $cwd = dirname(__FILE__);
  require_once $cwd."/../elements/getuser.php";
  $user->updateUser();
?>

<h2>My Account</h2>
<h2 id="message" hidden></h2>
<form id="accountsettings">
  <div class="formfield">
    <label class="fieldname">Username: </label><input type="text" name="username" value="<?php echo hsc($user->getUsername()); ?>" maxlength="20">
  </div>
  <div class="formfield">
    <label class="fieldname">First Name: </label><input type="text" name="firstname" value="<?php echo hsc($user->getFirstname()); ?>">
  </div>
  <div class="formfield">
    <label class="fieldname">Last Name: </label><input type="text" name="lastname" value="<?php echo hsc($user->getLastname()); ?>">
  </div>
  <div class="formfield">
    <label class="fieldname">New Password: </label><input type="password" name="newpassword">
  </div>
  <div class="formfield">
    <label class="fieldname">Confirm Password: </label><input type="password" name="passconfirm">
  </div>
  <div class="formfield">
    <label class="fieldname">Email: </label><input type="email" name="email" value="<?php echo hsc($user->getEmail()); ?>">
  </div>
  <div class="formfield">
    <label class="fieldname">Notification Settings: </label>
    <ul>
      <li>
        <label class='checkbox'>Email Alerts
          <input type='checkbox' name='email_alerts' <?php if ($user->canEmailAlert()){echo "checked";} ?>>
          <span class='checkmark'></span>
        </label>
      </li>
      <li>
        <label class='checkbox'>Browser Alerts
          <input type='checkbox' name='browser_alerts' <?php if ($user->canBrowserAlert()){echo "checked";} ?>>
          <span class='checkmark'></span>
        </label>
      </li>
    </ul>
  </div>
  <div class="formfield">
    <h3 class="fieldname"></h3><div><input type="submit" value="Save Changes"> <input type="button" id="resetform" value="Reset Form"></div>
  </div>
</form>
<div class="formfield">
  <h3 class="fieldname">Current Group: </h3>
  <ul id="currentgrouplist">
    <?php
      if ($user->getGroup() !== null) {
        echo '<li><label>'.hsc($user->getGroup()->getName()).' </label>';
        // Admins cannot leave group
        if (!$user->isAdmin()){
            echo '<input type="button" name="leavegroup" value="Leave Group">';
        } else {
          echo '<label> - Cannot leave while Admin</label>';
        }
      } else {
        echo "<li><label>Currently not in a Group</label>";
      }
    ?>
  </ul>
</div>
