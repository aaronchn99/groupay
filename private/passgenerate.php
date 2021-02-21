<?php
  if (isset($_GET["password"])){
    $password = $_GET["password"];
    $salt = time();
    $pepper = rand(1000, 10000);
    $hash = sha1($password.$salt.$pepper);
    echo "Password: ".$password."<br>";
    echo "Salt: ".$salt."<br>";
    echo "Pepper: ".$pepper."<br>";
    echo "Hash: ".$hash."<br>";
  }

  echo strncasecmp("He","Hello", 2);
?>
<form action="passgenerate.php">
  <input name="password">
</form>
