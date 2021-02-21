function toggleBillPanel(row){
  $("#sharepanel"+(row.attr("id")).substring(5)).slideToggle(function (){
    if ($("#sharepanel"+(row.attr("id")).substring(5)).css("display") === "none") {
        $(row).css("background-color", "");
    }
    $(this).clearQueue();
  });
  $(row).css("background-color", "rgba(0,0,0,0.3)");
}

today = new Date();

function checkDatePast(dateString){
  day = parseInt(dateString.substring(0,2));
  month = parseInt(dateString.substring(3,5))-1;
  year = parseInt(dateString.substring(6));

  if (year < today.getFullYear()){
    return true;
  } else if (year > today.getFullYear()){
    return false;
  } else {
    if (month < today.getMonth()){
      return true;
    } else if (month > today.getMonth()){
      return false;
    } else {
      if (day < today.getDate()){
        return true;
      } else {
        return false;
      }
    }
  }
}

function checkDateIsToday(dateString){
  day = parseInt(dateString.substring(0,2));
  month = parseInt(dateString.substring(3,5))-1;
  year = parseInt(dateString.substring(6));

  if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()){
    return true;
  }
  return false;
}

function updateDateLabels() {
  $(".billrows").toArray().forEach(function (billrow) {
    if (!$(billrow).hasClass("paid")) {
      datestring = $(billrow).find(".duecell").html();
      if (checkDatePast(datestring)) {
        $(billrow).find(".duecell").css("color","red");
      } else if (checkDateIsToday(datestring)){
        $(billrow).find(".duecell").css("color","#ff5000");
      } else {
        $(billrow).find(".duecell").css("color","green");
      }
    }
  });
}

function updateBillTable() {
  $("#billtablebody").hide();
  $("#billtable .loading").css("display", "block");

  postData = {
    "sort": "",
    "direction": "",
    "search": $("input[name='searchquery']").val(),
    "paid": +$("input[name='show_paid']").get(0).checked,
    "pending": +$("input[name='show_pending']").get(0).checked,
    "from": $("input[name='datestart']").val(),
    "to": $("input[name='dateend']").val()
  }

  $("#fieldheader div").not(".paycell").toArray().forEach(function (div) {
    if ($(div).children().html() == "keyboard_arrow_down") {
      // Set div's field to ascending order
      postData.sort = $(div).attr("name");
      postData.direction = "asc";

    } else if ($(div).children().html() == "keyboard_arrow_up") {
      // Set div's field to descending order
      postData.sort = $(div).attr("name");
      postData.direction = "desc";

    }
  });

  $.post("elements/generatebilltable.php", postData, function (data) {
    $("#billtablebody").html(data);
    $("#billtable .loading").css("display", "none");
    $("#billtablebody").show();
    updateDateLabels();
    $("#fieldheader div").clearQueue();
  });
}

$(document).ready(
  function (){
    updateBillTable();
    $("#billtable").on("click", ".billrows", function(){toggleBillPanel($(this));});
    $("#billtable").on("click", ".paycell a", function(event){event.stopPropagation();});
    setInterval(updateDateLabels, 1000);

    $(".sidebar input[type='checkbox']").on("change", updateBillTable);
    $(".sidebar input[type='search']").on("input", updateBillTable);
    $("#daterange").on("submit", function () {
      event.preventDefault();
      updateBillTable();
    });
    $("#fieldheader div").not(".paycell").on("click", function () {
      if ($(this).children().html() == "keyboard_arrow_down"){
        $(this).children().html("keyboard_arrow_up");

      } else if ($(this).children().html() == "keyboard_arrow_up"){
        $(this).children().html("keyboard_arrow_down");

      } else {
        $(this).siblings().children().html("");
        $(this).children().html("keyboard_arrow_down");

      }
      updateBillTable();
    });
  }
);
