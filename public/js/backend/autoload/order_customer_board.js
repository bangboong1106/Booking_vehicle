$render_calendar = $("#calendar-" + calendarID);
var isFirstLoad;
var currentPage = 1,
  page_size = 50;
var map;
var markers = [];
var location,
  lat = 21.0031177,
  lng = 105.82014079999999;
var lines = [];
var startDate = void 0,
  endDate = void 0;
var isAutoScroll = false;

$(document).ready(function () {
  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlComboCustomer !== "undefined") {
      cboSelect2.customer(urlComboCustomer);
    }
  }

  if (typeof createCustomerQuickSearch != "undefined") {
    var quickSearch = createCustomerQuickSearch();
    var config = {};
    quickSearch(config).init();
  }

  if ($("#map").length > 0) {
    google.maps.event.addDomListener(window, "load", initMap);
  }

  registerLoadEvent();

  registerScrollToTop();

  registerAutoScrollCalendar();

  registerSearchByStatus();

  registerScroll();

  registerFullscreenCalendar();

  registerOptionDate();

  registerFilter();

  registerDashboardDefault();

  registerShowModal(".trip-detail");

  registerUpdateDocuments();
});

function registerFilter() {
  $("#btnApplyFilter").on("click", function (e) {
    pagingResource(1, true);
  });

  $("#btnCancelFilter").on("click", function (e) {
    var customer = $("#filter_customer_ids");
    customer.empty();
    customer.val("").trigger("change");

    $("#order_no").val("");

    pagingResource(1, true);
  });
}

function initMap() {
  var latlng = new google.maps.LatLng(lat, lng);
  map = new google.maps.Map(document.getElementById("map"), {
    center: latlng,
    zoom: 15,
  });
  getLocation();
}

function getLocation() {
  clearMarkers();
  var latlngShow = new google.maps.LatLng(lat, lng);
  var marker = new google.maps.Marker({
    map: map,
    position: latlngShow,
    draggable: true,
    anchorPoint: new google.maps.Point(0, -29),
  });
  map.setCenter(marker.getPosition());
  markers.push(marker);
}

function clearMarkers() {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }
  markers = [];
}

function loading(isLoading, view) {
  if (isLoading) {
    showLoading();
  } else {
    hideLoading();
  }
}

function eventAfterAllRender(callback) {
  $render_calendar
    .find("button.fc-today-button")
    .removeClass("fc-state-disabled")
    .prop("disabled", false);
}

/*
????ng k?? s??? ki???n v??? l???i resource
 */
function resourceRender(resource, labelTds, bodyTds) {
  labelTds.css("cursor", "pointer");
  labelTds.css("color", "#7265bac4");
  labelTds.addClass("detail-info");
  labelTds.attr("data-id", resource.id);
  labelTds.attr("data-show-url", urlCustomerDetail.replace("-1", resource.id));
  labelTds.click(function (e) {
    showDetailModal(labelTds);
  });
}

// X??? l?? hi???n th??? show modal
function showDetailModal(_element, callback) {
  let id = _element.attr("data-id"),
    url = _element.data("show-url"),
    showModal = $("#modal_show"),
    title = showModal.find(".modal-title"),
    contentContainer = showModal.find(".modal-body"),
    backUrlKey = showModal.find("#back_url_key").val();
  if (id === "" || url === "") {
    return;
  }
  if (showModal.find("#sub_back_url_key").length > 0) {
    backUrlKey = showModal.find("#sub_back_url_key").val();
  }
  sendRequest(
    {
      url: url,
      type: "GET",
      data: {
        id: id,
        back_url_key: backUrlKey,
      },
    },
    function (response) {
      if (!response.ok) {
        return showErrorFlash(response.message);
      }
      if (response.data.deleted != null && response.data.deleted == true) {
        toastr["warning"]("?????i t?????ng ???? b??? x??a.");
      } else if (response.data.auth != null && response.data.auth == true) {
        toastr["warning"]("B???n kh??ng c?? quy???n xem ?????i t?????ng.");
      } else {
        contentContainer.html(response.data.content);
        title.html(response.data.title);
        showModal.modal("show");
        registerAuditing();
        registerOverwidthTitle();
        if (callback) {
          callback();
        }
      }
    }
  );

  showModal
    .on("show.bs.modal", function () {
      $("html").css("overflow-y", "hidden");
    })
    .on("hide.bs.modal", function () {
      $("html").css("overflow-y", "auto");
    });
}

function eventRender(event, element) {
  element.addClass("detail-info");
  element.attr("data-id", event.id);
  element.attr("data-show-url", urlOrderCustomerDetail.replace("-1", event.id));
  element.click(function (e) {
    showDetailModal(element, function () {
      $(".modal-title").text(
        "Th??ng tin chi ti???t ????n h??ng " + $(".code").text()
      );
    });
  });
}

/*
S??? ki???n sau khi render l???i event tr??n fullcalendar
 */
function eventAfterRender(event, element, view) {}

function loadResourceData(isLoadTrip, callback) {
  if (isLoadTrip) {
    loadEventList(callback);
  }
}

function loadEventList(callback, isLoading) {
  isFirstLoad = false;
  var self = this;

  var customerIDs = $("#filter_customer_ids").select2("data")
    ? $("#filter_customer_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",")
    : "";
  var statuses =
    $("#status-listing").val() == "" ? -1 : $("#status-listing").val();
  var orderNo = $("#order_no").val();

  var start = void 0;
  start = self.startDate
    ? self.startDate
    : $render_calendar.fullCalendar("getView").start;

  var end = void 0;
  end = self.endDate
    ? self.endDate
    : moment(
        $render_calendar.fullCalendar("getView").end.format("YYYY-MM-DD")
      ).add(-1, "days");

  var formatStart = start.format("YYYY-MM-DD");
  var formatEnd = end.format("YYYY-MM-DD");

  if (self.startDate && self.endDate) {
    if (formatStart == formatEnd) {
      $render_calendar.fullCalendar("option", "slotDuration", "00:60:00");
      $render_calendar.fullCalendar("gotoDate", start);
      $render_calendar.fullCalendar("changeView", "timelineDay");
    } else {
      var tmpEnd = moment(formatEnd).add(1, "days").format("YYYY-MM-DD");
      $render_calendar.fullCalendar("changeView", "timeline");
      $render_calendar.fullCalendar("option", "slotDuration", { days: 1 });
      $render_calendar.fullCalendar("option", "visibleRange", {
        start: formatStart,
        end: tmpEnd,
      });
    }
  }
  var data = {
    url: urlEvents,
    type: "GET",
    data: {
      customerIDs: customerIDs,
      statuses: statuses,
      orderNo: orderNo,
      start: formatStart,
      end: formatEnd,
      vehiclePageIndex: currentPage,
      vehiclePageSize: page_size,
    },
  };
  var func = function (response) {
    if (!response) return;
    let pager = $("#pager");
    pager.html(response.resources.paginator);

    resources = [];
    response.resources.items.forEach((item) => {
      resources.push({
        id: item.id,
        title: item.full_name,
      });
    });
    $render_calendar.fullCalendar("refetchResources");
    resizeFullCalendarHeight();

    $render_calendar.fullCalendar("removeEvents");
    $render_calendar.fullCalendar("addEventSource", response.events);
    var totalItem = [];

    if (response.group) {
      totalItem = [...response.group];
    }

    $(".counter").each(function () {
      var total = 0;
      if ($(this).data("status") != -1) {
        var model = totalItem.filter((p) => p.status == $(this).data("status"));
        if (model.length > 0) {
          total = model[0].total;
        }
      } else {
        total = totalItem.reduce(function (acc, obj) {
          return acc + obj.total;
        }, 0);
      }
      $(this)
        .prop("Counter", 0)
        .animate(
          {
            Counter: total,
          },
          {
            duration: 500,
            easing: "swing",
            step: function (now) {
              $(this).text(Math.ceil(now));
            },
          }
        );
    });
    if (callback) {
      callback();
    }
  };
  if (isLoading) {
    sendRequest(data, function (response) {
      func(response);
    });
  } else {
    sendRequestNotLoading(data, function (response) {
      func(response);
    });
  }
}

function pagingResource(page, isLoadTrip, callback) {
  currentPage = page;
  loadResourceData(isLoadTrip, callback);
}

function nextPagingResource() {
  pagingResource(currentPage + 1, true);
}

function previousPagingResource() {
  pagingResource(currentPage - 1), true;
}

//X??? l?? s??? ki???n l???c th??ng tin ????n h??ng theo tr???ng th??i
function registerSearchByStatus() {
  $("#wrapper-status input:checkbox").change(function () {
    if ($(this)[0].id === "chkAll") {
      if (!$(this).parent().hasClass("active")) {
        $("#wrapper-status input[type=checkbox]:not(#chkAll)")
          .parent()
          .removeClass("active");
        $("#status-listing").val(-1);
      }
    } else {
      $("#status-listing").val("");

      if (!$(this).parent().hasClass("active")) {
        $("#wrapper-status #chkAll").parent().removeClass("active");

        var listStatus = $.map(
          $("#wrapper-status .active input[type=checkbox]"),
          function (item) {
            return $(item).attr("data-status");
          }
        );
        if (listStatus.length == 0) {
          $("#status-listing").val($(this).attr("data-status"));
        } else {
          $("#status-listing").val(
            listStatus.join(";") + ";" + $(this).attr("data-status")
          );
        }
      } else {
        var value = $(this).attr("data-status");
        var listStatus = $.map(
          $(
            "#wrapper-status .active input[type=checkbox]:not([data-status=" +
              value +
              "])"
          ),
          function (item) {
            return $(item).attr("data-status");
          }
        );
        $("#status-listing").val(
          listStatus.length > 0 ? listStatus.join(";") : -1
        );
      }
    }
    pagingResource(1, true);
  });
}

// ????ng k?? n??t t??y ch???nh ng??y
function registerOptionDate() {
  var self = this;

  $(document).on("click", ".fc-customDate-button", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var activeFcButton = $(".fc-button.fc-state-active");
    $(".fc-button").removeClass("fc-state-active");
    $(this).toggleClass("fc-state-active");

    var dateTimePicker = $(".fc-hiddenDate-button").daterangepicker(
      {
        format: "DD/MM/YYYY",
        startDate: self.startDate ? self.startDate : moment().startOf("month"),
        endDate: self.endDate ? self.endDate : moment().endOf("month"),
        dateLimit: {
          days: 31,
        },
        showDropdowns: false,
        showWeekNumbers: true,
        timePicker: false,
        opens: "left",
        drops: "down",
        buttonClasses: ["btn", "btn-sm"],
        applyClass: "btn-success",
        cancelClass: "btn-secondary",
        separator: " to ",
        locale: {
          applyLabel: "Ch???n",
          cancelLabel: "H???y",
          fromLabel: "T???",
          toLabel: "?????n",
          customRangeLabel: "T??y ch???n",
          daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          monthNames: [
            "Th??ng 1",
            "Th??ng 2",
            "Th??ng 3",
            "Th??ng 4",
            "Th??ng 5 ",
            "Th??ng 6",
            "Th??ng 7",
            "Th??ng 8",
            "Th??ng 9",
            "Th??ng 10",
            "Th??ng 11",
            "Th??ng 12",
          ],
          firstDay: 1,
        },
      },
      function (start, end, label) {
        self.startDate = start;
        self.endDate = end;
        loadEventList();
      }
    );
    var dateRangePicker = $(this);
    $(dateTimePicker).on("cancel.daterangepicker", function (ev, picker) {
      activeFcButton.addClass("fc-state-active");
      dateRangePicker.removeClass("fc-state-active");
    });
    $(dateTimePicker).on("outsideClick.daterangepicker", function (ev, picker) {
      picker.show();
    });

    $(dateTimePicker).trigger("click");
  });

  $(document).on("click", ".fc-customTwoWeekDate-button", function (e) {
    e.preventDefault();
    e.stopPropagation();
    loadTwoWeek();
  });
}

// Load d??? li???u trong 2 tu???n
function loadTwoWeek() {
  var activeFcButton = $(".fc-button.fc-state-active");
  $(".fc-button").removeClass("fc-state-active");
  $(".fc-customTwoWeekDate-button").toggleClass("fc-state-active");

  var lastDayOfCurrentWeek = moment().endOf("isoWeek");
  var beginDayOfCurrentWeek = moment().subtract(1, "week").startOf("isoWeek");

  self.startDate = beginDayOfCurrentWeek;
  self.endDate = lastDayOfCurrentWeek;
  pagingResource(1, true, function () {
    var activeFcButton = $(".fc-button.fc-state-active");
    $(".fc-button").removeClass("fc-state-active");
    $(".fc-customTwoWeekDate-button").toggleClass("fc-state-active");
  });
}

// ????ng k?? t???i th??ng tin chuy???n xe
function registerLoadEvent() {
  var self = this;
  $(document).on("click", ".fc-today-button", function () {
    self.startDate = self.endDate = void 0;
    loadEventList(null, true);
  });

  $(document).on("click", ".fc-prev-button", function () {
    self.startDate = self.endDate = void 0;
    if ($render_calendar.fullCalendar("getView").type === "timeline") {
      self.startDate = moment(
        $render_calendar.fullCalendar("getView").start.format("YYYY-MM-DD")
      ).add(-14, "days");
      self.endDate = moment(
        $render_calendar.fullCalendar("getView").end.format("YYYY-MM-DD")
      ).add(-14, "days");
    }
    loadEventList(null, true);
  });

  $(document).on("click", ".fc-next-button", function () {
    self.startDate = self.endDate = void 0;
    if ($render_calendar.fullCalendar("getView").type === "timeline") {
      self.startDate = moment(
        $render_calendar.fullCalendar("getView").start.format("YYYY-MM-DD")
      ).add(14, "days");
      self.endDate = moment(
        $render_calendar.fullCalendar("getView").end.format("YYYY-MM-DD")
      ).add(14, "days");
    }
    loadEventList(null, true);
  });

  $(document).on("click", ".fc-timelineDay-button", function () {
    self.startDate = self.endDate = void 0;
    $render_calendar.fullCalendar("option", "slotDuration", "00:60:00");
    loadEventList(null, true);
  });

  $(document).on(
    "click",
    ".fc-customTimelineWeek-button," +
      ".fc-customTimelineTwoWeek-button," +
      ".fc-customTimelineMonth-button",
    function () {
      self.startDate = self.endDate = void 0;
      $render_calendar.fullCalendar("option", "slotDuration", { days: 1 });
      loadEventList();
    }
  );

  $(document).on("click", ".fc-refreshButton-button", function () {
    loadEventList(null, true);
  });
}

//????ng k?? hi???n th??? fullscreen l???ch
function registerFullscreenCalendar() {
  $(document).on("click", ".fc-fullscreenButton-button", function () {
    var full = $(".full-calendar #calendar-" + calendarID);
    full.toggleClass("full-screen-calendar");
    $(this).find("span").toggleClass("fa-window-maximize");
    $(this).find("span").toggleClass("fa-window-minimize");
    if ($render_calendar.hasClass("full-screen-calendar")) {
      $render_calendar.fullCalendar(
        "option",
        "contentHeight",
        $(window).height() - 80
      );
    } else {
      $render_calendar.fullCalendar(
        "option",
        "contentHeight",
        $(window).height() - 220
      );
    }
    $render_calendar.fullCalendar("rerenderEvents");
  });
}

//????ng k?? hi???n th??? fullscreen l???ch
function registerAutoScrollCalendar() {
  var interval = null;
  $(document).on("click", ".fc-autoScrollButton-button", function () {
    isAutoScroll = !isAutoScroll;
    if (isAutoScroll) {
      toastr.options = {
        closeButton: false,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: "toast-top-center",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
      };
      toastr["success"]("Thi???t l???p t??? ?????ng cu???n th??nh c??ng");
      if (
        $(
          ".fc-time-area.fc-widget-content .fc-scroller-canvas > .fc-content"
        ).height() > $(".fc-time-area.fc-widget-content").height()
      ) {
        interval = setInterval(function () {
          startScroll();
        }, 1000);
      }
      $(".fc-autoScrollButton-button").text("T???t t??? ?????ng cu???n");
    } else {
      toastr["success"]("T???t thi???t l???p t??? ?????ng cu???n th??nh c??ng");
      $(".fc-autoScrollButton-button").text("B???t t??? ?????ng cu???n");
      $(".fc-time-area.fc-widget-content .fc-scroller").stop();
      clearInterval(interval);
    }
  });
}

// ????ng k?? s??? ki???n scroll to top
function registerScrollToTop() {
  $(window).scroll(function () {
    if ($(this).scrollTop() >= 50) {
      // If page is scrolled more than 50px
      $("#return-to-top").css("display", "flex"); // Fade in the arrow
    } else {
      $("#return-to-top").fadeOut(200); // Else fade out the arrow
    }
  });
  $("#return-to-top").click(function () {
    // When arrow is clicked
    $("body,html").animate(
      {
        scrollTop: 0, // Scroll to top of body
      },
      500
    );
  });
}

// ????ng k?? s??? ki???n ????ng popup
function registerClosePopup() {
  $("html").on("click", function (e) {
    $(".popover").each(function () {
      if (
        $(e.target).parents(".fc-time-grid-event").get(0) !==
        $(this).prev().get(0)
      ) {
        $(this).popover("hide");
      }
    });
  });

  $(document).on("click", function (e) {
    $('[data-toggle="popover"],[data-original-title]').each(function () {
      //the 'is' for buttons that trigger popups
      //the 'has' for icons within a button that triggers a popup
      if (
        !$(this).is(e.target) &&
        $(this).has(e.target).length === 0 &&
        $(".popover").has(e.target).length === 0
      ) {
        (
          ($(this).popover("hide").data("bs.popover") || {}).inState || {}
        ).click = false; // fix for BS 3.3.6
      }
    });
  });
}

// ????ng k?? s??? ki???n scroll to top
function registerScroll() {
  $(window).scroll(function () {
    if ($(this).scrollTop() > 1) {
      $(".fc-border-separate thead").addClass("sticky");
    } else {
      $(".fc-border-separate thead").removeClass("sticky");
    }
  });
}

// ????ng k?? s??? ki???n scroll to top
function resizeFullCalendarHeight() {
  if ($render_calendar.hasClass("fs-calendar")) {
    $render_calendar.fullCalendar(
      "option",
      "contentHeight",
      $(window).height() - 120
    );
  } else {
    $render_calendar.fullCalendar(
      "option",
      "contentHeight",
      $(window).height() - 220
    );
  }
}

// ????ng k?? s??? ki???n resize window
$(window).resize(function () {
  resizeFullCalendarHeight();
  $render_calendar.fullCalendar("refetchEvents");
  $render_calendar.fullCalendar("rerenderEvents");
});

// ????ng k?? l???c BDK theo ?????i t??i x??? thu???c quy???n qu???n l??
function registerDashboardDefault() {
  if (systemConfig.viewType === "timelineTwoWeek") {
    loadTwoWeek();
  } else {
    pagingResource(1, true);
  }
}

function animateContent(direction) {
  var animationOffset =
    $(
      ".fc-time-area.fc-widget-content .fc-scroller-canvas > .fc-content"
    ).height() - $(".fc-time-area.fc-widget-content").height();
  if (direction == "up") {
    animationOffset = 0;
  }
  var timer = $render_calendar.fullCalendar("getResources").length * 750;
  $(".fc-time-area.fc-widget-content .fc-scroller").animate(
    { scrollTop: animationOffset + "px" },
    timer
  );
}

function up() {
  animateContent("up");
}

function down() {
  animateContent("down");
}

function startScroll() {
  setTimeout(function () {
    down();
  }, 1000);
  setTimeout(function () {
    up();
  }, 1000);
  setTimeout(function () {
    console.log("wait...");
  }, 3000);
}
// C???p nh???t tr???ng th??i ch???ng t??? tr??n form Chi ti???t
// CreatedBy nlhoang 24/08/2020
function registerUpdateDocuments() {
  $(document).on("click", "#btn-update-documents", function () {
    var id = $(this).parents(".form-info-wrap").attr("data-id");
    var now = moment();
    showLoading();
    sendRequestNotLoading(
      {
        url: urlUpdateDocument,
        type: "POST",
        data: {
          ids: id,
        },
      },
      function (response) {
        if (!response.ok) {
          toastr["error"](response.message);
        } else {
          toastr["success"]("C???p nh???t tr???ng th??i th??nh c??ng");
          $("#btn-update-documents").css("display", "none");
          $("#is_collected_documents").html(
            '<i class="fa fa-check" aria-hidden="true"></i>'
          );
          $("#status_collected_documents").text("???? thu ????? ch???ng t???");
          $("#datetime_collected_documents_reality").text(
            now.format("DD/MM/YYYY HH:mm")
          );
          hideLoading();
        }
      }
    );
  });
}
