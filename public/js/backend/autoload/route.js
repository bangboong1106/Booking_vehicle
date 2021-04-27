$(function () {
  if (typeof cboSelect2 !== "undefined") {
    if (typeof driverDropdownUri !== "undefined") {
      cboSelect2.driver(driverDropdownUri);
    }
    if (typeof urlVehicle !== "undefined") {
      cboSelect2.vehicle(urlVehicle);
    }
    if (typeof urlLocation !== "undefined") {
      cboSelect2.location(urlLocation);
    }
    if (typeof orderDropdownUri !== "undefined") {
      cboSelect2.order(orderDropdownUri, {
        routeId: $("#route_id").val(),
        vehicleId: $("#vehicle_id").val(),
        driverId: $("#driver_id").val(),
      });
    }
    if (typeof quotaDropdownUri !== "undefined") {
      cboSelect2.quotas(quotaDropdownUri, $("#driver_id").val());
    }
  }

  if (typeof createDriverQuickSearch != "undefined") {
    var quickSearch = createDriverQuickSearch();
    if (typeof searchDriverExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchDriverExceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createVehicleQuickSearch != "undefined") {
    var quickSearch = createVehicleQuickSearch();
    if (typeof searchVehicleExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchVehicleExceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createOrderQuickSearch != "undefined") {
    var quickSearch = createOrderQuickSearch();
    if (typeof searchOrderExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchOrderExceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createQuotaQuickSearch != "undefined") {
    var quickSearch = createQuotaQuickSearch();
    if (typeof searchQuotaExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchQuotaExceptIds;
      quickSearch(config).init();
    }
  }

  let addCompleteModal = $("#add_complete");
  addCompleteModal.on("hide.bs.modal", function (e) {
    let entity = addCompleteModal.data("entity"),
      model = addCompleteModal.data("model"),
      button = addCompleteModal.data("button");

    switch (model) {
      case "location":
        addLocationComplete(entity, button);
        break;
      default:
        return;
    }
  });

  function addLocationComplete(entity, button) {
    let locationSelect = button
      .closest(".input-group")
      .find(".select-location");

    locationSelect
      .empty()
      .append(
        '<option value="' +
          entity.id +
          '" title="' +
          entity.title +
          '">' +
          entity.title +
          "</option>"
      )
      .val(entity.id)
      .trigger("change");
  }

  //Đăng ký lại sự kiện select2 quota khi chọn xe
  $(".select-vehicle").on("change", function (e) {
    if (typeof quotaDropdownUri !== "undefined") {
      cboSelect2.quotas(quotaDropdownUri, $("#vehicle_id").val());
    }

    var id = $("input[name=id]").val();
    if (id == "undefined" || id == "" || id == 0) {
      $("#order_id").empty();
      let data = $(this).select2("data")[0];
      if (data && data.id === "") {
        $("#order_id").prop("disabled", true);
        $("#order-search").addClass("pointer");
      } else {
        $("#order_id").prop("disabled", false);
        $("#order-search").removeClass("pointer");
      }
    }

    if (typeof orderDropdownUri !== "undefined") {
      cboSelect2.order(orderDropdownUri, {
        routeId: $("#route_id").val(),
        vehicleId: $("#vehicle_id").val(),
        driverId: $("#driver_id").val(),
      });
    }
  });

  $(".select-driver").on("change", function (e) {
    if (typeof orderDropdownUri !== "undefined") {
      cboSelect2.order(orderDropdownUri, {
        routeId: $("#route_id").val(),
        vehicleId: $("#vehicle_id").val(),
        driverId: $("#driver_id").val(),
      });
    }
  });

  bindLocations();

  // Xử lý thêm địa điểm
  // Createdby nlhoang 01.05.2019
  $(document).on("click", ".add-plus", function (e) {
    generateLocationItem(void 0);
  });

  // Lưu thông tin địa điểm vào hidden field
  // Createdby nlhoang 03.05.2019
  $(".select-location").on("change", function (e) {
    getLocations(true);
  });

  // Lưu thông tin địa điểm vào hidden field
  // Createdby nlhoang 03.05.2019
  $(".select-order").on("select2:select", function (e) {
    var id = e.params.data.id;
    sendRequestNotLoading(
      {
        url: locationOrderUri,
        type: "GET",
        data: {
          order_id: id,
        },
      },
      function (response) {
        if (
          response == "" ||
          response == null ||
          (response.location == "[]" && response.order_cost == "[]")
        )
          return;

        if (response.location) {
          var locations = [];
          if ($("#locations").val() != "") {
            locations = JSON.parse($("#locations").val());
          }
          if (Array.isArray(locations)) {
            var location = response.location;
            location.order_id = id;
            locations.push(location);
          }
          $("#locations").val(JSON.stringify(locations));
          generateLocationItem(location);

          bindCostOrderAndETD_ETA(locations);

          // Cap nhat xe va tai xe
          if (!$("#vehicle_id").val() && response.vehicleAndDriver) {
            if (response.vehicleAndDriver.vehicle_id) {
              var vehicle_id = response.vehicleAndDriver.vehicle_id;
              var reg_no = response.vehicleAndDriver.reg_no;
              $("#vehicle_id").empty();
              $("#vehicle_id").append(
                $("<option>", {
                  value: vehicle_id,
                  text: reg_no,
                  title: reg_no,
                })
              );
              $("#vehicle_id").val(vehicle_id);
            }
            if (response.vehicleAndDriver.driver_id) {
              var driver_id = response.vehicleAndDriver.driver_id;
              var full_name = response.vehicleAndDriver.full_name;
              $("#driver_id").empty();
              $("#driver_id").append(
                $("<option>", {
                  value: driver_id,
                  text: full_name,
                  title: full_name,
                })
              );
              $("#driver_id").val(driver_id);
            }
          }
        }
      }
    );
  });

  $(".select-order").on("select2:unselect", function (e) {
    var id = e.params.data.id;
    var locations = [];
    if ($("#locations").val() != "") {
      locations = JSON.parse($("#locations").val());
    }
    if (Array.isArray(locations)) {
      locations = locations.filter((p) => p.order_id != id);
      $("#locations").val(JSON.stringify(locations));
    }
    if (
      $(".timeline.location").find(".timeline-item:not(:last)").length !== 1
    ) {
      $(".timeline.location")
        .find(".timeline-item[data-order-id=" + id + "]")
        .remove();
    } else {
      var firstItem = $(".timeline.location").find(".timeline-item:first");
      firstItem.find(":input").prop("disabled", false);
      firstItem.find(":input").val("");
      var newDestinationLocation = firstItem.find(
        ".destination .select-location"
      );
      newDestinationLocation.empty();
      newDestinationLocation.val("").trigger("change");
      var newArrivalLocation = firstItem.find(".arrival .select-location");
      newArrivalLocation.empty();
      newArrivalLocation.val("").trigger("change");
    }
    bindCostOrderAndETD_ETA(locations);
  });

  $(document).on(
    "keyup",
    ".timeline.location .datepicker,.timeline.location .timepicker",
    function (e) {
      getLocations(false);
    }
  );

  $(".timeline.location .datepicker")
    .datetimepicker()
    .on("dp.change", function (ev) {
      getLocations(false);
    });

  $(".timeline.location .timepicker")
    .datetimepicker()
    .on("dp.change", function (ev) {
      getLocations(false);
    });

  // THực hiện xóa địa điểm trên lộ trình
  // Createdby nlhoang 01.05.2019
  $(document).on("click", ".delete-timeline-item", function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (
      $(".timeline.location").find(".timeline-item:not(:last)").length === 1
    ) {
      $("#warning-delete").modal("show");
      return;
    }
    var orderID = $(this).closest(".timeline-item").attr("data-order-id");
    var values = $(".select-order").select2("data");
    if (values) {
      $(".select-order").empty();
      $.each(values, function (i, item) {
        if (item.id !== orderID) {
          $(".select-order").append(
            $("<option>", {
              value: item.id,
              text: item.text,
              title: item.title,
            })
          );
        }
      });
      $(".select-order").val(
        values.filter((p) => p.id != orderID).map((p) => p.id)
      );
    }

    $(this).closest(".timeline-item").remove();
    getLocations(true);
  });

  // Lưu thông tin địa điểm vào hidden field
  // Createdby nlhoang 03.05.2019
  $(document).on("change", ".table-cost .select-cost", function (e) {
    getCosts();
  });

  // Tự động tính tổng chi phí
  // Createdby nlhoang 01.05.2019
  $(document).on("keyup", " .number-input", function (event) {
    calcFinalCost();
  });

  // Load thông tin chi phí khi chọn định mức chi phí
  // Createdby nlhoang 09.05.2019
  $(document).on("change", ".select-quota", function (event) {
    let id = $(event.target).val();
    confirmCosts(id);
  });

  // Đăng ký sự kiện approve chi phí
  registerApproval();

  // Đăng ký sự kiện tính doanh thu
  registerPricePolicy();

  // Đăng ký sự kiện tính lương khoán tài xế
  registerDriverIncome();

  if (typeof uploadUrl != "undefined") {
    var config = {};
    config.uploadUrl = uploadUrl;
    config.downloadUrl = downloadUrl;
    config.removeUrl = removeUrl;
    config.publicUrl = publicUrl;
    config.existingFiles = existingFiles;

    config.customSuccessUpload = function (configID, response) {
      var fileIDs = $("#file_id");
      fileIDs.val(
        fileIDs.val() == "" ? response.id : fileIDs.val() + ";" + response.id
      );
    };
    config.customRemovedUpload = function (configID) {
      var fileIDs = $("#file_id");
      return fileIDs;
    };
    config.customFilterFile = function (configID, existingFiles) {
      var mockFiles = existingFiles;
      return mockFiles;
    };
    var dropzoneOneLog = createDropzone();
    dropzoneOneLog(config).init();
  }

    $('.select-order').on('change', function () {
        let value = $(this).val(),
            vehicleId = $('#vehicle_id').val();
        registerCalcCapacity(vehicleId, value);
    });
});

// Lấy thông tin danh sách địa điểm
// Createdby nlhoang 03.05.2019
function getLocations(isCallServer = false) {
  var self = this;
  var locations = [];
  $(".timeline.location")
    .find(".timeline-item:not(:last)")
    .each((index, item) => {
      var location = {};
      var destinationLocation = $(item).find(".destination .select-location");
      if (destinationLocation) {
        location.destination_location_id = $(destinationLocation).val();
        location.destination_location_title = $(destinationLocation).select2(
          "data"
        )[0]
          ? $(destinationLocation).select2("data")[0].title
          : "";
      }
      var arrivalLocation = $(item).find(".arrival .select-location");
      if (arrivalLocation) {
        location.arrival_location_id = $(arrivalLocation).val();
        location.arrival_location_title = $(arrivalLocation).select2("data")[0]
          ? $(arrivalLocation).select2("data")[0].title
          : "";
      }

      var destinationDate = moment(
        $(
          $(".timeline.location .destination").find(".datepicker")[index]
        ).val(),
        "DD-MM-YYYY"
      );
      if (destinationDate.isValid()) {
        location.destination_location_date = destinationDate.format(
          "YYYY-MM-DD"
        );
      } else {
        location.destination_location_date = null;
      }
      var destinationTime = $(
        $(".timeline.location .destination").find(".timepicker")[index]
      ).val();
      location.destination_location_time =
        destinationTime != "" ? destinationTime : null;

      var arrivalDate = moment(
        $($(".timeline.location .arrival").find(".datepicker")[index]).val(),
        "DD-MM-YYYY"
      );
      if (arrivalDate.isValid()) {
        location.arrival_location_date = arrivalDate.format("YYYY-MM-DD");
      } else {
        location.arrival_location_date = null;
      }
      var arrivalTime = $(
        $(".timeline.location .arrival").find(".timepicker")[index]
      ).val();
      location.arrival_location_time = arrivalTime != "" ? arrivalTime : null;

      locations.push(location);
    });

  $("#locations").val(JSON.stringify(locations));
}

// Lấy danh sách chi phí
function getCosts() {
  var $dataRows = $(".table-cost").find("tbody tr");
  var costs = [];
  $dataRows.each(function (index, item) {
    var cost = $(item).find(".select-cost");
    costs.push({
      receipt_payment_id: cost.val(),
      receipt_payment_name: cost.select2("data")[0]
        ? cost.select2("data")[0].text.trim()
        : "",
      amount: $(item).find(".number-input").val(),
    });
  });
  $("#costs").val(JSON.stringify(costs));
}

// Xử lý thêm địa điểm
// Createdby nlhoang 04.05.2019
function generateLocationItem(entity) {
  var locations = $(".timeline.location");

  var items = locations.find(".timeline-item:not(:last)");
  if (!items) return;
  var lastItem = $(items[items.length - 1]);

  lastItem.find(".select-location").select2("destroy");

  var id =
    parseInt(lastItem.find(".select-location").prop("id").match(/\d/), 10) + 1;
  var newItem = lastItem.clone();
  newItem.find(".datepicker").val(null);
  newItem.find(".timepicker").val(null);
  newItem.find(".select-location").removeAttr("data-select2-id");

  newItem
    .find(".destination .select-location")
    .prop("id", "destination_location_" + id)
    .prop("name", "destination_location_" + id);

  newItem
    .find(".arrival .select-location")
    .prop("id", "arrival_location_" + id)
    .prop("name", "arrival_location_" + id);

  newItem
    .find(".destination .datepicker")
    .prop("id", "destination_location_date_" + id)
    .prop("name", "destination_location_date_" + id)
    .prop("placeholder", "Ngày");

  newItem
    .find(".datepicker")
    .datetimepicker({
      format: "DD-MM-YYYY",
      locale: "vi",
      useCurrent: false,
    })
    .on("dp.change", function (ev) {
      getLocations(false);
    });

  newItem
    .find(".destination .timepicker")
    .prop("id", "destination_location_time_" + id)
    .prop("name", "destination_location_time_" + id)
    .prop("placeholder", "Giờ");

  newItem
    .find(".arrival .timepicker")
    .prop("id", "arrival_location_time_" + id)
    .prop("name", "arrival_location_time_" + id)
    .prop("placeholder", "Giờ");

  newItem
    .find(".timepicker")
    .datetimepicker({
      format: "HH:mm",
      locale: "vi",
    })
    .on("dp.change", function (ev) {
      getLocations(false);
    });

  lastItem.after(newItem);
  var options = {
    allowClear: true,
    placeholder: "Vui lòng chọn địa điểm",
    ajax: {
      url: urlLocation,
      dataType: "json",
      data: function (params) {
        return {
          q: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        data.page = data.page || 1;
        return {
          results: data.items.map(function (item) {
            return {
              id: item.id,
              text: item.text,
              title: item.title,
            };
          }),
          pagination: {
            more: data.pagination,
          },
        };
      },
    },
    templateResult: function (data) {
      return data.text;
    },
    templateSelection: function (data) {
      if (data.id === "") {
        // adjust for custom placeholder values
        return "Vui lòng chọn địa điểm";
      }
      return data.title;
    },
    escapeMarkup: function (m) {
      return m;
    },
    language: "vi",
  };
  lastItem.find(".select-location").select2(options);

  var newDestinationLocation = newItem.find(".destination .select-location");
  newDestinationLocation.select2(options);
  // $(newDestinationLocation).off('change');

  var newArrivalLocation = newItem.find(".arrival .select-location");
  newArrivalLocation.select2(options);
  // $(newArrivalLocation).off('change');

  if (entity) {
    newDestinationLocation
      .empty()
      .append(
        '<option value="' +
          entity.destination_location_id +
          '" title="' +
          entity.destination_location_title +
          '">' +
          entity.destination_location_title +
          "</option>"
      )
      .val(entity.destination_location_id)
      .trigger("change");
    $(newDestinationLocation).on("change", function (e) {
      getLocations(true);
    });

    newArrivalLocation
      .empty()
      .append(
        '<option value="' +
          entity.arrival_location_id +
          '" title="' +
          entity.arrival_location_title +
          '">' +
          entity.arrival_location_title +
          "</option>"
      )
      .val(entity.arrival_location_id)
      .trigger("change");
    $(newArrivalLocation).on("change", function (e) {
      getLocations(true);
    });

    var newDestinationDate = newItem.find(".destination .datepicker");
    if (newDestinationDate) {
      var date = moment(entity.destination_location_date);
      if (date.isValid()) {
        $(newDestinationDate).val(date.format("DD-MM-YYYY"));
      }
    }
    var newDestinationTime = newItem.find(".destination .timepicker");
    if (newDestinationTime) {
      var time = moment(entity.destination_location_time, "HH:mm:ss");
      if (time.isValid()) {
        $(newDestinationTime).val(time.format("HH:mm"));
      }
    }

    var newArrivalDate = newItem.find(".arrival .datepicker");
    if (newArrivalDate) {
      var date = moment(entity.arrival_location_date);
      if (date.isValid()) {
        $(newArrivalDate).val(date.format("DD-MM-YYYY"));
      }
    }
    var newArrivalTime = newItem.find(".arrival .timepicker");
    if (newArrivalTime) {
      var time = moment(entity.arrival_location_time, "HH:mm:ss");
      if (time.isValid()) {
        $(newArrivalTime).val(time.format("HH:mm"));
      }
    }

    if (entity.hasOwnProperty("order_id")) {
      newItem.attr("data-order-id", entity.order_id);
      newItem.find(":input").prop("disabled", true);
    } else {
      newItem.find(":input").prop("disabled", false);
    }
  } else {
    $(newDestinationLocation).on("change", function (e) {
      getLocations(true);
    });
    newDestinationLocation.empty();
    newDestinationLocation.val("").trigger("change");

    $(newArrivalLocation).on("change", function (e) {
      getLocations(true);
    });
    newArrivalLocation.empty();
    newArrivalLocation.val("").trigger("change");
    newItem.find(":input").prop("disabled", false);
  }
}

// Load thông tin địa điểm
// Createdby nlhoang 04.05.2019
function bindLocations() {
  var locations = $("#locations").val();
  if (locations) {
    locations = JSON.parse(locations);
    if (Array.isArray(locations)) {
      locations.forEach((entity, index) => {
        if (index == 0) {
          var newDestinationLocation = $(
            ".timeline.location .destination"
          ).find(".select-location")[index];
          if (newDestinationLocation) {
            // $(newDestinationLocation).off('change');
            $(newDestinationLocation)
              .empty()
              .append(
                '<option value="' +
                  entity.destination_location_id +
                  '" title="' +
                  entity.destination_location_title +
                  '">' +
                  entity.destination_location_title +
                  "</option>"
              )
              .val(entity.destination_location_id)
              .trigger("change");
            $(newDestinationLocation).on("change", function (e) {
              getLocations(true);
            });
          }
          var newDestinationDate = $(".timeline.location .destination").find(
            ".datepicker"
          )[index];
          if (newDestinationDate) {
            var date = moment(entity.destination_location_date);
            if (date.isValid()) {
              $(newDestinationDate).val(date.format("DD-MM-YYYY"));
            }
          }
          var newDestinationTime = $(".timeline.location .destination").find(
            ".timepicker"
          )[index];
          if (newDestinationTime) {
            var time = moment(entity.destination_location_time, "HH:mm:ss");
            if (time.isValid()) {
              $(newDestinationTime).val(time.format("HH:mm"));
            }
          }
          //--------------------------------------
          var newArrivalLocation = $(".timeline.location .arrival").find(
            ".select-location"
          )[index];
          if (newArrivalLocation) {
            // $(newArrivalLocation).off('change');
            $(newArrivalLocation)
              .empty()
              .append(
                '<option value="' +
                  entity.arrival_location_id +
                  '" title="' +
                  entity.arrival_location_title +
                  '">' +
                  entity.arrival_location_title +
                  "</option>"
              )
              .val(entity.arrival_location_id)
              .trigger("change");
            $(newArrivalLocation).on("change", function (e) {
              getLocations(true);
            });
          }
          var newArrivalDate = $(".timeline.location .arrival").find(
            ".datepicker"
          )[index];
          if (newArrivalDate) {
            var date = moment(entity.arrival_location_date);
            if (date.isValid()) {
              $(newArrivalDate).val(date.format("DD-MM-YYYY"));
            }
          }
          var newArrivalTime = $(".timeline.location .arrival").find(
            ".timepicker"
          )[index];
          if (newArrivalTime) {
            var time = moment(entity.arrival_location_time, "HH:mm:ss");
            if (time.isValid()) {
              $(newArrivalTime).val(time.format("HH:mm"));
            }
          }
          if (entity.hasOwnProperty("order_id")) {
            $($(".timeline-item")[index]).attr(
              "data-order-id",
              entity.order_id
            );
            $($(".timeline-item")[index]).find(":input").prop("disabled", true);
          } else {
            $($(".timeline-item")[index])
              .find(":input")
              .prop("disabled", false);
          }
        } else {
          generateLocationItem(entity);
        }
      });
    }
  }
}

// Tự động tính tổng chi phí
// Createdby nlhoang 01.05.2019
function getTotalCost() {
  var total = 0;
  var $dataRows = $(".table-cost").find("tbody tr");

  // $dataRows.each(function () {
  //     total += parseFloat($(this).find('.number-input').val().replace(/,/g, ""));
  // });
  //
  // var result = $('.table-cost').find('.result-cost');
  // result.html(total.toLocaleString("en-EN"));
}

// Load thông tin địa điểm
// Createdby nlhoang 04.05.2019st
function bindCosts(costs) {
  if (costs) {
    costs = JSON.parse(costs);
    if (Array.isArray(costs)) {
      costs.forEach((entity, index) => {
        generateCostItem(entity, index);
      });
    }
    getTotalCost();
  }
}

// Thêm mới 1 dòng dữ liệu chi phí
// Createdby nlhoang 04.05.2019
function generateCostItem(entity, index) {
  let tableCost = $(".table-cost"),
    tableBody = tableCost.find("tbody"),
    trDefault = tableBody.find(".cost-default"),
    trNew = trDefault.clone();

  trNew.find(".rp-item").each(function () {
    let input = $(this),
      fieldName = input.data("field");
    input.attr("name", "listCost[" + index + "][" + fieldName + "]");
  });
  trNew.removeClass("d-none").removeClass("cost-default").addClass("cost-item");
  trNew.find(".rp-name").val(entity.receipt_payment_name);
  trNew.find(".receipt-payment").html(entity.receipt_payment_name);
  trNew.find(".rp-id").val(entity.receipt_payment_id);
  trNew.find(".rp-amount").val(entity.amount);
  trNew.find(".currency").html(formatNumber(entity.amount));
  tableBody.append(trNew);
}

// Xử lý sự kiện khi chọn lại 1 định mức chi phí
// Createdby nlhoang 09.05.2019
function routeSearchCallback(routes) {
  var self = this;
  $(".select-quota").empty().trigger("change");
  routes.forEach(function (item) {
    $(".select-quota").select2("trigger", "select", {
      data: {
        id: item.id,
        title: item.title,
      },
    });
  });
  if (routes && routes.length > 0) {
    confirmCosts(routes[0].id);
  }
}

// Xử lý sự kiện khi chọn lại định mức chi phí
// Createdby nlhoang 09.05.2019
function confirmCosts(id, name) {
  sendRequestNotLoading(
    {
      url: costsUri,
      type: "GET",
      data: {
        quota_id: id,
      },
    },
    function (response) {
      if (response === "" || response === null || response.costs === "[]")
        return;
      $(".table-cost").find("tbody tr.cost-item").remove();
      bindCosts(response.costs);
      var total_cost = 0;
      if (response.total_cost != null && response.total_cost !== "")
        total_cost = response.total_cost;
      $("#total_cost").val(total_cost);
      $("#total_cost_view").html(formatNumber(total_cost));
      calcFinalCost();
    }
  );
}
function bindCostOrderAndETD_ETA(locations) {
  if (Array.isArray(locations)) {
    var order_cost = 0;
    var ETD_date = "";
    var ETD_time = "";
    var ETA_date = "";
    locations.forEach(function (location) {
      order_cost += parseFloat(location.order_cost);

      //Lay ETD ETA dưa vao lo trinh
      var destination_location_date = location.destination_location_date;
      var destination_location_time = location.destination_location_time;
      var arrival_location_date = location.arrival_location_date;
      var arrival_location_time = location.arrival_location_time;

      if (
        ETD_date == "" &&
        destination_location_date != null &&
        destination_location_date != ""
      ) {
        ETD_date = destination_location_date;
        ETD_time = destination_location_time;
      }

      if (
        ETA_date == "" &&
        arrival_location_date != null &&
        arrival_location_date != ""
      ) {
        ETA_date = arrival_location_date;
        ETA_time = arrival_location_time;
      }

      if (
        ETD_date != "" &&
        destination_location_date != null &&
        destination_location_date != ""
      ) {
        var ETD_datetime = moment(
          ETD_date + " " + ETD_time,
          "DD-MM-YYYY HH:mm:ss"
        ).format();
        var destination_datetime = moment(
          destination_location_date + " " + destination_location_time,
          "YYYY-MM-DD HH:mm:ss"
        ).format();
        if (destination_datetime < ETD_datetime) {
          ETD_date_rs = destination_location_date;
          ETD_time_rs = destination_location_time;
        }
      }
      if (
        ETA_date != "" &&
        arrival_location_date != null &&
        arrival_location_time != ""
      ) {
        var ETA_datetime = moment(
          ETA_date + " " + ETA_time,
          "DD-MM-YYYY HH:mm:ss"
        ).format();
        var arrival_datetime = moment(
          arrival_location_date + " " + arrival_location_time,
          "YYYY-MM-DD HH:mm:ss"
        ).format();
        if (arrival_datetime > ETA_datetime) {
          ETA_date = arrival_location_date;
          ETA_time = arrival_location_time;
        }
      }
    });

    $("#order_cost").val(order_cost);
    $("#order_cost_view").html(formatNumber(order_cost));

    var ETD_date = moment(ETD_date);
    var ETD_time = moment(ETD_time, "HH:mm:ss");
    if (ETD_date.isValid()) $("#ETD_date").val(ETD_date.format("DD-MM-YYYY"));
    if (ETD_time.isValid()) $("#ETD_time").val(ETD_time.format("HH:mm"));

    var ETA_date = moment(ETA_date);
    var ETA_time = moment(ETA_time, "HH:mm:ss");
    if (ETA_date.isValid()) $("#ETA_date").val(ETA_date.format("DD-MM-YYYY"));
    if (ETA_time.isValid()) $("#ETA_time").val(ETA_time.format("HH:mm"));

    calcFinalCost();
  }
}

function calcFinalCost() {
  let inputTotalCost = $("#total_cost"),
    total_cost = Number.isNaN(parseFloat(inputTotalCost.val()))
      ? 0
      : parseFloat(inputTotalCost.val());
  // inputOrderCost = $("#order_cost"),
  // order_cost = Number.isNaN(parseFloat(inputOrderCost.val())) ? 0 : parseFloat(inputOrderCost.val()),
  // final_cost = total_cost + order_cost;
  $("#total_cost_view").html(formatNumber(total_cost));
  // $("#total_cost").val(final_cost);
}

// Sự kiện approve chi phí
function registerApproval() {
  var autoId = 0;

  $(document).on("click", "#approve-btn", function (e) {
    let btn = $(this),
      modal = $("#modal_approval"),
      url = btn.data("url");

    sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        let data = response.data;
        modal.find(".modal-content").html(data.content);
        modal
          .find(".number-input")
          .toArray()
          .forEach(function (el) {
            new Cleave(el, {
              numeral: true,
              numeralDecimalMark: ",",
              delimiter: ".",
              numeralDecimalScale: 4,
              numeralThousandsGroupStyle: "thousand",
            });
          });
        autoId = 0;
        modal.modal("show");
      }
    );
  });

  $(document).on("click", ".btn-approve", function (e) {
    let btn = $(this),
      tableBody = btn.closest(".modal-content").find("#body_content"),
      url = btn.data("url"),
      modal = $("#modal_approval"),
      data = [];
    tableBody.find(".final-cost").each(function (e, item) {
      let input = $(this),
        id = input.data("id"),
        value = input.val();
      if (id != -1) {
        data.push({
          id: id,
          name: "",
          isInserted: false,
          value: value,
        });
      } else {
        var tmp = $(item).parents("tr").find(".insertCost").select2("data");
        if (tmp) {
          var costId = $(item).parents("tr").find(".insertCost").val();
          if (costId) {
            data.push({
              id: costId,
              name: tmp[0].text.trim(),
              isInserted: true,
              value: value,
            });
          }
        }
      }
    });
    var note = modal.find("#note").val();
    showLoading();
    sendRequestNotLoading(
      {
        url: url,
        type: "POST",
        data: {
          all: true,
          listCost: data,
          note: note,
        },
      },
      function (response) {
        toastr["success"]("Phê duyệt chi phí thành công");
        modal.modal("hide");
        $(".list-cost").html(response.data.content);
        hideLoading();
        oneLogGrid._ajaxSearch($(".list-ajax"), null, false);
      }
    );
  });

  $(document).on("keyup", ".inserted.final-cost", function (e) {
    var val = $(this).val();
    $(this).parents("tr").find(".insertedLastCost").html(val);
  });

  $(document).on("click", ".add-cost-wrap .btn", function (e) {
    let btn = $(this),
      tableBody = btn.closest(".modal-content").find("#body_content"),
      data = [],
      modal = $("#modal_approval");

    tableBody.find(".empty-data").remove();

    var $tr = $(
      '<tr class="container-cost">' +
        '<td class="text-right text-middle wrap-cost"></td>' +
        '<td><input class="form-control text-right text-middle inserted final-cost number-input" type="text"  data-id=-1></td>' +
        '<td class="text-right text-middle wrap-cost"><span class="insertedLastCost"></span>' +
        "</td>" +
        +"</tr>"
    );
    var newEvent = $("#cost").clone();
    autoId = autoId - 1;
    newEvent.addClass("insertCost");
    newEvent.attr("id", autoId);
    var $td = $("<td></td>").append(newEvent);
    $tr.prepend($td);
    tableBody.append($tr);

    newEvent.select2();
    modal
      .find(".number-input")
      .toArray()
      .forEach(function (el) {
        new Cleave(el, {
          numeral: true,
          numeralDecimalMark: ",",
          delimiter: ".",
          numeralDecimalScale: 4,
          numeralThousandsGroupStyle: "thousand",
        });
      });
  });
}

// Sự kiện tính giá chuyến xe
// CreatedBy nlhoang 01/07/2020
function registerPricePolicy() {
  $(document).on("click", "#price-policy-btn", function (e) {
    let btn = $(this),
      modal = $("#modal_price_policy"),
      url = btn.data("url"),
      title = $(this).parents(".form-info-wrap").find("#route_code").val();

    sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        let data = response.data;
        modal.find(".modal-content").html(data.content);
        if (modal.data("price-policy")) {
          $(".select-price-policy").each(function (index, element) {
            var id = $(element).attr("id");
            var url = modal.data("price-policy");
            url = url + "?customerId=" + $(element).data("customer-id");
            cboSelect2.pricePolicy(url, "[id='" + id + "']");
          });
        }
        modal
          .find(".number-input")
          .toArray()
          .forEach(function (el) {
            new Cleave(el, {
              numeral: true,
              numeralDecimalMark: ",",
              delimiter: ".",
              numeralDecimalScale: 4,
              numeralThousandsGroupStyle: "thousand",
            });
          });
        modal
          .find(".modal-title")
          .text(modal.find(".modal-title").text() + " " + title);
        modal.modal("show");
      }
    );
  });

  $(document).on("change", ".select-price-policy", function (e) {
    let $wrap = $(this).parents(".wrap-route");
    let params = {
      price_policy_id: $(this).val(),
      customer_id: $(this).parents(".wrap-customer").data("id"),
      route_id: $wrap.data("id"),
    };
    var out = [];
    for (var key in params) {
      if (params.hasOwnProperty(key)) {
        out.push(key + "=" + encodeURIComponent(params[key]));
      }
    }
    var url = $wrap.data("url");
    url = url + "?" + out.join("&");
    var url = sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        if (response.errorCode == 200) {
          let data = response.data;
          $wrap.find(".description").each(function (index, element) {
            var order = data.find((p) => p.order_id == $(element).data("id"));
            if (order) {
              if (order.is_point_charge) {
                $(element).html(order.description.replace(/\n/g, "<br />"));
              } else {
                $(element).html(order.description.replace(/\n/g, "<br />"));
              }
            }
          });
          $wrap.find(".amount").each(function (index, element) {
            var order = data.find((p) => p.order_id == $(element).data("id"));
            if (order) {
              $(element).val(
                order.amount == null ? 0 : formatNumber(order.amount)
              );
            }
          });
        } else {
          toastr["error"](
            "Có lỗi xảy ra khi tính giá cho đơn hàng. Vui lòng thử lại"
          );
          console.error(response.errorMessage);
        }
      }
    );
  });

  $(document).on("click", ".btn-price", function (e) {
    var url = $(this).data("url");
    var modal = $("#modal_price_policy");
    let $wrap = $(this).parents(".wrap-route");
    var data = [];
    $wrap.find(".wrap-customer").each(function (index, customerWrap) {
      var item = {
        customerId: $(customerWrap).data("id"),
        orders: [],
      };
      $(customerWrap)
        .find("#body_content .container")
        .each(function (index, tr) {
          var orderId = $(tr).data("id");
          var amount = parseFloat(
            $(tr).find(".amount").val().replace(/\./g, "").replace(/,/g, ".")
          );
          item.orders.push({
            orderId: orderId,
            amount: amount,
          });
        });

      data.push(item);
    });
    var body = {
      routeId: $wrap.data("id"),
      data: data,
    };
    var url = sendRequest(
      {
        url: url,
        type: "POST",
        data: body,
      },
      function (response) {
        if (response.errorCode == 200) {
          toastr["success"]("Tính giá chuyến xe thành công");
          modal.modal("hide");
        } else {
          toastr["error"]("Có lỗi xảy ra khi tính giá. Vui lòng thử lại");
          console.error(response.errorMessage);
        }
      }
    );
  });
}


// Sự kiện tính lương tài xế theo chuyến xe
// CreatedBy nlhoang 22/07/2020
function registerDriverIncome() {
  $(document).on("click", "#payroll-btn", function (e) {
    let btn = $(this),
      modal = $("#modal_payroll"),
      url = btn.data("url"),
      title = $(this).parents(".form-info-wrap").find("#route_code").val();

    sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        let data = response.data;
        modal.find(".modal-content").html(data.content);
        if (modal.data("payroll")) {
          $(".select-payroll").each(function (index, element) {
            var id = $(element).attr("id");
            var url = modal.data("payroll");
            url = url + "?customerId=" + $(element).data("customer-id");
            cboSelect2.payroll(url, "[id='" + id + "']");
          });
        }
        modal
          .find(".number-input")
          .toArray()
          .forEach(function (el) {
            new Cleave(el, {
              numeral: true,
              numeralDecimalMark: ",",
              delimiter: ".",
              numeralDecimalScale: 4,
              numeralThousandsGroupStyle: "thousand",
            });
          });
        modal
          .find(".modal-title")
          .text(modal.find(".modal-title").text() + " " + title);
        modal.modal("show");
      }
    );
  });

  $(document).on("change", ".select-payroll", function (e) {
    let $wrap = $(this).parents(".wrap-route");
    let params = {
      payroll_id: $(this).val(),
      customer_id: $(this).parents(".wrap-customer").data("id"),
      route_id: $wrap.data("id"),
    };
    var out = [];
    for (var key in params) {
      if (params.hasOwnProperty(key)) {
        out.push(key + "=" + encodeURIComponent(params[key]));
      }
    }
    var url = $wrap.data("url");
    url = url + "?" + out.join("&");
    var url = sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        if (response.errorCode == 200) {
          let data = response.data;
          $wrap.find(".amount").each(function (index, element) {
            var order = data.find((p) => p.order_id == $(element).data("id"));
            if (order) {
              $(element).val(
                order.amount == null ? 0 : formatNumber(order.amount)
              );
            }
          });
        } else {
          toastr["error"]("Có lỗi xảy ra khi tính lương. Vui lòng thử lại");
          console.error(response.errorMessage);
        }
      }
    );
  });

  $(document).on("click", ".btn-payroll", function (e) {
    var url = $(this).data("url");
    var modal = $("#modal_payroll");
    let $wrap = $(this).parents(".wrap-route");
    var data = [];
    $wrap.find(".wrap-customer").each(function (index, customerWrap) {
      var item = {
        customerId: $(customerWrap).data("id"),
        orders: [],
      };
      $(customerWrap)
        .find("#body_content .container")
        .each(function (index, tr) {
          var orderId = $(tr).data("id");
          var amount = parseFloat(
            $(tr).find(".amount").val().replace(/\./g, "").replace(/,/g, ".")
          );
          item.orders.push({
            orderId: orderId,
            amount: amount,
          });
        });

      data.push(item);
    });
    var body = {
      routeId: $wrap.data("id"),
      data: data,
    };
    var url = sendRequest(
      {
        url: url,
        type: "POST",
        data: body,
      },
      function (response) {
        if (response.errorCode == 200) {
          toastr["success"]("Lưu thông tin lương tài xế thành công");
          modal.modal("hide");
        } else {
          toastr["error"](
            "Có lỗi xảy ra khi lưu thông tin lương tài xế. Vui lòng thử lại"
          );
          console.error(response.errorMessage);
        }
      }
    );
  });
}

// Tính toán khả năng tải khi lựa chọn đơn hàng
function registerCalcCapacity(vehicleId, value) {
    if (isNaN(vehicleId) || vehicleId === '') return;

    sendRequest({
        url: calcCapacityUri,
        type: 'GET',
        data: {
            'vehicle_id': vehicleId,
            'order_ids': value,
        }
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        } else {
            let warningMessage = $('.warning-message');
            if (response.data.status === 'OK') {
                warningMessage.empty();
                return;
            }

            let message = response.data.message;
            warningMessage.html(message);
        }
    });
}
