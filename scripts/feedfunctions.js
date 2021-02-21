function deleteMessage(line){
  $.get('deletemessage.php?line='+line, function(){ $('#line'+line).slideUp(function(){checkNotifications();});});
}

// Function called when admin accepts a user's join request to his group
function acceptRequest(userid, line) {
  postData = new Object();
  postData.userid = userid;
  postData.action = "joinaccept";
  postData.today = getTodayString();
  messageId = line;
  $.post("applygroupsettings.php", postData, function (data) {
    if (data === "success") {
      $("#line"+messageId).html("Request Accepted");
      deleteMessage(messageId);
    } else {
      console.log("data");
    }
  });
}

// Function called when admin declines a user's join request to his group
function declineRequest(userid, line) {
  postData = new Object();
  postData.userid = userid;
  postData.action = "joindecline";
  postData.today = getTodayString();
  messageId = line;
  $.post("applygroupsettings.php", postData, function (data) {
    if (data === "success") {
      $("#line"+messageId).html("Request Declined");
      deleteMessage(messageId);
    } else {
      console.log("data");
    }
  });
}

// Function called when user accepts invite to group
function acceptInvite(groupid, line){
  postData = new Object();
  postData.groupid = groupid;
  postData.action = "inviteaccept";
  postData.today = getTodayString();
  messageId = line;
  $.post("applygroupsettings.php", postData, function (data) {
    if (data === "success") {
      $("#line"+messageId).html("<p>Invite Accepted</p>");
      deleteMessage(messageId);
    } else {
      console.log(data);
    }
  });
}

// Function called when user declines invite to group
function declineInvite(groupid, line){
  postData = new Object();
  postData.groupid = groupid;
  postData.action = "invitedecline";
  postData.today = getTodayString();
  messageId = line;
  $.post("applygroupsettings.php", postData, function (data) {
    if (data === "success") {
      $("#line"+messageId).html("<p>Invite Declined</p>");
      deleteMessage(messageId);
    } else {
      console.log("data");
    }
  });
}

$(document).ready(function () {

  $(".feedpanel li").on("mouseenter", function () {
    alertid = parseInt($(this).attr("id").substring(4));
    if ($("#line"+alertid).hasClass("unread")) {
      $.get("elements/setalertread.php?alertid="+alertid, function (data) {
        response = JSON.parse(data);
        alertid = response.readalert;
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
        $("#line"+alertid).removeClass("unread");
      });
    }
  });

  // $("document").off("click");
  // $("document").on("click", ":not(.feedpanel)", function () {
  //   $(".feedpanel").first().slideUp(function () {
  //     $("#feedbutton").find(".material-icons").first().html("notifications_none");
  //     $(this).clearQueue();
  //     showFeed = false;
  //   });
  // });
});
