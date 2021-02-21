<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
      include "elements/sitehead.php";
      $cwd = dirname(__FILE__);
      require $cwd."/elements/apphead.php";
      $user->updateShares();
    ?>
    <script src="scripts/billtable.js"></script>
  </head>
  <body>
    <nav>
      <?php
        $cwd = dirname(__FILE__);
        $page = "dashboard.php";
        require $cwd."/elements/appnav.php";
      ?>
    </nav>

    <header>
      <div class="feedpanel" hidden>
      </div>
      <h1 class="heading">Welcome, <?php echo hsc($user->getFirstname()); ?></h1>
      <h1 id="totallabel">Total Due: &pound;<?php echo number_format($user->getTotal()/100, 2); ?></h1>
    </header>

    <div class="sidebar">
      <h2>Search Bill</h2>
      <input type="search" placeholder="Search..." name="searchquery">
      <h2>View</h2>
      <label class="checkbox">Pending
        <input type="checkbox" name="show_pending" checked>
        <span class="checkmark"></span>
      </label>
      <label class="checkbox">Paid
        <input type="checkbox" name="show_paid" checked>
        <span class="checkmark"></span>
      </label>
      <form id="daterange">
        <h3>By Due Date:</h3>
        <label>From: </label><input type="date" name="datestart" value=""><label> to </label><input type="date" name="dateend" value=""><br>
        <input type="submit" value="Update">
      </form>
    </div>

    <article id="dashboard">
      <div id="billtable">
        <div id="fieldheader">
          <div name="name" class="namecell">Name<i class="material-icons">keyboard_arrow_down</i></div>
          <div name="outstanding" class="outstandingcell">Outstanding<i class="material-icons"></i></div>
          <div name="duedate" class="duecell">Due date<i class="material-icons"></i></div>
          <div class="paycell"></div>
        </div>
        <img src='images/loading.gif' class='loading' style='display: none'>
        <div id="billtablebody">

        </div>
      </div>
    </article>
    <?php include "elements/popup.php" ?>
  </body>
</html>
