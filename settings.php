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
        $page = "settings.php";
        require "elements/appnav.php";
      ?>
    </nav>
    <header>
      <div class="feedpanel" hidden>
      </div>
      <h1 class="heading">Settings</h1>
    </header>
    <article>
      <div id="accountform">
        <script type="text/javascript" src="scripts/accountsettingsscript.js"></script>
        <?php
          require "extraforms/accountsettings.php";
        ?>
      </div>
      <h2>My Group</h2>
      <div id="groupform">
        <?php require "extraforms/groupsettings.php"; ?>
      </div>
    </article>
    <?php include "elements/popup.php" ?>
  </body>
</html>
