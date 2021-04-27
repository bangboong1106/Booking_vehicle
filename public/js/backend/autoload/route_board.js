$render_calendar = $("#calendar-" + calendarID);
var currentPage = 1,
  page_size = systemConfig.vehiclePageSize;
var map;
var markers = [];
var location,
  lat = 21.0031177,
  lng = 105.82014079999999;
var lines = [];
var startDate = void 0,
  endDate = void 0;
var isCustomRender = false;
var totalVehicle = 0;

var NS = "http://www.w3.org/2000/svg";
var svg = document.createElementNS(NS, "svg");
var newLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
var filterSearchEVehicleTeamxceptIds = [];
var filterSearchCustomerExceptIds = [];

$(document).ready(function () {
  initializeMap();

  registerCombo();

  registerQuickSearch();

  registerDragEvent();

  activeShowOrderAction($("#external-events-listing"));

  registerLoadTrip();

  registerAutoLoadTrip();

  registerClosePopup();

  registerTogglePanel();

  registerSearchVehicle();

  registerSearchInvoice();

  registerSearchByStatus();

  registerFullscreenCalendar();

  registerOptionDate();

  registerFilter();

  registerDashboardDefault();

  registerShowModal(".trip-detail");

  registerHoverPopover();

  registerChooseRoute();
});

function initializeMap() {
  if ($("#map").length > 0) {
    google.maps.event.addDomListener(window, "load", initMap);
  }
}

// Khởi tạo combo select2
// CreatedBy nlhoang 12/04/2020
function registerCombo() {
  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlDriverDropdown !== "undefined") {
      cboSelect2.driver(urlDriverDropdown);
    }
    if (typeof urlVehicleDropdown !== "undefined") {
      cboSelect2.vehicle(urlVehicleDropdown);
    }
    if (typeof urlCustomerDropdown !== "undefined") {
      cboSelect2.customer(urlCustomerDropdown);
    }
    if (typeof urlVehicleTeamDropdown !== "undefined") {
      cboSelect2.vehicleTeam(urlVehicleTeamDropdown);
    }
    if (typeof urlLocation !== "undefined") {
      cboSelect2.location(urlLocation, ".select-location-destination");
    }
  }

  $("#filter_vehicle_group_ids").select2({
    allowClear: true,
    placeholder: "Vui lòng chọn chủng loại xe",
  });
}

// Khởi tạo form Tìm kiếm
// CreatedBy nlhoang 12/04/2020
function registerQuickSearch() {
  if (typeof createDriverQuickSearch != "undefined") {
    var driverQuickSearch = createDriverQuickSearch();

    var addOrderSearchDriverExceptIds = [];
    var addOrderDriverConfig = {};
    addOrderDriverConfig.exceptIds = addOrderSearchDriverExceptIds;
    addOrderDriverConfig.searchElement = "add-order-driver-search";
    addOrderDriverConfig.searchType = "element";
    addOrderDriverConfig.tableElement = "table_add_order_drivers";
    addOrderDriverConfig.modalElement = "add_order_driver_modal";
    addOrderDriverConfig.buttonElement = "btn-add-order-driver";
    driverQuickSearch(addOrderDriverConfig).init();

    var chooseVehicleSearchDriverExceptIds = [];
    var chooseVehicleDriverConfig = {};
    chooseVehicleDriverConfig.exceptIds = chooseVehicleSearchDriverExceptIds;
    chooseVehicleDriverConfig.searchElement = "choose-vehicle-driver-search";
    chooseVehicleDriverConfig.searchType = "element";
    chooseVehicleDriverConfig.tableElement = "table_choose_vehicle_drivers";
    chooseVehicleDriverConfig.modalElement = "choose_vehicle_driver_modal";
    chooseVehicleDriverConfig.buttonElement = "btn-choose-vehicle-driver";
    driverQuickSearch(chooseVehicleDriverConfig).init();

    var changeVehicleSearchDriverExceptIds = [];
    var changeVehicleDriverConfig = {};
    changeVehicleDriverConfig.exceptIds = changeVehicleSearchDriverExceptIds;
    changeVehicleDriverConfig.searchElement = "change-vehicle-driver-search";
    changeVehicleDriverConfig.searchType = "element";
    changeVehicleDriverConfig.tableElement = "table_change_vehicle_drivers";
    changeVehicleDriverConfig.modalElement = "change_vehicle_driver_modal";
    changeVehicleDriverConfig.buttonElement = "btn-change-vehicle-driver";
    driverQuickSearch(changeVehicleDriverConfig).init();
  }

  if (typeof createVehicleQuickSearch != "undefined") {
    var vehicleQuickSearch = createVehicleQuickSearch();

    var filterSearchVehicleExceptIds = [];
    var filterVehicleConfig = {};
    filterVehicleConfig.exceptIds = filterSearchVehicleExceptIds;
    filterVehicleConfig.searchElement = "filter-vehicle-search";
    filterVehicleConfig.searchType = "element";
    filterVehicleConfig.tableElement = "table_filter_vehicles";
    filterVehicleConfig.modalElement = "filter_vehicle_modal";
    filterVehicleConfig.buttonElement = "btn-filter-vehicle";
    vehicleQuickSearch(filterVehicleConfig).init();

    var chooseVehicleSearchVehicleExceptIds = [];
    var chooseVehicleVehicleConfig = {};
    chooseVehicleVehicleConfig.exceptIds = chooseVehicleSearchVehicleExceptIds;
    chooseVehicleVehicleConfig.searchElement = "choose-vehicle-vehicle-search";
    chooseVehicleVehicleConfig.searchType = "element";
    chooseVehicleVehicleConfig.tableElement = "table_choose_vehicle_vehicles";
    chooseVehicleVehicleConfig.modalElement = "choose_vehicle_vehicle_modal";
    chooseVehicleVehicleConfig.buttonElement = "btn-choose-vehicle-vehicle";
    vehicleQuickSearch(chooseVehicleVehicleConfig).init();
  }

  if (typeof createVehicleTeamQuickSearch != "undefined") {
    var quickSearch = createVehicleTeamQuickSearch();
    var filterConfig = {};
    filterConfig.exceptIds = filterSearchEVehicleTeamxceptIds;
    quickSearch(filterConfig).init();
  }

  if (typeof createCustomerQuickSearch != "undefined") {
    var quickSearch = createCustomerQuickSearch();
    var filterConfig = {};
    filterConfig.exceptIds = filterSearchCustomerExceptIds;
    quickSearch(filterConfig).init();
  }
}

function registerFilter() {
  $("#btnApplyFilter").on("click", function (e) {
    pagingResource(1, true);
  });

  $("#btnCancelFilter").on("click", function (e) {
    var vehicleTeam = $("#filter_vehicle_team_ids");
    vehicleTeam.empty();
    vehicleTeam.val("").trigger("change");

    var vehicle = $("#filter_vehicle_ids");
    vehicle.empty();
    vehicle.val("").trigger("change");

    var vehicleGroup = $("#filter_vehicle_group_ids");
    vehicleGroup.empty();
    vehicleGroup.val("").trigger("change");

    var customer = $("#filter_customer_ids");
    customer.empty();
    customer.val("").trigger("change");

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
Đăng ký sự kiện vẽ lại resource
 */
function resourceRender(resource, labelTds, bodyTds) {
  labelTds.css("cursor", "pointer");
  labelTds.css("color", "#7265bac4");

  labelTds.on("click", function (event) {
    $.ajax({
      url: urlVehicleDetail + "?id=" + resource.id,
      dataType: "json",
      success: function (data) {
        lat = data.latitude;
        lng = data.longitude;
        getLocation();

        $("#vehicle_volume").text(
          (data.volume == null ? 0 : data.volume) + " m³"
        );
        $("#vehicle_weight").text(
          (data.weight == null ? 0 : data.weight) + " kg"
        );
        $("#vehicle_bag").text(
          (data.length == null ? 0 : data.length) +
            " m x " +
            (data.width == null ? 0 : data.width) +
            " m x " +
            (data.height == null ? 0 : data.height) +
            " m"
        );
        $("#vehicle_primary").text(
          data.primaryDriverName == null ? "" : data.primaryDriverName
        );
        $("#vehicle_secondary").text(
          data.secondaryDriverName == null ? "" : data.secondaryDriverName
        );
        $("#vehicle_detail h4").html("Thông tin của xe " + resource.title);
        $("#vehicle_detail").modal();
      },
    });
  });
  var $address = "";
  if (resource.address != "") {
    var arr = resource.address.split(",");
    var temp = arr[arr.length - 2];
    temp = temp || resource.address;
    $address =
      '<br/><span class="address" style="color: #10509b;"><i class="fa fa-map-marker"></i>' +
      temp +
      "</span>";
  }
  var cell = $(labelTds).find(".fc-cell-text");
  cell.text("(" + resource.index + ") " + cell.text());
  cell.append($address);

  //Đăng ký tooltip khi hover qua trường vị trí
  var address = resource.address;

  if (typeof address != "undefined" && address != "") {
    $($(labelTds).find(".fc-cell-text .address")).popover({
      content: address,
      trigger: "hover",
      placement: "top",
      html: true,
      container: "body",
    });
  }
}

function eventRender(event, element) {
  if (isCustomRender == false) return;

  element
    .addClass("route-detail")
    .data("show-url", urlRouteDetail.replace("-1", event.id))
    .attr("data-id", event.id)
    .attr("id", "source-" + event.id)
    .addClass("source")
    .css("background-color", event.color);

  if (event.is_attachment && event.is_attachment > 0) {
    element.addClass("attachment");
  }
  //Bổ sung cảnh báo khi đơn hàng sắp đến hạn giao
  var s = $(element),
    f = false,
    c1 = "yellow",
    c2 = "red";

  setInterval(function () {
    //Nếu trạng thái là Chờ lấy hàng thì hiển thị cảnh báo
    if (event.status == 3) {
      let start = moment(event.start.format("YYYY-MM-DD HH:mm:ss")).toDate();
      var miniute = systemConfig.notifyVehicle;
      if (
        moment().toDate() < start &&
        start < moment().add(miniute, "m").toDate()
      ) {
        s.css("background-color", f ? c2 : c1);
        s.css("textColor", f ? c1 : c2);
        f = !f;
      }
    }
  }, 3000);
}

// Khởi tạo sự kiện hover thì nối 2 event với nhau
// CreatedBy nlhoang 12/03/2020
function registerHoverPopover() {
  var request;
  $(document).on("mouseenter", ".full-calendar *[data-id]", function () {
    $(".popover").remove();
    var e = $(this);
    e.off("hover");

    var startElement = $("#source-" + e.data("id") + " .fc-content"),
      endElement = $("#destination-" + e.data("id") + "  .fc-content");

    if (startElement.length > 0 && endElement.length > 0) {
      $(startElement).parent().css({
        border: "4px solid darkorange",
        "border-radius": "4px",
      });

      var $wrapEndElement = $(endElement).parent();

      $wrapEndElement
        .attr("data-background-color", $wrapEndElement.css("background-color"))
        .attr("data-z-index", $wrapEndElement.css("z-index"))
        .css({
          "z-index": 9999,
          "background-color": "darkorange",
        })
        .addClass("hover");

      var width = Math.abs(
        endElement.offset().left - startElement.offset().left
      );
      var height = Math.abs(
        endElement.offset().top - startElement.offset().top
      );

      var top = Math.min(
        $(startElement).offset().top,
        $(endElement).offset().top
      );
      var left = Math.min(
        $(startElement).offset().left,
        $(endElement).offset().left
      );

      $(newLine)
        .attr("x1", 0)
        .attr("y1", 0)
        .attr("x2", width)
        .attr("y2", 0)
        .attr("stroke", "darkorange")
        .attr("stroke-width", "4");

      $(svg)
        .attr("id", e.title)
        .attr("class", "leader-line")
        .css({
          top: top,
          left: left,
          width: width,
          height: height,
        })
        .append(newLine);

      $("body").append($(svg));
    }
  });

  $(document).on("mouseleave", ".full-calendar *[data-id]", function () {
    var e = $(this);
    e.off("hover");
    var startElement = $("#source-" + e.data("id") + " .fc-content"),
      endElement = $("#destination-" + e.data("id") + "  .fc-content");

    if (startElement.length > 0 && endElement.length > 0) {
      $(startElement).parent().css({
        border: "none",
      });

      var $wrapEndElement = $(endElement).parent();

      $wrapEndElement
        .css("z-index", $wrapEndElement.attr("data-z-index"))
        .css("background-color", $wrapEndElement.attr("data-background-color"))
        .removeAttr("data-z-index")
        .removeAttr("data-background-color")
        .removeClass("hover");

      $(svg).remove();
    }

    if (request) {
      request.abort();
      request = null;
    }
  });
}

/*
Sự kiện sau khi render lại event trên fullcalendar
 */
function eventAfterRender(event, element, view) {
  if (isCustomRender == false) return;

  if (
    (event.real_start == null && event.real_end == null) ||
    (event.real_start === "0000-00-00 00:00:00" &&
      event.real_end === "0000-00-00 00:00:00")
  )
    return;
  if (event.real_start == null) return;
  var real_start = moment(event.real_start);
  var real_end;
  var color = "#007bff";
  var inday = false;
  //Xử lý việc lệch múi giờ
  var event_end =
    event.end == null
      ? moment()
      : moment(event.end.format("YYYY-MM-DD HH:mm:ss")).toDate();

  real_end =
    event.real_end == null || event.real_end == "0000-00-00 00:00:00"
      ? moment()
      : moment(event.real_end);

  if (event.real_end == null) {
    color = "orange";
  }

  //Xác định các dòng chứa resource, thời gian thực tế nhận và trả hàng trên fullcalendar
  var row = $(".fc-time-area tr[data-resource-id=" + event.resourceId + "]");

  var formatRealStart = real_start.format("YYYY-MM-DD"),
    formatRealEnd = real_end.format("YYYY-MM-DD");
  var start = $(".fc-day[data-date=" + formatRealStart + "]"),
    end = $(".fc-day[data-date=" + formatRealEnd + "]");

  var view = $render_calendar.fullCalendar("getView");
  var viewStartDate = moment(view.intervalStart.format("YYYY-MM-DD")),
    viewEndDate = moment(view.intervalEnd.format("YYYY-MM-DD"));

  if (view.name == "timelineDay") {
    if (
      real_start.isSame(viewStartDate, "day") ||
      real_end.isSame(viewStartDate, "day")
    ) {
      formatRealStart = real_start.format("YYYY-MM-DD[T]HH") + ":00:00";
      formatRealEnd = real_end.format("YYYY-MM-DD[T]HH") + ":00:00";

      start = $(".fc-major[data-date='" + formatRealStart + "']");
      end = $(".fc-major[data-date='" + formatRealEnd + "']");
    }
  }

  //Nếu thời gian thực tế nhận trả hàng không nằm trong view của lịch
  //sẽ hiển thị 1 dòng chạy thẳng trên toàn bộ lịch
  if (start.length == 0 && end.length == 0) {
    return;
  }

  var newEvent = $(
    '<a id="destination-' +
      event.id +
      '"' +
      " data-id=" +
      event.id +
      ' class="fc-timeline-event fc-h-event fc-event fc-start fc-end fc-draggable fc-resizable route-detail clone-event destination circle-start-event">' +
      ' <div class="fc-content"></div>' +
      "</a>"
  );

  newEvent
    .addClass(event.real_end != null ? "arrow-end-event" : "not-completed")
    .data("show-url", urlRouteDetail.replace("-1", event.id));

  var l_start = start.length === 0 ? 0 : start.offset().left,
    l_end = end.length === 0 ? 0 : end.offset().left,
    l_event = $(element).position().left;

  let number_cell = 1;
  if (view.name === "timelineDay") {
    number_cell = 24;
  } else if (view.name === "customTimelineWeek") {
    number_cell = 7;
  } else if (view.name === "customTimelineTwoWeek") {
    number_cell = 14;
  } else if (view.name === "customTimelineMonth") {
    number_cell = $render_calendar.fullCalendar("getDate").daysInMonth();
  } else if (view.name === "timeline") {
    if (this.startDate && this.endDate) {
      number_cell = this.endDate.diff(this.startDate, "days") + 1;
    }
  }
  //Tính toán độ rộng của 1 cell trong fullcalendar
  let width_cell = row.width() / number_cell;

  //Nếu không tìm thấy thời gian thực tế trả hàng, nghĩa là nó nằm ở view khác của lịch nên sẽ hiển thị
  //thời gian thực tế không có mũi tên kết thúc
  var width = 0;
  var left = l_event;

  var estimate_start = $(
    ".fc-day[data-date=" + event.start.format("YYYY-MM-DD") + "]"
  );
  if (view.name === "timelineDay") {
    var formatRlStart = event.start.format("YYYY-MM-DD[T]HH") + ":00:00";
    start = $(".fc-major[data-date='" + formatRlStart + "']");
  }

  if (estimate_start.length === 0) {
    if (view.name === "timelineDay") {
      left = width_cell * Math.ceil(real_start.diff(viewStartDate, "hours"));
    } else {
      left +=
        width_cell *
        Math.ceil(
          moment(real_start.format("YYYY-MM-DD")).diff(viewStartDate, "days")
        );
    }
  } else {
    if (view.name === "timelineDay") {
      left = width_cell * Math.ceil(real_start.diff(event.start, "hours"));
    } else {
      left +=
        width_cell *
        Math.ceil(
          moment(real_start.format("YYYY-MM-DD")).diff(
            moment(event.start.format("YYYY-MM-DD")),
            "days"
          )
        );
    }
  }

  if (start.length === 0 && end.length !== 0) {
    left = 0;
    if (view.name === "timelineDay") {
      width = width_cell * Math.ceil(real_end.diff(viewStartDate, "hours"));
    } else {
      width =
        width_cell *
        Math.ceil(
          moment(real_end.format("YYYY-MM-DD")).diff(viewStartDate, "days")
        );
    }
    newEvent.removeClass("circle-start-event");
  } else if (start.length !== 0 && end.length === 0) {
    if (view.name === "timelineDay") {
      width = width_cell * Math.ceil(moment().diff(viewStartDate, "hours"));
    } else {
      width =
        width_cell *
        Math.ceil(
          viewEndDate.diff(moment(real_start.format("YYYY-MM-DD")), "days")
        );
    }
    newEvent.removeClass("arrow-end-event");
  } else {
    if (l_start === l_end) {
      inday = true;
      width = width_cell - 15; // Đối với đơn hàng trong ngày hiển thị trong cùng 1 cell
    } else {
      width = l_end - l_start;
    }
  }

  if (event.real_end != null && moment(event.real_end).isAfter(event.end)) {
    color = "red";
  }
  newEvent.css({
    top: $(element).position().top,
    left: left,
    width: width,
    "background-color": color,
    height: "2px",
  });
  if (inday) {
    newEvent.addClass("inday");
  }

  $(element).parent(".fc-event-container").append(newEvent);
  // newEvent.insertBefore($(element));
}

// Sự kiện kéo event
function registerDragEvent() {
  $("#external-events .fc-event").each(function () {
    // store data so the calendar knows to render an event upon drop
    $(this).data("event", {
      title: $.trim($(this).find(".order-no").text()), // use the element's text as the event title
      orderId: $(this).find("input[type=hidden]").val(),
      volume: $(this).find(".volume").text(),
      quantity: $(this).find(".quantity").text(),
      stick: true, // maintain when user navigates (see docs on the renderEvent method)
      color: $(this).data("color"),
    });

    // make the event draggable using jQuery UI
    $(this).draggable({
      cancel: ".toggle-order-list-item",
      zIndex: 9999,
      appendTo: "#content-right-div",
      helper: "clone",
      revert: true, // will cause the event to go back to its
      revertDuration: 0, //  original position after the drag
      start: function (event, ui) {
        $(ui.helper).addClass("ui-helper");
        $(this).data(
          "startingScrollTop",
          $(document).scrollTop() + $("#external-events-listing").scrollTop()
        );
      },
      drag: function (event, ui) {
        var st = parseInt($(this).data("startingScrollTop"));
        ui.position.top -= $(this).parent().scrollTop() - st;

        ui.position.top -= $(this).parent().scrollTop() - st;
      },
    });

    $(this).data("duration", "23:59:59");
  });
}

// event click to order
function activeShowOrderAction(externalInvoiceList) {
  externalInvoiceList.find("> div").each(function () {
    let timeClick = 0,
      _element = $(this);
    // _element.find(".order-detail").on("mousedown mouseup click", function (e) {
    //   e.preventDefault();
    //   let timeout;
    //   if (e.type === "mousedown") {
    //     timeout = setTimeout(function () {
    //       timeClick += 1;
    //     }, 130);
    //   }
    //   if (e.type === "mouseup") {
    //     if (timeClick === 0) {
    //       clearTimeout(timeout);
    //       let link = $(this);
    //       showDetailModal(link);
    //     }
    //     timeClick = 0;
    //   }
    // });
    _element.find(".title .toggle-order-list-item").on("click", function (e) {
      if ($(this).hasClass("fa-minus")) {
        $(this)
          .removeClass("fa-minus")
          .addClass("fa-plus")
          .prop("title", "Click để mở rộng");
      } else {
        $(this)
          .removeClass("fa-plus")
          .addClass("fa-minus")
          .prop("title", "Click để thu gọn");
      }
      $(this).parent().parent().parent().parent().removeAttr("style");
      $(this).parent().parent().parent().parent().find(".body").toggle();
    });
  });
}

function eventDrop(event, delta, revertFunc, jsEvent, ui, view) {}
function eventDragStart(event, jsEvent, ui, view) {}
function eventDragStop(event, jsEvent, ui, view) {}

/**
 * Sự kiện khi kéo order vào lịch
 * @param event
 */
function eventReceive(event) {
  console.time("test");
  let modal = $("#choose_route_modal");
  modal.data("id", event._id);
  modal.data("vehicle_id", event.resourceId);
  modal.data("order_id", event.orderId);
  modal.find(".modal-body").html("");

  var resource = $render_calendar.fullCalendar(
    "getResourceById",
    event.resourceId
  );

  sendRequest(
    {
      url: urlChooseRoute,
      type: "POST",
      data: {
        vehicle_id: event.resourceId,
        start: $render_calendar
          .fullCalendar("getView")
          .start.format("YYYY-MM-DD"),
        end: $render_calendar.fullCalendar("getView").end.format("YYYY-MM-DD"),
      },
    },
    function (response) {
      let data = response.data;
      modal.find(".modal-body").html(data.content);
      $("#select-driver-for-route").select2();
      $("span.vehicle-title").text(resource.title);
      $("span.order-title").text(event.title);
      modal.modal("show");
      console.timeEnd("test");
    }
  );
}

function registerChooseRoute() {
  var $choose_route_modal = $("#choose_route_modal");
  $("#button_close_choose_route").click(function (e) {
    $choose_route_modal.modal("hide");
  });
  $("#button_save_choose_route").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var data = {};
    var isCreateRoute = $("input[name=choose-radio]:checked").val() == 1;
    data = {
      is_create_route: isCreateRoute,
      order_id: $choose_route_modal.data("order_id"),
      route_id: isCreateRoute ? null : $choose_route_modal.data("route_id"),
      vehicle_id: isCreateRoute ? $choose_route_modal.data("vehicle_id") : null,
      driver_id: isCreateRoute
        ? $choose_route_modal.find("#select-driver-for-route").val()
        : null,
    };
    var url = $(this).data("url");
    sendRequest(
      {
        url: url,
        type: "POST",
        data: data,
      },
      function (response) {
        if (response.errorCode != 0) {
          toastr["error"](response.message);
        } else {
          toastr["success"]("Ghép chuyến thành công");
          $choose_route_modal.modal("hide");
          loadOrderPaging(1);
        }
      }
    );
  });

  $choose_route_modal.on("hidden.bs.modal", function () {
    $render_calendar.fullCalendar(
      "removeEvents",
      $choose_route_modal.data("id")
    );
  });

  $(document).on("change", "#radio-create-route", function () {
    $choose_route_modal.data("route_id", null);
    $choose_route_modal
      .find("#select-driver-for-route")
      .prop("disabled", false);
    $(".route-item").prop("disabled", true);
  });

  $(document).on("change", "#radio-merge-route", function () {
    $choose_route_modal.find("#select-driver-for-route").prop("disabled", true);
    $(".route-item").prop("disabled", false);
  });

  $(document).on("change", ".route-item", function () {
    $choose_route_modal.data("route_id", $(this).val());
  });
}

// Load dữ liệu Xe trong BDK
// CreatedBy nlhoang 12/04/2020
function loadResource(isLoadEvent, callback) {
  if (isLoadEvent) {
    loadEvent(callback, true);
  }
}

// Tạo dữ liệu filter cho bảng điều khiển
// CreatedBy nlhoang 12/04/2020
function createFilterData(isFilterVehicle) {

  var partnerId = $('#partner_id').length ? $("#partner_id").val() : 0;

  var vehicleTeamIDs = $("#filter_vehicle_team_ids").select2("data")
    ? $("#filter_vehicle_team_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",")
    : "";
  var vehicleIDs = $("#filter_vehicle_ids").select2("data")
    ? $("#filter_vehicle_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",")
    : "";
  var customerIDs = $("#filter_customer_ids").select2("data")
    ? $("#filter_customer_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",")
    : "";
  var vehicleGroupIDs = $("#filter_vehicle_group_ids").select2("data")
    ? $("#filter_vehicle_group_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",")
    : "";
  var statuses =
    $("#status-listing").val() == "" ? -1 : $("#status-listing").val();
  var start = self.startDate
    ? self.startDate
    : $render_calendar.fullCalendar("getView").start;
  var end = self.endDate
    ? self.endDate
    : moment(
        $render_calendar.fullCalendar("getView").end.format("YYYY-MM-DD")
      ).add(-1, "days");
  var formatStart = start.format("YYYY-MM-DD");
  var formatEnd = end.format("YYYY-MM-DD");

  return {
    partnerId : partnerId,
    vehicleTeamIDs: vehicleTeamIDs,
    vehicleIDs: vehicleIDs,
    vehicleGroupIDs: vehicleGroupIDs,
    customerIDs: customerIDs,
    statuses: statuses,
    start: formatStart,
    end: formatEnd,
    pageIndex: 1,
    vehiclePageIndex: currentPage,
    vehiclePageSize: page_size,
    isFilterVehicle: isFilterVehicle ? 1 : 0,
  };
}

// Load dữ liệu đơn hàng trên BDK
// CreatedBy nlhoang 12/04/2020
function loadEvent(callback, isLoading, isFilterVehicle = false) {
  var self = this;
  self.isCustomRender = false;

  var filterData = createFilterData(isFilterVehicle);
  var formatStart = filterData.start;
  var formatEnd = filterData.end;

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
    url: urlRouteList,
    type: "GET",
    data: filterData,
  };
  var totalItem = [];
  $render_calendar.fullCalendar("removeEvents");

  var func = function (response, filterData) {
    if (!response) return;
    let pager = $("#pager");
    pager.html(response.resources.paginator);

    this.totalVehicle = response.resources.total;

    resources = [];
    response.resources.items.forEach((item, idx) => {
      resources.push({
        index:
          (filterData.vehiclePageIndex - 1) * filterData.vehiclePageSize +
          idx +
          1,
        id: item.id,
        title: item.reg_no,
        address: item.current_location == null ? "" : item.current_location,
      });
    });

    $render_calendar.fullCalendar("refetchResources");
    resizeFullCalendarHeight();

    if (response.group) {
      totalItem = [...response.group];
    }
    self.isCustomRender = true;
    if (isFilterVehicle) {
      if (response.events.length == 0) {
        var $reloadDashboardConfirm = $("#reload_dashboard_confirm");
        $reloadDashboardConfirm.modal("show");

        $reloadDashboardConfirm.find("button").click(function () {
          $("#status-listing").val(-1);
          $("#wrapper-status label").each((index, item) => {
            if ($(item)[0].id === "chkAll") {
              $(item).addClass("active");
            } else {
              $(item).removeClass("active");
            }
          });
          pagingResource(1, true);
          $reloadDashboardConfirm.modal("hide");
          return;
        });
      }
    }
    $render_calendar.fullCalendar("removeEvents");
    $render_calendar.fullCalendar("addEventSource", response);

    if (totalVehicle) {
      var header = $(
        ".fc-resource-area.fc-widget-header .fc-widget-header .fc-cell-text"
      );
      var total =
        header.text().indexOf("(") > 0
          ? header.text().replace(/ *\([^)]*\) */g, "(" + totalVehicle + ")")
          : header.text() + "(" + totalVehicle + ")";
      header.text(total);
    }

    self.isCustomRender = false;

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
      func(response, filterData);
    });
  } else {
    sendRequestNotLoading(data, function (response) {
      func(response, filterData);
    });
  }
}

// Load danh sách Xe
function pagingResource(page, isLoadEvent, callback) {
  currentPage = page;
  loadResource(isLoadEvent, callback);
}

// Load danh sách đơn hàng ở trạng thái Sẵn sàng
function loadOrderPaging(page) {
  $(".wrap-loader").css("display", "block");
  $("#hdfOrderIDs").val("");
  sendRequestNotLoading(
    {
      url: urlOrderList,
      method: "get",
      data: {
        keyword: $("#invoice-search").val(),
        page: page,
      },
    },
    function (data) {
      getOrderList(data);
    }
  );
}

//Hiển thị danh sách hóa đơn
function getOrderList(data) {
  var externalInvoiceList = $("#external-events-listing");
  externalInvoiceList.html(data.data.content);

  registerDragEvent();
  activeShowOrderAction(externalInvoiceList);

  var pager = $("#invoice-pager");
  pager.html(data.data.paginator);

  $(".wrap-loader").css("display", "none");
}

// Thiết laoaj tài xế mặc định
// CreatedBy nlhoang 12/04/2020
function setDefaultDriver(vehicleId, idView) {
  $(idView).find("option").remove();
  sendRequest(
    {
      url: urDefaultDriverForVehicle,
      type: "GET",
      data: {
        vehicle_id: vehicleId,
      },
    },
    function (response) {
      if (!response.ok) {
        return showErrorFlash(response.message);
      }
      if (response.data != null && response.data !== "") {
        var id = response.data["id"];
        var full_name = response.data["full_name"];
        $(idView).html(
          " <option value=" +
            id +
            ' selected="selected" title="' +
            full_name +
            '"></option>'
        );
      }
    }
  );
}

//Xử lý sự kiện lọc thông tin đơn hàng theo trạng thái
function registerSearchByStatus() {
  var $statuses = $("#status-listing");
  $("#wrapper-status label").click(function () {
    $(this).toggleClass("active");

    if ($(this)[0].id === "chkAll") {
      if ($(this).hasClass("active")) {
        $("#wrapper-status label:not(#chkAll)").removeClass("active");
        $statuses.val(-1);
      }
    } else {
      if ($(this).hasClass("active")) {
        $("#wrapper-status #chkAll").removeClass("active");
      }
      var listStatus = $.map($("#wrapper-status .active"), function (item) {
        return $(item).attr("data-status");
      });
      listStatus = listStatus.filter((p) => p != -1);
      $statuses.val(listStatus.length > 0 ? listStatus.join(";") : -1);
    }
    if ($statuses.val() == -1) {
      debounce(pagingResource(1, true), 500);
    } else {
      loadEvent(null, true, true);
    }
  });
}

// Tìm kiếm xe
function registerSearchVehicle() {
  $(document).on("keypress", "body #search", function (e) {
    if (e.which === 13) {
      temp = $(this).val();
      pagingResource(1), true;
    }
  });
}

// Tìm kiếm hóa đơn
function registerSearchInvoice() {
  $(document).on("keypress", "body #invoice-search", function (e) {
    if (e.which === 13) {
      loadOrderPaging(1);
    }
  });

  // ajax search cho search order
  $("#invoice-search").on("keyup", function (e) {
    if (
      (e.keyCode >= 48 && e.keyCode <= 57) ||
      (e.keyCode >= 65 && e.keyCode <= 90) ||
      (e.keyCode >= 96 && e.keyCode <= 105) ||
      e.keyCode === 8 ||
      e.keyCode === 32
    ) {
      delay(function () {
        loadOrderPaging(1);
      }, 800);
    }
  });

  var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();
}

// Đăng ký nút tùy chỉnh ngày
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
        // parentEl: '#content-div-caledar .fc-toolbar.fc-header-toolbar .fc-right',
        locale: {
          applyLabel: "Chọn",
          cancelLabel: "Hủy",
          fromLabel: "Từ",
          toLabel: "đến",
          customRangeLabel: "Tùy chọn",
          daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
          monthNames: [
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5 ",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12",
          ],
          firstDay: 1,
        },
      },
      function (start, end, label) {
        self.startDate = start;
        self.endDate = end;
        loadEvent(null, true);
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
}

// Load dữ liệu trong 2 tuần
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

// Đăng ký tải thông tin chuyến xe
function registerLoadTrip() {
  var self = this;
  $(document).on("click", ".fc-today-button", function () {
    self.startDate = self.endDate = void 0;
    loadEvent(null, true);
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
    loadEvent(null, true);
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
    loadEvent(null, true);
  });

  $(document).on("click", ".fc-timelineDay-button", function (e) {
    e.preventDefault();
    e.stopPropagation();
    self.fullCalendarView = "timelineDay";
    self.startDate = self.endDate = void 0;
    $render_calendar.fullCalendar("option", "slotDuration", "00:60:00");
    loadEvent(null, true);
  });

  $(document).on(
    "click",
    ".fc-customTimelineWeek-button," +
      ".fc-customTimelineTwoWeek-button," +
      ".fc-customTimelineMonth-button",
    function (e) {
      e.preventDefault();
      e.stopPropagation();
      self.fullCalendarView = "timeline";
      self.startDate = self.endDate = void 0;
      // $render_calendar.fullCalendar('option', 'slotDuration', {days: 1});
      loadEvent(null, true);
    }
  );

  $(document).on("click", ".fc-customTwoWeekDate-button", function (e) {
    e.preventDefault();
    e.stopPropagation();
    self.fullCalendarView = "timeline";

    var activeFcButton = $(".fc-button.fc-state-active");
    $(".fc-button").removeClass("fc-state-active");
    $(".fc-customTwoWeekDate-button").toggleClass("fc-state-active");

    var lastDayOfCurrentWeek = moment().endOf("isoWeek");
    var beginDayOfCurrentWeek = moment().subtract(1, "week").startOf("isoWeek");

    self.startDate = beginDayOfCurrentWeek;
    self.endDate = lastDayOfCurrentWeek;

    loadEvent(function () {
      var activeFcButton = $(".fc-button.fc-state-active");
      $(".fc-button").removeClass("fc-state-active");
      $(".fc-customTwoWeekDate-button").toggleClass("fc-state-active");
    }, true);
  });

  $(document).on("click", ".fc-refreshButton-button", function () {
    loadEvent(null, true);
  });
}

//Đăng ký hiển thị fullscreen lịch
function registerFullscreenCalendar() {
  //Xử lý nút xuất pdf phần thông tin lịch
  $(document).on("click", ".fc-fullscreenButton-button", function () {
    var full = $(".full-calendar #calendar-" + calendarID);
    full.parent().toggleClass("full-screen-calendar");
    full.toggleClass("fs-calendar");
    $(this).find("span").toggleClass("fa-window-maximize");
    $(this).find("span").toggleClass("fa-window-minimize");
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
    isCustomRender = true;
    $render_calendar.fullCalendar("rerenderEvents");
    isCustomRender = false;
  });
}

// Tự động tải thông tin dashboard sau 1 phút
function registerAutoLoadTrip() {
  //Mặc định 1 phút sẽ tự động refresh màn hình tổng quan
  var reloadMinute = systemConfig.reload || 5;
  setInterval(function () {
    pagingResource(currentPage, true);
    loadOrderPaging(1);
    $(".popover").each(function () {
      $(this).popover("hide");
    });
  }, 1000 * 60 * reloadMinute);
}

// Đăng ký sự kiện đóng popup
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

// Đăng ký sự kiện đóng mở panel hóa đơn
function registerTogglePanel() {
  /*
   * ẩn hiện thẻ div bên phải
   * */
  $("#click-toggle-right-div").on("click", toggleRightDiv);

  function toggleRightDiv() {
    let $contentRightDiv = $("#content-right-div"),
      $contentDiv = $("#content-div-caledar"),
      $clickToggleRightDiv = $("#click-toggle-right-div");
    if ($("#content-right-div").is(":visible")) {
      $contentRightDiv.hide();
      $contentDiv.removeClass("col-md-9");
      $contentDiv.addClass("col-md-12");
      $clickToggleRightDiv.html('<i class="fa fa-arrow-circle-o-left"></i>');
      $clickToggleRightDiv.attr(
        "data-original-title",
        "Hiển thị danh sách đơn hàng"
      );
    } else {
      $contentRightDiv.show();
      $contentDiv.removeClass("col-md-12");
      $contentDiv.addClass("col-md-9");
      $clickToggleRightDiv.html('<i class="fa fa-arrow-circle-o-right"></i>');
      $clickToggleRightDiv.attr("data-original-title", "Ẩn danh sách đơn hàng");
    }
    $render_calendar.fullCalendar("option", "width", "100%");
    $render_calendar.fullCalendar(
      "option",
      "contentHeight",
      $(window).height() - 270
    );
    isCustomRender = true;
    $render_calendar.fullCalendar("rerenderEvents");
    isCustomRender = false;
  }
}

// Đăng kí sự kiện scroll to top
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

// Đăng kí sự kiện resize window
$(window).resize(function () {
  isCustomRender = true;
  resizeFullCalendarHeight();
  $render_calendar.fullCalendar("refetchEvents");
  $render_calendar.fullCalendar("rerenderEvents");
  isCustomRender = false;
});

// Đăng ký lọc BDK theo đội tài xế thuộc quyền quản lý
function registerDashboardDefault() {
  if (systemConfig.viewType === "timelineTwoWeek") {
    loadTwoWeek();
  } else {
    pagingResource(1, true);
  }
}
