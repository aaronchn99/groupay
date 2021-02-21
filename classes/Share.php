<?php
  $cwd = dirname(__FILE__);
  require $cwd."/Date.php";

  class Share{
    private $id;
    private $billName;
    private $details;
    private $dueDate;   // Date object
    private $payeeId;
    private $payerId;
    private $dueAmount;
    private $paidAmount;
    private $isPaid;

    public function __construct($shareid, $billid, $payerid, $paidamount, $dueamount, $paid){
      $this->id = $shareid;
      $this->payerId = $payerid;
      $this->paidAmount = $paidamount;
      $this->dueAmount = $dueamount;
      $this->isPaid = $paid;

      // Query bill data
      $db = new Database();
      $query = "SELECT * FROM Bills WHERE BillID = :billid";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":billid", $billid, SQLITE3_INTEGER);
      $billData = $db->stmtQuerySingle($stmt);
      // Set Bill data
      $this->billName = $billData["BillName"];
      $this->payeeId = $billData["OwnerID"];
      $this->details = $billData["Details"];
      $this->dueDate = new Date($billData["DueDate"], "d-m-y"); // Create date object for due date
    }


    public function getId(){
      return $this->id;
    }

    public function getName(){
      return $this->billName;
    }

    public function getDetails(){
      return $this->details;
    }

    public function getDueDate(){
      return $this->dueDate;
    }

    public function getDueDateString($format){
      return $this->dueDate->getDateString($format);
    }

    public function getPayeeId(){
      return $this->payeeId;
    }

    public function getPayerId(){
        return $this->payerId;
    }

    public function getPaidAmount(){
      return $this->paidAmount;
    }

    public function getTotalAmount(){
      return $this->dueAmount;
    }

    public function getOutstandingAmount(){
      if ($this->isPaid){
        return 0;
      }
      return $this->dueAmount - $this->paidAmount;
    }

    public function isPaid(){
      return $this->isPaid;
    }

    public function pay($amount){
      $this->paidAmount += $amount; // Add to paid amount
      // If paid amount exceeds due amount, share is paid
      if ($this->paidAmount >= $this->dueAmount || $this->getOutstandingAmount() < 1){
        $this->isPaid = true;
      }
      // Query to update paid amount and paid fields
      $db = new Database();
      $query = "UPDATE Shares SET PaidAmount = :paidamount, Paid = :ispaid WHERE ShareID = :shareid;";
      $stmt = $db->prepare($query);
      $stmt->bindValue(":paidamount", $this->paidAmount, SQLITE3_INTEGER);
      $stmt->bindValue(":ispaid", $this->isPaid, SQLITE3_INTEGER);
      $stmt->bindValue(":shareid", $this->id, SQLITE3_INTEGER);
      $stmt->execute();
    }
  }
?>
