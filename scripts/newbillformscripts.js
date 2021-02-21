function onTogglePayer(checkBox){
  memberId = checkBox.name.substring(5);
  if (checkBox.checked){
    if ($("#shares").css("display") == "none"){
      $("#shares").slideDown();
    }
    $("#share"+memberId).find("input").attr("disabled",false);
    $("#share"+memberId).slideDown();
  } else {
    $("#share"+memberId).find("input").attr("disabled",true);
    $("#share"+memberId).slideUp();
    if ($("#shares").find("input[type='range'][disabled!='disabled']").length == 0){
      $("#shares").slideUp();
    }
  }
  resetPayerSliders();
}

function toggleManual(checkBox){
  if (checkBox.checked){
    $("#shares").find("input[type='range']").off("change");
    $("#shares").find("input[type='number']").off("input");
    $("#shares").find("input[type='number']").on("input",function(){
      checkFieldValid(this);
      updateShareValueReverse(this);
    });
    $("#shares").find("input[type='range']").slideUp();
  } else {
    $("#shares").find("input[type='range']").on("change", function(){updateOtherSliders(this);});
    $("#shares").find("input[type='number']").on("input",function(){
      checkFieldValid(this);
      updateShareValueReverse(this);
      updateOtherSliders($(this).siblings().get(0));
    });
    resetPayerSliders();
    $("#shares").find("input[type='range']").slideDown();
  }
}

function resetPayerSliders(){
  activePayerSliders = $("#shares").find("input[type='range'][disabled!='disabled']").toArray();
  totalAmount = $("#newbillform").find("input[name='amount']").val() * 100;
  equalShare = totalAmount/activePayerSliders.length;

  sum = 0;
  activePayerSliders.forEach(function (slider, i) {
    slider.value = equalShare;

    sum += parseInt(slider.value);
    if (sum > totalAmount){
      slider.value = parseInt(slider.value) - (sum - totalAmount);
    } else if (i == activePayerSliders.length - 1 && sum < totalAmount){
      slider.value = parseInt(slider.value) + (totalAmount - sum);
    }

    slider.setAttribute("oldvalue", slider.value);
    updateShareValue(slider);
  });
}

// slider updates field
function updateShareValue(slider){
  sliderVal = $(slider).val();
  $(slider).siblings().val(sliderVal/100);
}

// field updates slider
function updateShareValueReverse(field){
  fieldVal = $(field).val();
  $(field).siblings().val(fieldVal*100);
}

function updateOtherSliders(currentSlider){
  currentSliderVal = parseInt($(currentSlider).val());
  otherPayerSliders = $("#shares").find("input[type='range'][disabled!='disabled'][name!='"+currentSlider.name+"']").toArray();
  deltaShare = (currentSliderVal - currentSlider.getAttribute("oldvalue"))/otherPayerSliders.length;
  currentSlider.setAttribute("oldvalue", currentSliderVal);

  totalAmount = $("#newbillform").find("input[name='amount']").val() * 100;

  var sum = currentSliderVal;
  otherPayerSliders.forEach(function(slider, i){
    slider.value = parseInt(slider.value) - deltaShare;
    sum += parseInt(slider.value);
    if (sum > totalAmount){
      slider.value = parseInt(slider.value) - (sum - totalAmount);
      sum -= (sum - totalAmount)
    } else if (i == otherPayerSliders.length - 1 && sum < totalAmount){
      slider.value = parseInt(slider.value) + (totalAmount - sum);
      sum += (totalAmount - sum)
    }
    updateShareValue(slider);
  });
}

function setTotalAmount(total){
  sliders = $("#shares").find("input[type='range']").toArray();
  fields = $("#shares").find("input[type='number']").toArray();
  sliders.forEach(function (slider) {
    slider.max = total;
  });
  fields.forEach(function (field) {
    field.max = total/100;
  });
}

function checkFieldValid(field){
  fieldString = $(field).val();
  fieldVal = parseInt(fieldString);
  maxVal = parseInt($(field).attr("max"));
  if (fieldVal === NaN || fieldVal <= 0){
    $(field).val(0);
  } else if (fieldVal >= maxVal){
    $(field).val(maxVal);
  }// else if (fieldString.indexOf("e") != -1){
  //   while (fieldString.indexOf("e") != -1){
  //     fieldString.replace("e", "");
  //   }
  //   $(field).val(fieldString);
  // }
}

function validateNewBillForm(){
  sliders = $("#shares").find("input[type='range']").toArray();
  totalAmount = parseInt($("#newbillform").find("input[name='amount']").val());
  dueDate = $("#newbillform").find("input[name='due']").val();

  if ($("#shares").find("input[type='range'][disabled!='disabled']").length == 0){
    $(".error").hide();
    $(".error").html("Invalid: Require at least one payer");
    $(".error").slideDown();
    return false;
  }

  if (totalAmount == 0){
    $(".error").hide();
    $(".error").html("Invalid: Total amount cannot be zero");
    $(".error").slideDown();
    return false;
  }

  sum = 0;
  sliders.forEach(function(slider){
    sum += parseInt(slider.value);
  });
  if (sum != totalAmount*100){
    $(".error").hide();
    $(".error").html("Invalid: Sum of shares not equal to total");
    $(".error").slideDown();
    return false;
  }

  if (parseInt(dueDate.substring(0,4)) == today.getFullYear()){
    if (parseInt(dueDate.substring(5,7)) == today.getMonth()+1){
      if (parseInt(dueDate.substring(8,10)) <= today.getDate()){
        $(".error").hide();
        $(".error").html("Invalid: Date is in the past");
        $(".error").slideDown();
        return false;
      }
    }else if (parseInt(dueDate.substring(5,7)) < today.getMonth()+1){
      $(".error").hide();
      $(".error").html("Invalid: Date is in the past");
      $(".error").slideDown();
      return false;
    }
  }else if (parseInt(dueDate.substring(0,4)) < today.getFullYear()){
    $(".error").hide();
    $(".error").html("Invalid: Date is in the past");
    $(".error").slideDown();
    return false;
  }
}

$(document).ready(function () {
  $("#payers_field").find("input").on("change",function(){onTogglePayer(this);});
  $("#newbillform").find("input[name='amount']").on("input",function(){
    setTotalAmount(this.value*100);
    resetPayerSliders();
  });
  $("#shares").find("input[type='range']").change(function(){updateOtherSliders(this);});
  $("#shares").find("input[type='range']").on("input",function(){updateShareValue(this);});
  $("#shares").find("input[type='number']").on("input",function(){
    checkFieldValid(this);
    updateShareValueReverse(this);
    updateOtherSliders($(this).siblings().get(0));
  });
  $("#shares").find("input[type='checkbox']").on("change",function(){toggleManual(this);});

  $("#payers_field").find("input").trigger("change");
  $("#newbillform").find("input[name='amount']").trigger("input");

  $("#newbillform").find("input[name='today']").val(getTodayString());
  $("#newbillform").find("input[name='due']").val(getTodayString());
  $("#newbillform").submit(function(){
    return validateNewBillForm();
  });
});
