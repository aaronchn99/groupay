function checkPasswordAndSubmit(submitMode, password){
  $.post("elements/checkpassword.php",
  {"password":password,
   "mode":submitMode},
  function (data) {
    response = JSON.parse(data);
    if (response.success){
      popOut(function () {
        // Change account settings mode
        if (response.mode === "account"){
          submitForm();
          // Leave group mode
        } else if (response.mode === "leavegroup"){
          $.post("applyaccountsettings.php",{"settings_mode":"leavegroup","today":getTodayString()},function (data) {
            try {
              response = JSON.parse(data);
              if (response.success){
                showMessage($("#message").removeClass("error"), "Successfully saved");
                $("#currentgrouplist").html("<li><label>Currently not in a Group</label>");
                $.get("extraforms/groupsettings.php",function (data) {
                  $("#groupform").slideUp();
                  $("#groupform").html(data);
                  $("#groupform").slideDown();
                });
              }
            } catch (err) {
                showMessage($("#message").addClass("error"), "An error occurred: "+data);
            }
          });
        } else if (response.mode === "changename") {
          $.post("applygroupsettings.php",
          {"action":"changename", "groupname":$("#groupnameform input[name='groupname']").val()},
          function (data) {
            try {
              response = JSON.parse(data);
              if (response.success){
                $("#groupnameform input[name='groupname']").get(0).defaultValue = response.groupname;
                $("#currentgrouplist").html("<li><label>"+response.groupname+"</label><label> - Cannot leave while Admin</label>");
                showMessage($("#message").removeClass("error"), "Successfully saved");
              }
            } catch (err) {
                showMessage($("#message").addClass("error"), "An error occurred: "+data);
            }
          });
        }
      });
    // If password incorrect
    } else {
      showMessage($("#verifypassdiv p.error"), "Incorrect Password");
    }
  });
}

function submitForm() {

  postData = new Object();
  postData.settings_mode = "account";
  postData.username = $("#accountsettings").find("input[name='username']").val();
  postData.firstname = $("#accountsettings").find("input[name='firstname']").val();
  postData.lastname = $("#accountsettings").find("input[name='lastname']").val();
  postData.email = $("#accountsettings").find("input[name='email']").val();
  postData.password = $("#accountsettings").find("input[name='newpassword']").val();
  postData.email_alerts = +$("#accountsettings").find("input[name='email_alerts']").get(0).checked;
  postData.browser_alerts = +$("#accountsettings").find("input[name='browser_alerts']").get(0).checked;

  $.post("applyaccountsettings.php",postData,function (data) {
    response = JSON.parse(data);
    if (response.success){
      $("#accountsettings").find("input[name='username']").get(0).defaultValue = response.username;
      $("#accountsettings").find("input[name='firstname']").get(0).defaultValue = response.firstname;
      $("#accountsettings").find("input[name='lastname']").get(0).defaultValue = response.lastname;
      $("#accountsettings").find("input[name='email']").get(0).defaultValue = response.email;
      $("#accountsettings").find("input[name='email_alerts']").get(0).defaultChecked = +response.email_alerts;
      $("#accountsettings").find("input[name='browser_alerts']").get(0).defaultChecked = +response.browser_alerts;

      showMessage($("#message").removeClass("error"), "Successfully saved");
    } else {
      showMessage($("#message").addClass("error"), "An error occurred: "+data);
    }
  });
}

function passwordPrompt(mode, onAuth=function(){return;}) {

  // Show pop-up and display password prompt
  popIn($("#verifypassdiv"));
  if (mode !== "") {  // If mode specified
    // Set the settings mode
    $("#verifypassdiv").find("input[name='mode']").val(mode);
    $("#verifypassdiv").find("form").off("submit");
    $("#verifypassdiv").find("form").on("submit", function () {
      event.preventDefault();

      password = $("#verifypassdiv").find("input[name='password']").val();
      mode = $("#verifypassdiv").find("input[name='mode']").val();
      $("#verifypassdiv").find("input[name='password']").val("");
      checkPasswordAndSubmit(mode, password);
    });
  } else {  // Otherwise, call onAuth on authentication
    $("#verifypassdiv").find("form").off("submit");
    $("#verifypassdiv").find("form").on("submit", function () {
      event.preventDefault();

      password = $("#verifypassdiv").find("input[name='password']").val();  // Get password
      $("#verifypassdiv").find("input[name='password']").val("");
      // Authenticate user
      $.post("elements/checkpassword.php",
        {"password":password,
         "mode":""},
        function (data) {
          response = JSON.parse(data);
          if (response.success){
            popOut(onAuth);
          } else {
            showMessage($("#verifypassdiv p.error"), "Incorrect Password");
          }
        }
      );
    });
  }

}

function showMessage(jQElement, message) {
  jQElement.html(message);
  jQElement.slideDown();
  $("article").scrollTop(0);
  setTimeout(function () {
    jQElement.slideUp();
  }, 3000);
}


$(document).ready(function (){

  // Insert a password check form if there aren't already one
  $.get("extraforms/passcheckform.php", function (data) {
    if ($("#verifypassdiv").length === 0){
      $(".popup").append(data);
    }
  });

  $("#accountsettings").on("submit", function () {
    event.preventDefault();

    newpass = $("#accountsettings").find("input[name='newpassword']").val();
    passconfirm = $("#accountsettings").find("input[name='passconfirm']").val();
    if (newpass !== passconfirm){
      showMessage($("#message").addClass("error"), "Invalid: New passwords do not match");
      return false;
    }
    passwordPrompt("account");
  });

  $("input[name='leavegroup']").on("click",function () {
    passwordPrompt("leavegroup");
  });
  // Reset the form
  $("#resetform").on("click", function () {
    $("#accountsettings").get(0).reset();
  });

  // When shaded area clicked, hide pop-up and hide password prompt
  $(".shade").on("click", function () {
    popOut();
  });
  // Prevent popup closing from clicking on the popup
  $(".popup").on("click", function () {
    event.stopPropagation();
  });

});
