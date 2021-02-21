function updateScreen(){
  var vh = $(window).height();
  var articleHeight = vh - $("nav").outerHeight() - $("header").outerHeight();
  $("article").outerHeight(articleHeight);
  $(".sidebar").first().outerHeight(articleHeight);
}

function updateSidebar(){
  if ($(window).width() <= 1200){ // Toggle-able sidebar
    $(".sidebar").first().css("position", "fixed");
    if (!showSideBar){
      $(".sidebar").first().hide();
      $("#sidebar_button").find("i").html("menu")
    }
    $("#sidebar_button").find("a:first").attr("hidden", false);
  } else { // Fixed sidebar
    $(".sidebar").first().css("position", "static");
    $(".sidebar").first().show();
    $("#sidebar_button").find("a:first").attr("hidden", true);
    showSideBar = false;
  }
}

$(document).ready(function(){
  updateScreen();
  updateSidebar();
});
$(window).resize(function(){
  updateScreen();
  updateSidebar();
});
