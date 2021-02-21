<?php
  class Group{
    private $id;
    private $name;
    private $ownerId;
    private $membersIds;

    public function __construct($groupid, $groupname, $ownerid){
      $this->id = $groupid;
      $this->name = $groupname;
      $this->ownerId = $ownerid;

      $this->members = $this->findMembers();
    }

    private function findMembers(){
      $db = new Database();
      $query = "SELECT UserID FROM Users WHERE GroupID = :groupid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":groupid", $this->id, SQLITE3_INTEGER);
      $members = $stmt->execute();

      $membersArray = array();
      while ($member = $members->fetchArray()){
        $memberId = $member["UserID"];
        array_push($membersArray, $memberId);
      }
      return $membersArray;
    }

    public function getId(){
      return $this->id;
    }

    public function getName(){
      return $this->name;
    }

    public function getOwnerId(){
      return $this->ownerId;
    }

    public function getMembersIds(){
      return $this->members;
    }

    public function updateGroup(){
      $query = "SELECT * FROM Groups WHERE GroupID = :groupid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":groupid", $this->getId(), SQLITE3_INTEGER);
      $groupData = $db->stmtQuerySingle($stmt);

      $this->__construct($this->getId(),
                          $groupData["GroupName"],
                          $groupData["OwnerID"]);
    }

    public function updateMembers(){
      $this->members = $this->findMembers();
    }

    public static function findMemberRecordById($memberid){
      $db = new Database();
      $query = "SELECT * FROM Users WHERE UserID = :memberid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":memberid", $memberid, SQLITE3_INTEGER);
      $member = $db->stmtQuerySingle($stmt);

      return $member;
    }
  }
?>
