$(document).ready(function () {
  $(".sidebar-icon").click(function () {
    if ($(window).width() <= 768) {
      $(".sidebar").toggleClass("sidebar-open");
    }
  });

  $(window).resize(function () {
    if ($(window).width() < 768) {
      $(".sidebar").addClass("sidebar-toggle");
    }
  });
  $(window).resize(function () {
    if ($(window).width() > 768) {
      $(".sidebar").removeClass("sidebar-toggle");
    }
  });
  $("#logout").on("click", function () {
    window.location.href = "/back/logout";
  });
  // $('.check-value').removeAttr('min').removeAttr('max');
  $('.check-value').attr('novalidate', 'novalidate');
  $(".check-value").on("submit", function (event) {
    event.preventDefault();
    $(".result").empty();
    var isFormValid = true;
    $(this)
      .find(".px-3 .py-2")
      .each(function () {
        var checkbox = $(this).find('input[type="checkbox"]');
        if (checkbox.is(":checked")) {
          var input = $(this).find('input[type="number"], input[type="text"]');
          var label = $(this).find("label").text();
          label = label.slice(0, -1);
          input.each(function () {
            if (
              $(this).attr("type") === "number" &&
              ($(this).val() < parseInt($(this).attr("min")) ||
                $(this).val() > parseInt($(this).attr("max")))
            ) {
              console.log(label);
              if (label === "星期" || label === "節次") {
                $(".result").append(
                  "<div>請輸入正確的" + "星期&節次" + "</div>"
                );
              } 
              else {
                
                $(".result").append("<div>請輸入正確的" + label + "</div>");
              }
              isFormValid = false;
            } else if (!$(this).val()) {
              $(".result").append("<div>" + label + "為必填項</div>");
              isFormValid = false;
            }
          });
        } else {
          var input = $(this).find('input[type="number"], input[type="text"]');
          input.each(function () {
            $(this).val("");
          });
        }
      });
    if (isFormValid) {
      this.submit();
    }
  });
  $('.calendar table tbody tr td, .calendar table tfoot tr td').each(function() {
    var spans = $(this).find('span');
    if (spans.length === 1 && spans.text().trim() !== '') {
      $(this).addClass('color-1');
    } else if (spans.length > 1) {
      $(this).addClass('color-4');
    }
  });
  
});
function enroll(CourseID) {
  $.ajax({
    type: "PUT",
    url: "/back/dashboard",
    contentType: "application/json",
    data: JSON.stringify({ CourseID: CourseID }),
    success: function(temp) {
      // temp = JSON.parse(temp);
      // if (Array.isArray(temp)) {
      //   temp.forEach(item => {
      //     let courseID = Number(item.Course_ID);
      //     let timeSlotID = Number(item.Time_Slot_ID);
      //     let week = Math.floor(timeSlotID / 14);
      //     let period = timeSlotID % 14 -1;
      //     let show_td_id = "x" + week + "y" + period;
      //     $('#' + show_td_id).find('span').text(courseID);
      //     $('#' + show_td_id).find('span').first().attr('title', newTitle);
      //   });
      // } else {
      //   console.log('Received data is not an array.');
      // }
    }
  });
}