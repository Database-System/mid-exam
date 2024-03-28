$(document).ready(function () {
  $(".sidebar-icon").click(function () {
    if ($(window).width() <= 768) {
      $(".sidebar").toggleClass("sidebar-open");
    }
  });

  $(window).resize(function () {
    if ($(window).width() > 768) {
      $(".sidebar").removeClass("sidebar-open");
    }
  });
  $("#logout").on("click", function () {
    window.location.href = "/back/logout";
  });
});
