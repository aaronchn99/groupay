<?php
  $cwd = dirname(__FILE__);
  include $cwd."/antixss.php";
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  $cwd = dirname(__FILE__);
  require $cwd."/getuser.php";

?>


<link type="text/css" rel="stylesheet" href="styles/appstyle.css" charset="utf-8" />
<script src="scripts/datefunctions.js"></script>
<script src="scripts/popupscript.js"></script>
<script src="scripts/togglepanels.js"></script>
<script src="scripts/screenfit.js"></script>
<script src="scripts/checknotifications.js"></script>
