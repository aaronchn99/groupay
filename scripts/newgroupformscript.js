function searchNewGroupUsers(query) {
  if (query !== "") {
    $.post("elements/searchusers.php",
      {"membersearch":query},
      function (data) {
        try {
          results = JSON.parse(data);
          $("#membersearch .items").html("");
          if (results.length === 0){
            $("#membersearch .items").html("<div>No available users found</div>");
          } else {
            results.forEach(function (entry) {

              alreadySelected = false;
              $("#memberlist li input").toArray().forEach(function (listItem) {
                if (listItem.value == entry.userid){
                  alreadySelected = true;
                }
              });

              if (!alreadySelected){
                $("#membersearch .items").append("<div><h3>"+entry.fullname+"</h3><label>"+entry.username+"</label><input type='hidden' value="+entry.userid+"></div>");
              }

            });
            $("#membersearch .items div").off("click");
            $("#membersearch .items div").on("click", function () {
              event.stopPropagation();
              userid = $(this).find("input").val();
              fullname = $(this).find("h3").html();
              $("#newgroupformdiv #memberlist").append("<li><label>"+fullname+"</label><input type='hidden' value="+userid+"><input type='button' value='Remove'></li>");

              $("#newgroupformdiv #memberlist input[type='button']").off("click");
              $("#newgroupformdiv #memberlist input[type='button']").on("click", function () {
                $(this).parent().remove();
              });

              closeSuggestList();
            });
          }
        } catch (err){
          console.log(err.message+"\n"+data);
        }
      }
    );
  } else {
    $("#membersearch .items").html("<div>Search by full names, last names, or usernames</div>");
  }
}

function resetNewGroupForm() {
  $("#newgroupformdiv input[name='groupname']").val("");
  $("#membersearch input").val("");
  $("#newgroupformdiv #memberlist").html("");

}

function closeSuggestList() {
  $("#membersearch .items").html("");
  $(".shade").off("click");
  $(".shade").on("click", popOutForm);
}

$(document).ready(function () {

  $("#membersearch input").off("input");
  $("#membersearch input").on("input", function () {
    searchNewGroupUsers($("#membersearch input").val());
  });

  $("#membersearch input").off("focus");
  $("#membersearch input").on("focus", function () {
    searchNewGroupUsers($("#membersearch input").val());
    $(".shade").off("click");
    $(".shade").on("click", closeSuggestList);
  });

  $("#membersearch").off("click");
  $("#membersearch").on("click", function () {
    event.stopPropagation();
  });

  $(".popup").off("click", closeSuggestList);
  $(".popup").on("click", closeSuggestList);
});
