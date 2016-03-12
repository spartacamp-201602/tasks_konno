$(function() {
    $("#datepicker").datepicker();
});

$(function() {
    $(document).foundation();
});


// left-side-navの高さを常に画面の最大にする
$(document).ready(function () {
  hsize = $(window).height();
  $(".left-side-nav").css("height", hsize + "px");
  // tasks領域は（画面の高さ - ヘッダーツールバーの高さ）とする
  $(".right-main-tasks").css("height", (hsize - 50) + "px");
});

$(window).resize(function () {
  hsize = $(window).height();
  $(".left-side-nav").css("height", hsize + "px");
  // tasks領域は（画面の高さ - ヘッダーツールバーの高さ）とする
  $(".right-main-tasks").css("height", (hsize - 50) + "px");
});