var showSideBar = false;
var showFeed = false;

function toggleSidebar(){
  if (showSideBar){
    $(".sidebar").first().slideUp(function () {
      $("#sidebar_button").find(".material-icons").first().html("menu");
      $(this).clearQueue();
      showSideBar = false;
    });
  }else{
    $(".sidebar").first().slideDown(function () {
      $("#sidebar_button").find(".material-icons").first().html("close");
      $(this).clearQueue();
      showSideBar = true;
    });
  }
}

function toggleFeed(){
  if (showFeed){
    $(".feedpanel").first().slideUp(function () {
      $("#feedbutton").find(".material-icons").first().html("notifications_none");
      $(this).clearQueue();
      showFeed = false;
    });
  }else{
    $(".feedpanel").first().slideDown(function () {
      $("#feedbutton").find(".material-icons").first().html("notifications");
      $(".feedpanel").first().scrollTop(0);
      $(this).clearQueue();
      showFeed = true;
    });
  }
}
