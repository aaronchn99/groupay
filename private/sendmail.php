<?php
  if (isset($_POST["email"])){
    mail($_POST["email"],$_POST["subject"],$_POST["body"],"From:Groupay");
    echo "<h1>Email Sent</h1>";
  }
?>
<form action="sendmail.php" method="post">
  <input type="email" name="email" placeholder="Enter Email"><br>
  <input type="text" name="subject" placeholder="Enter Subject"><br>
  <textarea name="body" placeholder="Enter Content"></textarea><br>
  <input type="submit" value="Send Email">
</form>
