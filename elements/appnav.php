<?php
  if ($page == "dashboard.php"){
    echo "
    <ul id='sidebar_button'>
      <li><a href='javascript:toggleSidebar();' hidden>
        <i class='material-icons'>menu</i>
      </a>
    </ul>";
  }
?>
<ul id="main_buttons">
  <li><a href="dashboard.php" <?php if ($page == "dashboard.php"){echo "id='current_button'";} ?>>Dashboard</a>
  <li><a href="addbill.php" <?php if ($page == "addbill.php"){echo "id='current_button'";} ?>>Add Bill</a>
  <li><a href="settings.php" <?php if ($page == "settings.php"){echo "id='current_button'";} ?>>Settings</a>
  <li><a href="logout.php">Logout</a>
</ul>
<script type='text/javascript' src='scripts/feedfunctions.js'></script>
<ul id="feedbutton">
  <li><a href="javascript:toggleFeed();">
    <i class="material-icons">notifications_none</i>
    <div id="feedamount" hidden><label>10</label></div>
  </a>
</ul>
