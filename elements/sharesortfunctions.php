<?php

  class NameComparator{

    public function compareTo($share1, $share2){
      return strcmp($share1->getName(), $share2->getName());
    }
  }



  class OutstandingComparator{

    public function compareTo($share1, $share2){
      return $share1->getOutstandingAmount() - $share2->getOutstandingAmount();
    }
  }



  class DueDateComparator{

    public function compareTo($share1, $share2){
      $share1Date = $share1->getDueDate();
      $share2Date = $share2->getDueDate();

      if ($share1Date->isBefore($share2Date)){
        return -1;
      } elseif ($share1Date->isAfter($share2Date)){
        return 1;
      } else {
        return 0;
      }
    }
  }


  $comp = null;
  function setComparator($field){
    global $comp;
    switch($field){
      case "name":
        $comp = new NameComparator();
        break;
      case "outstanding":
        $comp = new OutstandingComparator();
        break;
      case "duedate":
        $comp = new DueDateComparator();
        break;
    }
  }


  function ascComparator($share1, $share2){
    global $comp;
    return $comp->compareTo($share1, $share2);
  }


  function descComparator($share1, $share2){
    global $comp;
    return -1*$comp->compareTo($share1, $share2);
  }


  function sortShares($shareArray, $flow){
    switch ($flow) {
      case 'asc':
        usort($shareArray, "ascComparator");
        return $shareArray;

      case 'desc':
        usort($shareArray, "descComparator");
        return $shareArray;
    }
  }


  // function sortAsc($shareArray){
  //   if (count($shareArray) <= 1){
  //     return $shareArray;
  //   }
  //
  //   $mid = intval((count($shareArray)-1)/2);
  //   $leftArray = sortAsc(array_slice($shareArray, 0, $mid));
  //   $rightArray = sortAsc(array_slice($shareArray, $mid));
  //   if ($comp->compareTo(end($leftArray), $rightArray[0]) <= 0){
  //     return $shareArray;
  //   }
  //
  //   $i = 0;
  //   while (count($leftArray) > 0 && count($rightArray) > 0){
  //     if ($comp->compareTo($leftArray[0], $rightArray[0]) <= 0){
  //       $shareArray[$i] = array_shift($leftArray);
  //       $i++;
  //     } else {
  //       $shareArray[$i] = array_shift($rightArray);
  //       $i++;
  //     }
  //   }
  //   while (count($leftArray) > 0) {
  //     $shareArray[$i] = array_shift($leftArray);
  //     $i++;
  //   }
  //   while (count($rightArray) > 0) {
  //     $shareArray[$i] = array_shift($rightArray);
  //     $i++;
  //   }
  //   return $shareArray;
  // }
  //
  //
  // function sortDesc($shareArray){
  //   if (count($shareArray) <= 1){
  //     return $shareArray;
  //   }
  //
  //   $mid = intval((count($shareArray)-1)/2);
  //   $leftArray = sortDesc(array_slice($shareArray, 0, $mid));
  //   $rightArray = sortDesc(array_slice($shareArray, $mid));
  //   if ($comp->compareTo(end($leftArray), $rightArray[0]) >= 0){
  //     return $shareArray;
  //   }
  //
  //   $i = 0;
  //   while (count($leftArray) > 0 && count($rightArray) > 0){
  //     if ($comp->compareTo($leftArray[0], $rightArray[0]) >= 0){
  //       $shareArray[$i] = array_shift($leftArray);
  //       $i++;
  //     } else {
  //       $shareArray[$i] = array_shift($rightArray);
  //       $i++;
  //     }
  //   }
  //   while (count($leftArray) > 0) {
  //     $shareArray[$i] = array_shift($leftArray);
  //     $i++;
  //   }
  //   while (count($rightArray) > 0) {
  //     $shareArray[$i] = array_shift($rightArray);
  //     $i++;
  //   }
  //   return $shareArray;
  // }
?>
