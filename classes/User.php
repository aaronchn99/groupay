<?php
  $cwd = dirname(__FILE__);
  require $cwd."/Group.php";
  require $cwd."/Share.php";

  class User{
    private $id;
    private $username;
    private $firstname;
    private $lastname;
    private $email;
    private $emailAlert;
    private $browserAlert;
    private $group;
    private $isGroupAdmin;
    private $shares;

    public function __construct($id, $usrn, $fstn, $lstn, $email, $emailalrt, $brsralrt, $groupid=null){
      $this->id = $id;
      $this->username = $usrn;
      $this->firstname = $fstn;
      $this->lastname = $lstn;
      $this->email = $email;
      $this->emailAlert = $emailalrt;
      $this->browserAlert = $brsralrt;

      if ($groupid === null){
        $this->group = null;
        $this->isGroupAdmin = false;
      } else {
        $this->group = $this->findGroup($groupid);
        if ($this->checkAdmin()){
          $this->isGroupAdmin = true;
        } else {
          $this->isGroupAdmin = false;
        }
      }
      $this->shares = $this->findShares();
    }

    private function findGroup($groupid){
      // Query Group data
      $db = new Database();
      $query = "SELECT * FROM Groups WHERE GroupID = :groupid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":groupid", $groupid, SQLITE3_INTEGER);
      $groupData = $db->stmtQuerySingle($stmt);
      // Parse group data
      $name = $groupData["GroupName"];
      $ownerid = $groupData["OwnerID"];
      // Return group object
      return new Group($groupid, $name, $ownerid);
    }

    private function checkAdmin(){
      return ($this->id == $this->group->getOwnerId());
    }

    private function findShares(){
      // Query for bill shares
      $db = new Database();
      $query = "SELECT * FROM Shares WHERE PayerID = :id";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":id", $this->id, SQLITE3_INTEGER);
      $results = $stmt->execute();

      // Process Shares
      $shares = array();
      while($row = $results->fetchArray()){
        // Create new Share object
        $share = new Share(
          $row["ShareID"],
          $row["BillID"],
          $row["PayerID"],
          $row["PaidAmount"],
          $row["DueAmount"],
          $row["Paid"]
        );
        // Append new share to array of shares
        array_push($shares, $share);
      }
      // Return final array of shares
      return $shares;
    }

    public function getId(){
      return $this->id;
    }

    public function getUsername(){
      return $this->username;
    }

    public function getFirstname(){
      return $this->firstname;
    }

    public function getLastname(){
      return $this->lastname;
    }

    public function getEmail(){
      return $this->email;
    }

    public function canEmailAlert(){
      return $this->emailAlert;
    }

    public function canBrowserAlert(){
      return $this->browserAlert;
    }

    public function getTotal(){
      $total = 0;
      // Iterate through unpaid shares and sum up total outstanding
      foreach($this->shares as $share){
        $total += $share->getOutstandingAmount();
      }
      return $total;
    }

    public function getGroup(){
      return $this->group;
    }

    public function getShares(){
      return $this->shares;
    }

    public function isAdmin(){
      return $this->isGroupAdmin;
    }

    public function updateUser(){

      $db = new Database();
      $query = "SELECT * FROM Users WHERE UserID = :userid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":userid", $this->getId(), SQLITE3_INTEGER);
      $userData = $db->stmtQuerySingle($stmt);

      $this->__construct($this->getId(),
                          $userData["Username"],
                          $userData["FirstName"],
                          $userData["LastName"],
                          $userData["Email"],
                          $userData["EmailAlert"],
                          $userData["BrowserAlert"],
                          $userData["GroupID"]);
    }

    public function updateGroup(){
      $db = new Database();
      // Get user's group id
      $query = "SELECT GroupID FROM Users WHERE UserID = :userid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":userid", $this->id, SQLITE3_INTEGER);
      $groupData = $db->stmtQuerySingle($stmt);
      $groupid = $groupData["GroupID"];

      // Find and create user's Group object, or set as null if not joined
      if ($groupid === null){
        $this->group = null;
      } else {
        $this->group = $this->findGroup($groupid);
        if ($this->checkAdmin()){
          $this->isGroupAdmin = true;
        } else {
          $this->isGroupAdmin = false;
        }
      }
    }

    public function updateShares(){
      $this->shares = $this->findShares();
    }
  }
?>
