function searchUsers(fielddropdown, query) {
  fielddropdown = fielddropdown;
  if (query !== "") {
    $.post("elements/searchusers.php",
      {"membersearch":query},
      function (data) {
        try {
          results = JSON.parse(data);
          $(fielddropdown).find(".items").html("");
          if (results.length === 0){
            $(fielddropdown).find(".items").html("<div>No available users found</div>");
          } else {
            results.forEach(function (entry) {
              $(fielddropdown).find(".items").append("<div><h3>"+entry.fullname+"</h3><label>"+entry.username+"</label><input type='hidden' value="+entry.userid+"></div>");
            });
            $(fielddropdown).find(".items div").off("click");
            $(fielddropdown).find(".items div").on("click", function () {
              event.stopPropagation();
              inviteUser($(this).find("input").val());
              $(fielddropdown).find("input").val("");
              closeSuggestList(fielddropdown);
            });
          }
        } catch (err){
          console.log(err.message+"\n"+data);
        }
      }
    );
  } else {
    $(fielddropdown).find(".items").html("<div>Search by full names, last names, or usernames</div>");
  }
}

function inviteUser(userid) {
  postData = new Object();
  postData.action = "invite";
  postData.memberid = userid;
  postData.today = getTodayString();
  $.post("applygroupsettings.php", postData, function (data) {
    if (data == "success") {
      showMessage($("#message").removeClass(".error"), "Invite sent");
    }
  });
}

function closeSuggestList(fielddropdown) {
  $(fielddropdown).find(".items").html("");
  $(document).off("click");
}

$(document).ready(function () {

  // Removes all previous click handlers for the input fields
  $("#groupnameform").submit(function () {
    event.preventDefault();
    passwordPrompt("changename");
  });

  // Reset the form
  $("#groupnameform input[name='resetname']").off("click");
  $("#groupnameform input[name='resetname']").on("click", function () {
    $("#groupnameform").get(0).reset();
  });

  // Kick user buttons
  $("#currentmembers").find("input[name='kickuser']").off("click");
  $("#currentmembers").find("input[name='kickuser']").on("click", function () {
    kickbutton = this;
    passwordPrompt("", function () {
      postData = new Object();
      postData.action = "kick";
      postData.userid = $(kickbutton).siblings("input[name='userid']").val();
      postData.today = getTodayString();
      $.post("applygroupsettings.php", postData, function (data) {
        if (data == "success") {
          $(kickbutton).parent().remove();
          showMessage($("#message").removeClass(".error"), "User Kicked");
        } else {
          showMessage($("#message").addClass(".error"), "Error: "+data);
        }
      });
    });
  });

  // Set admin buttons
  // $("#currentmembers input[name='setadmin']").off("click");
  // $("#currentmembers input[name='setadmin']").on("click", function () {
  //
  //
  // });

  $("input[name='deletegroup']").off("click");
  $("input[name='deletegroup']").on("click", function () {
    passwordPrompt("", function () {
      postData = new Object();
      postData.action = "deletegroup";
      postData.today = getTodayString();
      $.post("applygroupsettings.php", postData, function (data) {
        if (data == "success") {
          $("#currentgrouplist").html("<li><label>Currently not in a Group</label>");
          $.get("extraforms/groupsettings.php",function (data) {
            $("#groupform").slideUp();
            $("#groupform").html(data);
            $("#groupform").slideDown();
          });
          showMessage($("#message").removeClass(".error"), "Group Deleted");
        } else {
          showMessage($("#message").addClass(".error"), "Error: "+data);
        }
      });
    });
  });

  $("#addmember input").off("input").off("focus");
  $("#addmember input").on("input focus", function () {
    fielddropdown = $(this).parent();
    $(document).off("click");
    $(document).on("click", function () {
      closeSuggestList(fielddropdown);
      $(document).off("click");
    });

    searchUsers($(this).parent(), $(this).val());
  });

  $("#addmember").on("click", function () {
    event.stopPropagation();
  });
});
