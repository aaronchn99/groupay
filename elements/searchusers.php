<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
  $cwd = dirname(__FILE__);
  require $cwd."/../Database.php";

  $db = new Database();
  $search = $_POST["membersearch"];
  $finalResults = array();

  function addResultsToArray($results){
    global $finalResults;
    while ($row = $results->fetchArray()) {
      $entry = array("userid"=>$row["UserID"],"fullname"=>$row["FirstName"]." ".$row["LastName"], "username"=>$row["Username"]);
      if (!in_array($entry, $finalResults, true)){
        array_push($finalResults, $entry);
      }
    }
  }

  // First, search by username
  $query = "SELECT * FROM Users WHERE Username LIKE :search || '%' AND GroupID is null;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":search", $search, SQLITE3_TEXT);
  $results = $stmt->execute();

  addResultsToArray($results);


  // Next, search by last name
  $query = "SELECT * FROM Users WHERE LastName LIKE :search || '%' AND GroupID is null;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":search", $search, SQLITE3_TEXT);
  $results = $stmt->execute();

  addResultsToArray($results);


  // Next, search by first and last name (with space between)
  $query = "SELECT * FROM Users WHERE FirstName || ' ' || LastName LIKE :search || '%' AND GroupID is null;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":search", $search, SQLITE3_TEXT);
  $results = $stmt->execute();

  addResultsToArray($results);


  // Next, search by first and last name (with no space between)
  $query = "SELECT * FROM Users WHERE FirstName || LastName LIKE :search || '%' AND GroupID is null;";
  $stmt = $db->prepare($query);
  $stmt->bindValue(":search", $search, SQLITE3_TEXT);
  $results = $stmt->execute();

  addResultsToArray($results);

  echo json_encode($finalResults);
?>
