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

// $(document).ready(function () {
//   $(".sidebar-icon").click(function () {
//     if ($(window).width() <= 768) {
//       $(".sidebar").animate({
//         width: "toggle"
//       }, 500, function() {
//         // 当侧边栏切换完成后，根据其显示状态调整 .container 的布局
//         adjustGridLayout();
//       });
//     }
//   });
// });

// $(window).resize(function () {
//   adjustGridLayout();
// });

// $("#logout").on("click", function () {
//   window.location.href = "/back/logout";
// });

// // 定义一个函数来调整 .container 的 grid-template-columns
// function adjustGridLayout() {
//   if ($(window).width() > 768) {
//     $(".sidebar").removeAttr("style");
//     $(".container").css('grid-template-columns', 'minmax(200px, 20%) 1fr');
//   } else if (!$(".sidebar").is(":visible")) {
//     // 当侧边栏被隐藏时，让内容区域占据全部宽度
//     $(".container").css('grid-template-columns', '1fr');
//   } else {
//     // 当侧边栏显示时，根据侧边栏宽度调整内容区域的宽度
//     $(".container").css('grid-template-columns', 'auto 1fr');
//   }
// }