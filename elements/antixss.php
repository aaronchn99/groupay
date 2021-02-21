<?php
  function hsc($string){
    return htmlspecialchars("".$string,ENT_QUOTES,"utf-8");
  }
  function st($string){
    return strip_tags($string);
  }
?>
