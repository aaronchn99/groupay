<?php
  class Date{
    private $day;
    private $month;
    private $year;

    /* Construct new Date object. Requires:
    - Datestring (Separated with '-' dashes)
    - Date Format (String specifying the order of the year (y), month (m) and day (d))
    */
    public function __construct($datestring, $format){

      switch ($format){
        case "y-m-d":
          $this->parseReverse($datestring);
          break;
        case "d-m-y":
          $this->parseStandard($datestring);
          break;
        case "m-d-y":
          $this->parseAmerican($datestring);
          break;
        default:
          echo "Line 20: Cannot parse datestring";
      }
    }

    public function getDateString($format){
      switch ($format){
        case "y-m-d":
          return $this->outputReverse($format);
          break;
        case "d-m-y":
          return $this->outputStandard($format);
          break;
        case "m-d-y":
          return $this->outputAmerican($format);
          break;
        default:
          echo "Line 69: Cannot output date in format ".$format;
      }
    }

    public function isBefore($date){
      if (intval($this->year) < intval($date->year)){
        return true;
      } elseif (intval($this->year) > intval($date->year)){
        return false;
      } else {
        if (intval($this->month) < intval($date->month)){
          return true;
        } elseif (intval($this->month) > intval($date->month)){
          return false;
        } else {
          if (intval($this->day) < intval($date->day)){
            return true;
          } else {
            return false;
          }
        }
      }
    }

    public function isAfter($date){
      if (intval($this->year) < intval($date->year)){
        return false;
      } elseif (intval($this->year) > intval($date->year)){
        return true;
      } else {
        if (intval($this->month) < intval($date->month)){
          return false;
        } elseif (intval($this->month) > intval($date->month)){
          return true;
        } else {
          if (intval($this->day) <= intval($date->day)){
            return false;
          } else {
            return true;
          }
        }
      }
    }

    // Parses datestring to this Date object
    // d-m-y
    private function parseStandard($date){
      $vals = explode("-", $date);
      $this->day = $vals[0];
      $this->month = $vals[1];
      $this->year = $vals[2];
    }

    // y-m-d
    private function parseReverse($date){
      $vals = explode("-", $date);
      $this->day = $vals[2];
      $this->month = $vals[1];
      $this->year = $vals[0];
    }

    // m-d-y
    private function parseAmerican($date){
      $vals = explode("-", $date);
      $this->day = $vals[1];
      $this->month = $vals[0];
      $this->year = $vals[2];
    }

    // Outputs this date object into a Datestring
    // d-m-y
    private function outputStandard(){
      return $this->day."-".$this->month."-".$this->year;
    }

    // y-m-d
    private function outputReverse(){
      return $this->year."-".$this->month."-".$this->day;
    }

    // m-d-y
    private function outputAmerican(){
      return $this->month."-".$this->day."-".$this->year;
    }
  }
?>
