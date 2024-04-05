$(document).ready(function () {
  // $("#NID").on("input", function () {
  //   if ($(this).val().trim() !== "") {
  //     $(".ri-mail-line").addClass("d-none");
  //   } else {
  //     $(".ri-mail-line").removeClass("d-none");
  //   }
  // });
  // $("#password").on("input", function () {
  //   if ($(this).val().trim() !== "") {
  //     $(".ri-lock-line").addClass("d-none");
  //   } else {
  //     $(".ri-lock-line").removeClass("d-none");
  //   }
  // });
  $(".ri-eye").click(function () {
    var input = $("#password");
    var icon = $(this);

    if (input.attr("type") === "password") {
      input.attr("type", "text");
      icon.removeClass("ri-eye-off-line").addClass("ri-eye-line");
    } else {
      input.attr("type", "password");
      icon.removeClass("ri-eye-line").addClass("ri-eye-off-line");
    }
  });
});
