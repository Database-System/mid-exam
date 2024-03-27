$(document).ready(function () {
  $(".sidebar-icon").click(function () {
    if ($(window).width() <= 768) {
      $(".sidebar").animate(
        {
          width: "toggle",
        },
        500
      );
    }
  });
});
$(window).resize(function () {
  if ($(window).width() > 768) {
    $(".sidebar").removeAttr("style");
  }
});
$("#logout").on("click", function () {
  window.location.href = "/back/logout";
});
