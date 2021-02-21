function checkNotifications() {
  $.get("elements/feed.php", function(data) {
    try {
      response = JSON.parse(data);
      if (response.unread_count > 0){
        $("#feedamount").attr("hidden",false);
        if (response.unread_count < 10) {
          $("#feedamount label").html(response.unread_count);
        } else {
          $("#feedamount label").html("+9");
        }
      } else {
        $("#feedamount").attr("hidden",true);
      }
      $(".feedpanel:first").html(response.content);
    } catch (e) {
      console.log(data);
    }
  });
}

var alertQueue = [];

function checkAlerts() {
  $.get("elements/getalerts.php", function(data) {
    response = JSON.parse(data);
    if (response !== null){
      response.forEach(function (alertText) {
        alertQueue.push(alertText);
      });
      if ($("#popupalert").toArray().length == 0) {
        showAlertQueue();
      }
    }
  });
}

function showAlertQueue() {
  // If there are messages in queue and pop-up is not already displayed
  if (alertQueue.length > 0 && $(".shade").css("display") === "none") {
    message = alertQueue.shift();
    $(".popup").append("<div class='popupcontainer' id='popupalert'><h3>"+message+"</h3><input type='button' value='Close Message'></div>");
    popIn($("#popupalert"), function () {
      $("#popupalert").find("input[type='button']").off();
      $("#popupalert").find("input[type='button']").on("click", function () {
        popOut(function () {
          $("#popupalert").remove();
          setTimeout(showAlertQueue, 500);
        });
      });
    });
  // If there are messages in queue, but pop-up is already being displayed
  } else if (alertQueue.length > 0 && $(".shade").css("display") !== "none") {
    checkIfNoPopup = setInterval(function () {
      if ($(".shade").css("display") === "none"){
        showAlertQueue();
        clearInterval(checkIfNoPopup);
      }
    }, 1000);
  // If there are no more messages in queue
  } else {
    return;
  }
}

$(document).ready(function () {
  $.get("elements/feed.php", function(data) {
    checkNotifications();
    checkAlerts();
    setInterval(checkNotifications, 10000);
    setInterval(checkAlerts, 10000);
  });

});
