function getTodayString() {
  today = new Date();

  todayDateString = today.getFullYear() + "-";
  if (today.getMonth()+1 < 10){
    todayDateString += "0" + (today.getMonth()+1) + "-";
  } else {
    todayDateString += (today.getMonth()+1) + "-";
  }
  if (today.getDate() < 10){
    todayDateString += "0" + today.getDate();
  } else {
    todayDateString += today.getDate();
  }
  return todayDateString;
}
