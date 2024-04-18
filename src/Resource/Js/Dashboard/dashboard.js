$(document).ready(pageInit());
function pageInit() {
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
  $(".check-value").attr("novalidate", "novalidate");
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
              } else {
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
  $(".calendar table tbody tr td, .calendar table tfoot tr td").each(
    function () {
      var spans = $(this).find("span");
      if (spans.length === 1 && spans.text().trim() !== "") {
        $(this).addClass("color-1");
      } else if (spans.length > 1) {
        $(this).addClass("color-4");
      }
    }
  );
  buttonDisable();
  $("button[data-course-id]").click(function () {
    var courseCode = $(this).data("course-id");
    $(this).prop("disabled", true);
    enroll(courseCode);
  });
}
function enroll(courseCode) {
  $.ajax({
    type: "PUT",
    url: "/back/dashboard",
    contentType: "application/json",
    data: JSON.stringify({
      CourseID: courseCode,
      NID: $(".info-item").find("span").eq(1).text(),
      check: 1,
    }),
    success: function (temp) {
      console.log(temp);
      refreshCalendar();
    },
  });
}

function search_Course_id_inTable(CourseID) {
  var found = false;
  $(".calendar table td").each(function () {
    var spans = $(this).find("span");
    if (spans.length >= 1 && spans.text().trim() === CourseID) {
      found = true;
      return false;
    }
  });
  return found;
}
function refreshCalendar() {
  $.ajax({
    url: "/back/getCalendar",
    type: "GET",
    success: function (data) {
      $(".calendar").html(data);
      pageInit();
    },
    error: function () {
      console.error("Calendar could not be updated.");
    },
  });
}

function buttonDisable(){
  var courseIds = new Set();
    $('.calendar span').each(function() {
      var text = $(this).text().trim();
      if (text === "") {
          return true; 
      }
      courseIds.add(text);
    });
    var courseIdsArray = Array.from(courseIds);
    $("button[data-course-id]").each(function() {
        var btnCourseId = $(this).data('course-id').toString();
        if (courseIdsArray.includes(btnCourseId)) {
            $(this).prop('disabled', true);
        }
    });
}