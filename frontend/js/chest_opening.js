const chestImg = document.getElementById("chest-image");
chestImg.src = "frontend/img/chest.gif";
setTimeout(() => {
  chestImg.src = "frontend/img/chest_end.png";
  document.getElementById("bbl-anouncement").style.visibility = "visible"
}, 2.75 * 1000);
