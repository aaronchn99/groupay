function popIn(jQContent, onComplete=function(){return;}) {
  $(".shade").css("display","flex");
  jQContent.css("display","block");
  setTimeout(function () {
    $(".popup").css("top", "0vh");
    onComplete();
  }, 100);
}

function popOut(onComplete=function(){return;}) {
  $(".popup").css("top", "-70vh");
  setTimeout(function () {
    $(".popupcontainer").css("display","none");
    $(".shade").css("display","none");
    onComplete();
  }, 1000);
}
