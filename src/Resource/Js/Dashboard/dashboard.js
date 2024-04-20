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
    } else {
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
  getTotalCredit();
  changeColor();
  buttonDisable();
  $("button[data-course-id]").on("click", function () {
    var courseCode = $(this).data("course-id");
    $(this).prop("disabled", true);
    console.log(courseCode);
    enroll(courseCode);
  });
  Delete_check_course();
  Insert_check_course();
  delete_handle();
  $(".nav-link").click(function (event) {
    countCheckcourse((data) => {
      if (data > 0) {
        refreshcheckcourse();
      } else {
        refreshDoneTable();
      }
    });
    refreshCalendar();
    changeColor();
    buttonDisable();
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
      getTotalCredit();
    },
  });
}

function Insert_check_course() {
  $("button[data-insertcourse-id]").on("click", function () {
    var insertcourseCode = $(this).data("insertcourse-id");
    get_course_credit(insertcourseCode, (data) => {
      let total_credit = parseInt($("#total").text());
      if (total_credit + data.Credits > 30) {
        alert("學分超過上限");
        return;
      }
      get_user_dept(data.courseID, (userDept) => {
        if (userDept !== data.dept) {
          alert("非本系課程");
          return;
        }
        get_course_currentpeople(insertcourseCode, (currentpeople) => {
          if (currentpeople+1 > data.MaxPeople) {
            alert("人數已滿");
            return;
          }
          $(this).prop("disabled", true);
          $.ajax({
            type: "UPDATECOURSE",
            url: "/back/dashboard",
            contentType: "application/json",
            data: JSON.stringify({
              CourseID: data.CourseID,
              NID: $(".info-item").find("span").eq(1).text(),
              check: 2,
            }),
            success: function (temp) {
              countCheckcourse((data) => {
                if (data > 0) {
                  refreshcheckcourse();
                } else {
                  refreshDoneTable();
                }
              });
              refreshCalendar();
              getTotalCredit();
            },
          });
        });
      });
    });
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
      changeColor();
      buttonDisable();
    },
    error: function () {
      console.error("Calendar could not be updated.");
    },
  });
}
function refreshDoneTable() {
  $.ajax({
    url: "/back/getDoneTable",
    type: "GET",
    success: function (data) {
      $("#select-result").html(data);
      delete_handle();
    },
    error: function () {
      console.error("Calendar could not be updated.");
    },
  });
}

function refreshcheckcourse() {
  $.ajax({
    url: "/back/getcheckcourse",
    type: "GET",
    success: function (data) {
      $("#select-result").html(data);
      Insert_check_course();
      Delete_check_course();
    },
    error: function () {
      console.error("getcheckcourse could not be updated.");
    },
  });
}

function buttonDisable() {
  var courseIds = new Set();
  $(".calendar span").each(function () {
    var text = $(this).text().trim();
    if (text === "") {
      return true;
    }
    courseIds.add(text);
  });
  var courseIdsArray = Array.from(courseIds);
  $("button[data-course-id]").each(function () {
    var btnCourseId = $(this).data("course-id").toString();
    if (courseIdsArray.includes(btnCourseId)) {
      $(this).prop("disabled", true);
    } else {
      $(this).prop("disabled", false);
    }
  });
}
function changeColor() {
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
}

function Delete_check_course() {
  $("button[data-deletecourse-id]").on("click", function () {
    var deletecourseCode = $(this).data("deletecourse-id");
    $(this).prop("disabled", true);
    $.ajax({
      url: "/back/dashboard",
      type: "DELETE1",
      dataType: "json",
      data: JSON.stringify({
        CourseID: deletecourseCode,
        NID: $(".info-item").find("span").eq(1).text(),
      }),
      success: function (response) {
        console.log(response);
        if (response === "success") {
          countCheckcourse((data) => {
            if (data > 0) {
              refreshcheckcourse();
            } else {
              refreshDoneTable();
            }
          });
          // refreshDoneTable();
          refreshCalendar();
          // refreshcheckcourse();
          getTotalCredit();
          console.log("delete success");
        } else {
          console.log(response);
          alert("刪除失敗");
        }
      },
    });
  });
}

function delete_handle() {
  $('a[name="delCourse"]').on("click", function (event) {
    event.preventDefault();
    var courseId = $(this).data("course-id");
    get_course_credit(courseId, (data) => {
      let total_credit = parseInt($("#total").text());
      // console.log(data);
      if (total_credit - data.Credits < 9) {
        alert("學分低於下限");
        return;
      }
      $.ajax({
        url: "/back/dashboard",
        type: "DELETE",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify({
          CourseID: data.CourseID,
          NID: $(".info-item").find("span").eq(1).text(),
        }),
        success: function (response) {
          if (response === "success") {
            refreshDoneTable();
            refreshCalendar();
            getTotalCredit();
            console.log("刪除成功");
          } else {
            console.log(response.confirm);
            if (response.confirm == 2) {
              if (confirm("注意!這是必修課程，確定要退選嗎")) {
                $.ajax({
                  url: "/back/dashboard",
                  type: "DELETE",
                  dataType: "json",
                  data: JSON.stringify({
                    CourseID: courseId,
                    NID: $(".info-item").find("span").eq(1).text(),
                    Confirm: 1,
                  }),
                  success: function (response) {
                    console.log(response);
                    if (response === "success") {
                      refreshDoneTable();
                      refreshCalendar();
                      getTotalCredit();
                      console.log("刪除成功");
                    } else {
                      console.log(response);
                      alert("刪除失敗1");
                    }
                  },
                });
              } else {
                refreshDoneTable();
              }
            } else {
              alert("刪除失敗");
            }
          }
        },
        error: function (jqXHR, textStatus, error) {
          console.error("Type: " + textStatus + "\n" + error);
        },
      });
    });
  });
}
function getTotalCredit() {
  $.ajax({
    url: "/back/api",
    type: "PATCH",
    dataType: "json",
    data: JSON.stringify({
      NID: $(".info-item").find("span").eq(1).text(),
      function: "getTotalCreadits",
    }),
    contentType: "application/json",
    success: function (data) {
      $("#total").text(data);
    },
    error: function (jqXHR, textStatus, error) {
      console.error("Type: " + textStatus + "\n" + error);
    },
  });
}
function countCheckcourse(callback) {
  $.ajax({
    url: "/back/api",
    type: "PATCH",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify({
      NID: $(".info-item").find("span").eq(1).text(),
      function: "countCheckcourse",
    }),
    success: function (data) {
      callback(data);
    },
    error: function (jqXHR, textStatus, error) {
      console.error("Can't count check course.");
      console.error("Type: " + textStatus + "\n" + error);
    },
  });
}
function get_course_credit(courseID, callback) {
  $.ajax({
    url: "/back/api",
    type: "POST",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify({
      courseID: courseID,
      function: "get_course_credit",
    }),
    success: function (data) {
      callback(data);
      // console.log(data);
    },
    error: function (jqXHR, textStatus, error) {
      console.error("Can't get course credit.");
      console.error(+"Type: " + textStatus + "\n" + error);
    },
  });
}
function get_user_dept(courseID, callback) {
  $.ajax({
    url: "/back/api",
    type: "POST",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify({
      courseID: courseID,
      function: "get_user_dept",
    }),
    success: function (data) {
      callback(data);
    },
    error: function () {
      console.error("Can't get user dept.");
    },
  });
}
function get_course_currentpeople(courseID, callback) {
  $.ajax({
    url: "/back/api",
    type: "POST",
    dataType: "json",
    contentType: "application/json",
    data: JSON.stringify({
      courseID: courseID,
      function: "get_course_currentpeople",
    }),
    success: function (data) {
      callback(data);
      console.log(data);
    },
    error: function (jqXHR, textStatus, error) {
      console.error("Can't get current people.");
      console.error(+"Type: " + textStatus + "\n" + error);
    },
  });
}
