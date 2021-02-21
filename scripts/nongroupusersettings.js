function submitNewGroupForm() {
  event.preventDefault();

  postData = new Object();
  postData.groupname = $("#newgroupformdiv form").find("input[name='groupname']").val();
  postData.members = [];
  $("#memberlist").find("input[type='hidden']").toArray().forEach(function (entry) {
    postData.members.push(entry.value);
  });
  postData.admin = $("#newgroupformdiv form").find("input[name='userid']").val();
  postData.today = getTodayString();

  $.post("elements/newgroup.php", postData, function (data) {
    // Checks if successful
    if (data == "success"){
      popOutForm(null, function () {
        $.get("extraforms/groupsettings.php", function (data) {
          groupname = $("#newgroupformdiv form").find("input[name='groupname']").val();
          $("#currentgrouplist li").html("<label>"+groupname+" - Cannot leave while Admin</label>");
          $("#groupform").slideUp();
          $("#groupform").html(data);
          $("#groupform").slideDown();
          showMessage($("#message").removeClass("error"), "New Group created and member invites sent");
        });

      });
    } else {
      popOutForm(null);
      showMessage($("#message").addClass("error"), "An Error Occurred: "+data);
    }
  });
}

function popOutForm(event, onComplete=function(){return;}) {
  try{
    event.stopPropagation();
  }catch (e) {}
  resetNewGroupForm();
  popOut(onComplete);
}

$(document).ready(function () {

  // Fetch and insert the new group popup form
  $.get("extraforms/newgroupform.php", function (data) {
    if ($("#newgroupformdiv").length === 0){
      $(".popup").append(data);
      $("#newgroupformdiv form").off("submit");
      $("#newgroupformdiv form").on("submit", submitNewGroupForm);
    }
  });

  $("input[name='newgroup']").off();
  // When the Create new group button is clicked, show new group form
  $("input[name='newgroup']").on("click", function () {
    popIn($("#newgroupformdiv"));
  });

  $(".shade").off("click", popOutForm);
  $(".shade").on("click", popOutForm);

  $("#joingroupform").off();
  // Join a Group field
  $("#joingroupform").on("submit", function () {
    event.preventDefault();
    postData = new Object();
    postData.action = "join";
    postData.groupid = $("select[name='group']").val();
    postData.today = getTodayString();
    if (postData.groupid !== "select"){
      $.post("applygroupsettings.php", postData, function (data) {
        if (data === "success") {
          showMessage($("#message").removeClass("error"), "Group request sent");
        } else {
          showMessage($("#message"), "Error: "+data);
        }
      });
    } else {
      showMessage($("#message").addClass("error"), "Invalid: Select a Group to join");
    }
  });
});
