<?php
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";
  require "getuser.php";
  $password = $_POST["password"];

  $responseData = array();
  $responseData["mode"] = $_POST["mode"];

  $db = new Database();
  $query = "SELECT PasswordHash, Salt FROM Users WHERE UserID = :userid";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":userid", $user->getId(), SQLITE3_TEXT);
  $userData = $db->stmtQuerySingle($stmt);

  $realHash = $userData["PasswordHash"];
  $salt = $userData["Salt"];

  for ($i=1000;$i<10000;$i++){
    $inputHash = sha1($password.$salt.$i);
    if ($inputHash == $realHash){
      $responseData["success"] = true;
      echo json_encode($responseData);
      exit();
    }
  }
  $responseData["success"] = false;
  echo json_encode($responseData);
  exit();
?>
