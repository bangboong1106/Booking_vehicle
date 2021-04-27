let map,
  markers = [],
  lat = 21.0464404,
  lng = 105.7936427,
  bounds;
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
$(function () {
  let selectCustomer = $("#customer_id"),
    inputCustomerName = $("#customer_name"),
    inputCustomerMobileNo = $("#customer_mobile_no");

  selectCustomer.on("select2:select select2:clear", function (e) {
    let data = $(this).select2("data")[0];
    if (data && data.id !== "") {
      inputCustomerName.parent().parent().removeClass("hide");
      inputCustomerMobileNo.parent().parent().removeClass("hide");
      var customerName = data.delegate,
        customerMobileNo = data.mobile_no;
      inputCustomerName.val(customerName);
      inputCustomerMobileNo.val(customerMobileNo);
    } else {
      inputCustomerName.parent().parent().addClass("hide");
      inputCustomerMobileNo.parent().parent().addClass("hide");
      inputCustomerName.val("");
      inputCustomerMobileNo.val("");
    }
  });

  $("#switchery_is_collected_documents").on("change", function () {
    var checked = $("#switchery_is_collected_documents").is(":checked");
    if (checked || $("#switchery_is_collected_documents").length == 0) {
      $("#form_is_collected_documents").val("1");
    } else {
      $("#form_is_collected_documents").val("0");
    }
  });

  $("#switchery_is_insured_goods").on("change", function () {
    var checked = $("#switchery_is_insured_goods").is(":checked");
    if (checked || $("#switchery_is_insured_goods").length == 0) {
      $("#form_is_insured_goods").val("1");
    } else {
      $("#form_is_insured_goods").val("0");
    }
  });

  $("#switchery_is_merge_item").on("change", function () {
    var checked = $("#switchery_is_merge_item").is(":checked");
    if (checked || $("#switchery_is_merge_item").length == 0) {
      $("#is_merge_item").val("1");
    } else {
      $("#is_merge_item").val("0");
    }
  });

  registerSelectDriver($('#partner_id').val());
  registerSelectVehicle($('#partner_id').val());

  $('#partner_id').on('change', function (e) {
    registerSelectDriver(this.value);
    registerSelectVehicle(this.value);
  });

  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlLocation !== "undefined") {
      cboSelect2.location(urlLocation, ".select-location", true);
    }
    if (typeof urlCodeConfig !== "undefined") {
      cboSelect2.codeConfig(urlCodeConfig);
    }
    if (typeof routeDropdownUri !== "undefined") {
      cboSelect2.routes(routeDropdownUri);
    }
    if (typeof quotaDropdownUri !== "undefined") {
      cboSelect2.quotas(quotaDropdownUri, null);
    }
    if (typeof comboCustomerUri !== "undefined") {
      cboSelect2.customer(comboCustomerUri, null);
    }
    if (typeof vehicleTeamDropdownUri !== "undefined") {
      cboSelect2.vehicleTeam(vehicleTeamDropdownUri, null);
    }
  }

  if (typeof uploadUrl != "undefined") {
    var config = {};
    config.uploadUrl = uploadUrl;
    config.downloadUrl = downloadUrl;
    config.removeUrl = removeUrl;
    config.publicUrl = publicUrl;
    config.existingFiles = existingFiles;

    config.customSuccessUpload = function (configID, response) {
      var fileIDs = $("#" + configID + "_file_id");
      fileIDs.val(
        fileIDs.val() == "" ? response.id : fileIDs.val() + ";" + response.id
      );
    };
    config.customRemovedUpload = function (configID) {
      var fileIDs = $("#" + configID + "_file_id");
      return fileIDs;
    };
    config.customFilterFile = function (configID, existingFiles) {
      return existingFiles.filter((item) => item.order_status_id == configID);
    };
    var dropzoneOneLog = createDropzone();
    dropzoneOneLog(config).init();
  }

  if (typeof createGoodsQuickSearch != "undefined") {
    var quickSearch = createGoodsQuickSearch();
    var searchGoodsExceptIds = [];
    if (typeof searchGoodsExceptIds != "undefined") {
      var config = {};
      config.searchCallback = (selectedData, datas) => {
        goodsSearchCallback(selectedData, datas);
      };
      config.exceptIds = searchGoodsExceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createRouteQuickSearch != "undefined") {
    var quickSearch = createRouteQuickSearch();
    if (typeof searchRouteExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchRouteExceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createLocationQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createLocationQuickSearch();
    if (typeof exceptIds != "undefined") {
      var config = {};
      config.exceptIds = exceptIds;
      quickSearch(config).init();
    }
  }

  $(".location-order-destination").on(
    "change",
    ".select-location:first-child",
    function (e) {
      $("#hdfDestinationLocationId").val($(this).val());
      registerSuggestion($(this));
    }
  );

  $(".location-order-arrival").on(
    "change",
    ".select-location:first-child",
    function (e) {
      $("#hdfArrivalLocationId").val($(this).val());
      registerSuggestion($(this));
    }
  );

  //Đăng ký lại sự kiện select2 quota khi chọn xe
  $(".select-vehicle").on("change", function (e) {
    setRoute($("#primary_driver_id").val(), $(this).val());
  });

  $("#primary_driver_id").on("change", function (e) {
    setRoute($(this).val(), $("#vehicle_id").val());
  });

  function setRoute(driverId, vehicleId) {
    let routeId = $("#route_id");
    routeId.val("");
    if (vehicleId && driverId) {
      routeId.prop("disabled", false);
      $("#route-search").removeClass("pointer");
    } else {
      routeId.prop("disabled", true);
      $("#route-search").addClass("pointer");
    }
    let data = {
      vehicleId: vehicleId,
      driverId: driverId,
    };
    if (typeof routeDropdownUri !== "undefined") {
      cboSelect2.routes(routeDropdownUri, data);
    }
  }

  $("input[name=order_precedence]").click(function (e) {
    $("#precedence").val($(this).val());
  });

  $(".order-status-select").on("click", function (e) {
    let status = $(this).find("input").val();
    if (status === "4" || status === "5") {
      $(".ETD_reality").removeClass("hide");
    } else {
      $(".ETD_reality").addClass("hide");
      $("[name=locationDestinations\\[0\\]\\[date_reality\\]]").val("");
      $("[name=locationDestinations\\[0\\]\\[time_reality\\]]").val("");
    }
    if (status === "5") {
      $(".ETA_reality").removeClass("hide");
      $(".Document_reality").removeClass("hide");
    } else {
      $(".ETA_reality").addClass("hide");
      $(".Document_reality").addClass("hide");
      $("[name=locationArrivals\\[0\\]\\[date_reality\\]]").val("");
      $("[name=locationArrivals\\[0\\]\\[time_reality\\]]").val("");
    }

    /*        if (status === '4'|| status === '5') {
                if ($('[name=locationDestinations\\[0\\]\\[date_reality\\]]').val() == "") {
                    $('[name=locationDestinations\\[0\\]\\[date_reality\\]]').val($('[name=locationDestinations\\[0\\]\\[date\\]]').val());

                }
                if ($('[name=locationDestinations\\[0\\]\\[time_reality\\]]').val() == "") {
                    $('[name=locationDestinations\\[0\\]\\[time_reality\\]]').val($('[name=locationDestinations\\[0\\]\\[time\\]]').val());

                }
            }
            if (status === '5') {
                if ($('[name=locationArrivals\\[0\\]\\[date_reality\\]]').val() == "") {
                    $('[name=locationArrivals\\[0\\]\\[date_reality\\]]').val($('[name=locationArrivals\\[0\\]\\[date\\]]').val());

                }
                if ($('[name=locationArrivals\\[0\\]\\[time_reality\\]]').val() == "") {
                    $('[name=locationArrivals\\[0\\]\\[time_reality\\]]').val($('[name=locationArrivals\\[0\\]\\[time\\]]').val());
                }
            }*/
  });

  $(".show-detail").click(function (e) {
    e.preventDefault();
    e.stopPropagation();

    let type = $(this).attr("data-type"),
      detail = $(".detail." + type);
    if (detail.hasClass("hide")) {
      $(this).html("ẨN CHI TIẾT");
    } else {
      $(this).html("HIỆN CHI TIẾT");
    }
    detail.toggleClass("hide");
  });

  if ($("#mapChild").length > 0) {
    google.maps.event.addDomListener(window, "load", initMapPage);
  }

  // validate lai form khi thay đổi ngày nhận hàng
  $("#ETD_date, #ETA_date").on("dp.change", function () {
    let form = $("form").validate();
    form.resetForm();
    form.form();
  });

  setReportData();

  exportUpdateData();

  calculateInGoods();

  viewHistory();

  deleteGoods();

  // setTotalGoods();

  registerAddLocation();

  processExcel();

  uploadFromGoogleDrive();

  registerAddModal(selectCustomer, inputCustomerName, inputCustomerMobileNo);

  registerMassUpdateDocuments();

  registerUpdateDocuments();

  registerExportSelected();

  registerPrintBill();

  registerDownloadQRCode();

  changeVAT();

  registerCalcFinalAmount();

  registerUpdateRevenue();

  registerUpdateVinNo();

  registerOpenEditor();

  registerChooseCustomerDefaultData();

  registerUpdatePartner();

  registerMergeOrder();
});

function registerSelectVehicle(partnerId) {

  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlVehicle !== "undefined") {
      cboSelect2.vehicle(urlVehicle,'',{all: '', partner_id : $('#partner_id').val()});
    }
  }
  if (typeof createDriverQuickSearch != "undefined") {
    var quickSearch = createDriverQuickSearch();

    if (typeof primaryDriverExceptIds != "undefined") {
      var primaryDriverConfig = {};
      primaryDriverConfig.exceptIds = primaryDriverExceptIds;
      primaryDriverConfig.searchElement = "primary-driver-search";
      primaryDriverConfig.searchType = "element";
      primaryDriverConfig.tableElement = "table_primary_drivers";
      primaryDriverConfig.modalElement = "primary_driver_modal";
      primaryDriverConfig.buttonElement = "btn-primary-driver";
      primaryDriverConfig.partnerId = partnerId;
      quickSearch(primaryDriverConfig).init();
    }
    if (typeof secondaryDriverExceptIds != "undefined") {
      var secondaryDriverConfig = {};
      secondaryDriverConfig.exceptIds = secondaryDriverExceptIds;
      secondaryDriverConfig.searchElement = "secondary-driver-search";
      secondaryDriverConfig.searchType = "element";
      secondaryDriverConfig.tableElement = "table_secondary_drivers";
      secondaryDriverConfig.modalElement = "secondary_driver_modal";
      secondaryDriverConfig.buttonElement = "btn-secondary-driver";
      secondaryDriverConfig.partnerId = partnerId;
      quickSearch(secondaryDriverConfig).init();
    }
  }

  $("#vehicle_ids").change(function (e) {
    sendRequest(
        {
          url: getDefaultDriverForVehicleUri,
          type: "GET",
          data: {
            vehicle_id: $(this).val(),
          },
        },
        function (response) {
          if (!response.ok) {
            return showErrorFlash(response.message);
          }
          var data = response.data;
          if (data.id) {
            $("#driver_ids").select2("trigger", "select", {
              data: { id: data.id, title: data.full_name },
            });
          }
        }
    );
  });
}
function registerSelectDriver(partnerId) {

  if (typeof cboSelect2 !== "undefined") {
    if (typeof driverDropdownUri !== "undefined") {
      cboSelect2.driver(driverDropdownUri,'','',{all: '', partner_id : $('#partner_id').val()});
    }
  }

  if (typeof createVehicleQuickSearch != "undefined") {
    var quickSearch = createVehicleQuickSearch();
    if (typeof searchVehicleExceptIds != "undefined") {
      var config = {};
      config.exceptIds = searchVehicleExceptIds;
      config.partnerId = partnerId;
      quickSearch(config).init();
    }
  }
}


function registerAddModal(
  selectCustomer,
  inputCustomerName,
  inputCustomerMobileNo
) {
  let addCompleteModal = $("#add_complete");
  addCompleteModal.on("hide.bs.modal", function (e) {
    let entity = addCompleteModal.data("entity"),
      model = addCompleteModal.data("model"),
      button = addCompleteModal.data("button");

    switch (model) {
      case "customer":
        addCustomerComplete(entity);
        break;
      case "location":
        addLocationComplete(entity, button);
        break;
      default:
        return;
    }
  });

  function addCustomerComplete(entity) {
    let fullName = entity.type === "1" ? entity.delegate : entity.full_name;

    let newOption =
      '<option value="' +
      entity.id +
      '" selected="selected" ' +
      'data-customer="' +
      fullName +
      '" data-phone="' +
      entity.mobile_no +
      '" title="' +
      entity.full_name +
      '">' +
      entity.full_name +
      "</option>";
    selectCustomer.append(newOption).trigger("change");

    inputCustomerName.parent().parent().removeClass("hide");
    inputCustomerMobileNo.parent().parent().removeClass("hide");

    inputCustomerName.val(fullName);
    inputCustomerMobileNo.val(entity.mobile_no);
  }

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

    if ($(button).closest(".location-order-destination").length > 0) {
      $("#hdfDestinationLocationId").val(entity.id);
    } else {
      $("#hdfArrivalLocationId").val(entity.id);
    }
  }
}

function calculateInGoods() {
  $(document).on(
    "keyup",
    "input[data-field=quantity],input[data-field=volume],input[data-field=weight]",
    debounce(function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $tr = $(this).parents("tr");
      setTotal($tr, "volume");
      setTotal($tr, "weight");
      setTotalGoods();
    })
  );

  $(document).on(
    "change",
    "input[data-field=insured_goods]",
    debounce(function (e) {
      $(this).val($(this).prop("checked") ? 1 : 0);
    })
  );
}

function viewHistory() {
  $(document).on("click", ".order-history", function () {
    $("#order-history-label").html(
      "Lịch sử của đơn hàng " + $(this).data("name")
    );
    var order_id = $(this).data("id");
    sendRequest(
      {
        url: orderHistoryUrl,
        type: "GET",
        data: {
          order_id: order_id,
        },
      },
      function (response) {
        if (!response.ok) {
          return showErrorFlash(response.message);
        } else {
          $("#content_order_history").html("").append(response.data.content);
          $("#order-history").modal();
          // console.log('$order', response.data.orderInfo);
          if (response.data.orderInfo) {
            var start = new google.maps.LatLng(
              response.data.orderInfo.location_destination_latitude,
              response.data.orderInfo.location_destination_longitude
            );
            var end = new google.maps.LatLng(
              response.data.orderInfo.location_arrival_latitude,
              response.data.orderInfo.location_arrival_longitude
            );
            if (4 === response.data.orderInfo.status) {
              var marker;
              var infowindow = new google.maps.InfoWindow({});
              if (
                response.data.orderInfo.current_latitude &&
                response.data.orderInfo.current_longitude
              ) {
                marker = new google.maps.Marker({
                  position: new google.maps.LatLng(
                    response.data.orderInfo.current_latitude,
                    response.data.orderInfo.current_longitude
                  ),
                  map: map,
                });

                google.maps.event.addListener(
                  marker,
                  "click",
                  (function (marker) {
                    return function () {
                      infowindow.setContent(
                        response.data.orderInfo.current_location
                      );
                      infowindow.open(map, marker);
                    };
                  })(marker, i)
                );
                drawRouteMap(
                  start,
                  end,
                  0,
                  new google.maps.LatLng(
                    response.data.orderInfo.current_latitude,
                    response.data.orderInfo.current_longitude
                  )
                );
              } else {
                drawRouteMap(start, end, 0);
              }
            } else {
              drawRouteMap(start, end, 0);
            }
          }
        }
      }
    );
  });
}

function deleteGoods() {
  $(document).on("click", "table.table-goods .delete-goods", function (e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).parent("td").parent("tr:first").remove();
    setTotalGoods();
    let length = $(".table-goods").find("tbody tr").length - 1;
    if (length === 0) {
      $(".table-goods").removeClass("show").addClass("hide");
      $("#btn-goods-search").removeClass("show").addClass("hide");
      $(".wrap-add-field").removeClass("hide").addClass("show");
    }
  });
}

function addCompletedLoadingModel(model) {
  if (model === "customer") {
    Customer("#modal_add");
  }
  if (model === "loaction") {
    Customer("#modal_add");
  }
}

function drawRouteMap(locationDes, locationArr, typeModal, currentLocation) {
  let mapChild = $(".mapChild"),
    mapElement;
  if (mapChild.length > 1 && typeModal > 0) {
    mapElement = mapChild.get(1);
  } else {
    mapElement = mapChild.get(0);
  }
  if (typeof mapElement === "undefined") {
    return;
  }
  map = new google.maps.Map(mapElement, {
    zoom: 13,
    center: locationArr,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    gestureHandling: "cooperative",
  });

  var directionsService = new google.maps.DirectionsService();
  var directionsDisplay = new google.maps.DirectionsRenderer({
    draggable: false,
    map: map,
    preserveViewport: true,
    // polylineOptions: {
    //     strokeColor: "green"
    // }
  });

  directionsDisplay.addListener("directions_changed", function () {
    computeTotalDistance(directionsDisplay.getDirections(), typeModal);
  });

  displayRoute(
    locationDes,
    locationArr,
    directionsService,
    directionsDisplay,
    currentLocation
  );
}

function displayRoute(origin, destination, service, display, currentLocation) {
  if (currentLocation) {
    ;
    var _waypoints = new Array();
    _waypoints.push({
      location: currentLocation,
      stopover: true, //stopover is used to show marker on map for waypoints
    });
    service.route(
      {
        origin: origin,
        destination: destination,
        travelMode: "DRIVING",
        waypoints: _waypoints,
        optimizeWaypoints: true,
      },
      function (response, status) {
        if (status === "OK") {
          display.setDirections(response);
          display.setMap(map);
        } else {
          console.error("Không thể hiển thị bản đồ: " + status);
        }
      }
    );
  } else {
    service.route(
      {
        origin: origin,
        destination: destination,
        travelMode: "DRIVING",
      },
      function (response, status) {
        if (status === "OK") {
          display.setDirections(response);
          display.setMap(map);
        } else {
          alert("Không thể hiển thị bản đồ: " + status);
        }
      }
    );
  }
}

function computeTotalDistance(result, typeModal) {
  var total = 0;
  var myroute = result.routes[0];
  for (var i = 0; i < myroute.legs.length; i++) {
    total += myroute.legs[i].distance.value;
  }
  total = (total / 1000).toFixed(0);

  if ($(".totalChild").length > 1 && typeModal > 0) {
    var mapElement = $(".totalChild").get(1);
  } else {
    var mapElement = $(".totalChild").get(0);
  }
  mapElement.innerHTML = total + " km";
}

function initMapPage() {
  var latlng = new google.maps.LatLng(lat, lng);
  map = new google.maps.Map(document.getElementById("mapChild"), {
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
}

function clearMarkers() {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }
  markers = [];
}

function detailCallback($orderId) {
  var order_id = $orderId;
  sendRequestNotLoading(
    {
      url: orderRouteMapUrl,
      type: "GET",
      data: {
        order_id: order_id,
      },
    },
    function (response) {
      if (!response.ok) {
        // return showErrorFlash(response.message);
        $("#mapChild").hide();
        $("#totalChild").hide();
      } else {
        if (response.data.orderInfo) {
          var start = new google.maps.LatLng(
            response.data.orderInfo.location_destination_latitude,
            response.data.orderInfo.location_destination_longitude
          );
          var end = new google.maps.LatLng(
            response.data.orderInfo.location_arrival_latitude,
            response.data.orderInfo.location_arrival_longitude
          );
          if (4 == response.data.orderInfo.status) {
            var marker;
            var infowindow = new google.maps.InfoWindow({});
            if (
              response.data.orderInfo.current_latitude &&
              response.data.orderInfo.current_longitude
            ) {
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(
                  response.data.orderInfo.current_latitude,
                  response.data.orderInfo.current_longitude
                ),
                map: map,
              });

              google.maps.event.addListener(
                marker,
                "click",
                (function (marker) {
                  return function () {
                    infowindow.setContent(
                      response.data.orderInfo.current_location
                    );
                    infowindow.open(map, marker);
                  };
                })(marker, i)
              );
              drawRouteMap(
                start,
                end,
                1,
                new google.maps.LatLng(
                  response.data.orderInfo.current_latitude,
                  response.data.orderInfo.current_longitude
                )
              );
            } else {
              drawRouteMap(start, end, 1);
            }
          } else {
            drawRouteMap(start, end, 1);
          }
        } else {
          $("#mapChild").hide();
          $("#map-detail-info").hide();
        }
      }
    }
  );
}

// Custom luồng nhập dữ liueje Excel
function customImportFormData(formData) {
  var val = $($(".import-type input[type=radio]:checked").get(0)).val();
  formData.append("check_update", val);
}

// Thiết lập dữ liệu xuất Bảng kê
// CreatedBy nlhoang
function setReportData() {
  let config = {
    format: "DD/MM/YYYY",
    startDate: moment().startOf("month"),
    endDate: moment().endOf("month"),
    dateLimit: {
      months: 36,
    },
    showDropdowns: true,
    showWeekNumbers: true,
    timePicker: false,
    timePickerIncrement: 1,
    timePicker12Hour: true,
    ranges: {
      "Hôm nay": [moment(), moment()],
      "Hôm qua": [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "7 ngày trước": [moment().subtract(6, "days"), moment()],
      "30 ngày trước": [moment().subtract(29, "days"), moment()],
      "Tuần này": [moment().startOf("isoWeek"), moment().endOf("isoWeek")],
      "Tuần trước": [
        moment().subtract(7, "days").startOf("isoWeek"),
        moment().subtract(7, "days").endOf("isoWeek"),
      ],
      "Tháng này": [moment().startOf("month"), moment().endOf("month")],
      "Tháng trước": [
        moment().subtract(1, "month").startOf("month"),
        moment().subtract(1, "month").endOf("month"),
      ],
    },
    opens: "left",
    drops: "up",
    buttonClasses: ["btn", "btn-sm"],
    applyClass: "btn-success",
    cancelClass: "btn-secondary",
    separator: " to ",
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
  };

  var text =
    moment().startOf("month").locale("vi").format("D MMMM, YYYY") +
    " - " +
    moment().endOf("month").locale("vi").format("D MMMM, YYYY");
  $("#declaration-range-real-destination-date span").html(text);

  if ($("#declaration-range-real-destination-date").length > 0) {
    $("#declaration-range-real-destination-date").daterangepicker(
      config,
      function (start, end, label) {
        $("#declaration-range-real-destination-date span").html(
          start.locale("vi").format("D MMMM, YYYY") +
            " - " +
            end.locale("vi").format("D MMMM, YYYY")
        );
      }
    );
  }

  $(".parameter input[type=radio]").each((index, item) => {
    var val = $(item).val();
    var start, end;
    if (val == 1) {
      start = moment().startOf("month").format("DD/MM/YYYY");
      end = moment().endOf("month").format("DD/MM/YYYY");
    }
    // Tháng trước
    if (val == 2) {
      start = moment()
        .subtract(1, "month")
        .startOf("month")
        .format("DD/MM/YYYY");
      end = moment().subtract(1, "month").endOf("month").format("DD/MM/YYYY");
    }
    // Tuần này
    if (val == 3) {
      start = moment().startOf("isoWeek").format("DD/MM/YYYY");
      end = moment().endOf("isoWeek").format("DD/MM/YYYY");
    }
    // Tuần trước
    if (val == 4) {
      start = moment()
        .subtract(7, "days")
        .startOf("isoWeek")
        .format("DD/MM/YYYY");
      end = moment().subtract(7, "days").endOf("isoWeek").format("DD/MM/YYYY");
    }
    if (start && end) {
      $(item)
        .parent()
        .find("span")
        .html(start + " - " + end);
    }
  });
}

// Xuất dữ liệu
// CreatedBy nlhoang
function exportUpdateData() {
  $(".parameter input[type=radio]").on("change", function () {
    if ($(this).get(0).checked) {
      if ($(this).val() == 5) {
        $(".custom-parameter").addClass("show");
        $(".custom-parameter").removeClass("hide");
      } else {
        $(".custom-parameter").removeClass("show");
        $(".custom-parameter").addClass("hide");
      }
    }
  });

  $(".btn-declaration-export").on("click", function () {
    var data = {};

    var paramerter = $($(".parameter input[type=radio]:checked").get(0)).val();
    data.status = -1;
    // Tháng này
    if (paramerter == 1) {
      data.date_from = moment().startOf("month").format("YYYY-MM-DD");
      data.date_to = moment().endOf("month").format("YYYY-MM-DD");
    }
    // Tháng trước
    if (paramerter == 2) {
      data.date_from = moment()
        .subtract(1, "month")
        .startOf("month")
        .format("YYYY-MM-DD");
      data.date_to = moment()
        .subtract(1, "month")
        .endOf("month")
        .format("YYYY-MM-DD");
    }
    // Tuần này
    if (paramerter == 3) {
      data.date_from = moment().startOf("isoWeek").format("YYYY-MM-DD");
      data.date_to = moment().endOf("isoWeek").format("YYYY-MM-DD");
    }
    // Tuần trước
    if (paramerter == 4) {
      data.date_from = moment()
        .subtract(7, "days")
        .startOf("isoWeek")
        .format("YYYY-MM-DD");
      data.date_to = moment()
        .subtract(7, "days")
        .endOf("isoWeek")
        .format("YYYY-MM-DD");
    }
    if (paramerter == 5) {
      data.customer_id = $("#declaration-customer")
        .select2("data")
        .map((p) => p.id)
        .join(",");
      data.vehicle_team_id = $("#declaration-vehicle-team")
        .select2("data")
        .map((p) => p.id)
        .join(",");
      data.vehicle_id = $("#declaration-vehicle")
        .select2("data")
        .map((p) => p.id)
        .join(",");
      data.order_code = $("#declaration-order-code").val();
      data.status = $("#declaration-status")
        .select2("data")
        .map((p) => p.id)
        .join(",");
      // if (data.status == -1) {
      //     data.status = null;
      // }

      data.day_condition = $("#dayCondition").val();

      data.date_from = $("#declaration-range-real-destination-date")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
      data.date_to = $("#declaration-range-real-destination-date")
        .data("daterangepicker")
        .endDate.format("YYYY-MM-DD");
    }
    var url =
      declarationUri +
      "?" +
      Object.keys(data)
        .map((k) => `${encodeURIComponent(k)}=${encodeURIComponent(data[k])}`)
        .join("&");
    window.open(url);
  });
}

// Format định số
// CreatedBy nlhoang 30/10/2019
function convertFormatNumber(val) {
  var result = val;
  if (typeof val === "string") {
    result = parseFloat(val.replace(/\./g, "").replace(/,/g, "."));
    if (Number.isNaN(result)) {
      result = 0;
    }
  }
  return result;
}

// Tính dữ liệu thể tích, trọng lượng
// CreatedBy nlhoang 30/10/2019
function setTotal($tr, type) {
  var unitType = $tr.find("input[data-field=" + type + "]").val();
  var quantity = $tr.find("input[data-field=quantity]").val();
  var total = convertFormatNumber(unitType) * convertFormatNumber(quantity);
  $tr.find("input[data-field=total_" + type + "]").val(formatNumber(total));
  // $tr.find('input[data-field=total_' + type + ']').html(formatNumber(total));
}

// Tính tổng thông tin thể tích, trọng lượng
// CreatedBy nlhoang 30/10/2019
function setTotalGoods() {
  let tableGoods = $(".table-goods"),
    weights = tableGoods
      .find("tbody tr input[data-field=total_weight]")
      .toArray(),
    totalWeight = weights
      .map((p) => convertFormatNumber($(p).val()))
      .reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  $("input[name=weight]").val(formatNumber(totalWeight));

  let volumes = tableGoods
      .find("tbody tr input[data-field=total_volume]")
      .toArray(),
    totalVolume = volumes
      .map((p) => convertFormatNumber($(p).val()))
      .reduce((accumulator, currentValue) => accumulator + currentValue, 0);
  $("input[name=volume]").val(formatNumber(totalVolume));
}

// Xử lý sự kiện sau khi Chọn hàng háo từ form xuống
// CreatedBy nlhoang 29/10/2019
function goodsSearchCallback(selectData, goods) {
  if (goods.length > 0) {
    $(".table-goods").removeClass("hide");
    $("#btn-goods-search").removeClass("hide");
    $(".wrap-add-field").addClass("hide");
  }
  let tableGoods = $(".table-goods"),
    tableBody = tableGoods.find("tbody"),
    trLast = tableBody.find("tr:first"),
    length = tableGoods.find("tbody tr").length - 1;
  if (length > 0) {
    var name = $($(".table-goods").find("tbody tr")[length])
      .find("input[type=hidden]")
      .attr("name");
    var matches = name.match(/\[([0-9]+)\]/);
    if (null != matches) {
      length = parseInt(matches[1], 10) + 1;
    }
  } else {
    length = length + 1;
  }

  $.each(goods, (index, item) => {
    let trNew = trLast.clone();

    trNew.find("td .form-control[data-field]").each(function (idx, el) {
      let field = $(el).attr("data-field") || null;
      $(el).attr("name", "goods[" + (index + length) + "][" + field + "]");
      if (field === "goods_type") {
        $(el).val(item[field]);
      } else if (item[field]) {
        $(el).val(formatNumber(item[field]));
      }
    });
    trNew.addClass("show").removeClass("hide");
    tableGoods.find("tbody").append(trNew);
  });
  setTotalGoods();

  $(".number-input")
    .toArray()
    .forEach(function (field) {
      new Cleave(field, {
        numeral: true,
        numeralDecimalMark: ",",
        delimiter: ".",
        numeralDecimalScale: 4,
        numeralThousandsGroupStyle: "thousand",
      });
    });
}

// Đăng ký sự kiện xử lý nút thêm mới địa điểm
function registerAddLocation() {
  let indexDestination = $(".location-order-destination").find(".location-item")
      .length,
    indexArrival = $(".location-order-arrival").find(".location-item").length;

  $(document).on(
    "click",
    "#arrival-plus-button, #destination-plus-button",
    function (e) {
      e.preventDefault();
      e.stopPropagation();
      let btn = $(this),
        cardBody = btn.parents(".card-body"),
        locationOrder = cardBody.find(".location-order"),
        locationItem = btn
          .closest(".add-block")
          .find(".location-item-default")
          .clone(),
        type,
        index;

      if (locationItem.hasClass("location-destination")) {
        type = "locationDestinations";
        index = indexDestination;
        indexDestination++;
      } else {
        type = "locationArrivals";
        index = indexArrival;
        indexArrival++;
      }
      locationItem.removeClass("location-item-default").removeClass("hide");
      locationItem.find(".form-control[data-field]").each(function () {
        let input = $(this),
          field = input.data("field");
        input.attr("name", type + "[" + index + "][" + field + "]");
      });
      locationOrder.append(locationItem);
      let selectLocation = locationItem.find(".select-location-add");
      cboSelect2.location(urlLocation, selectLocation, true);
      locationItem.find(".timepicker").datetimepicker({
        format: "HH:mm",
        locale: "vi",
      });
      locationItem.find(".datepicker").datetimepicker({
        format: "DD-MM-YYYY",
        locale: "vi",
        useCurrent: false,
      });
    }
  );
  $(document).on("click", ".delete-location", function (e) {
    if ($(this).hasClass("disabled")) return;
    $(this).closest(".location-item").remove();
  });
}

// Xử lý import excel order
function processExcel() {
  let importSuccess = false,
      importJs = false,
      importModal = $("#import_excel");

  importModal.each(function () {
    let modal = $(this),
        form = modal.find("form"),
        importBtn = modal.find("#import-button"),
        nextBtn = modal.find("#import-button-next"),
        backBtn = modal.find("#import-button-back"),
        closeBtn = modal.find(".close-import-modal"),
        input = modal.find("#import-excel"),
        fileInput = input[0],
        formData = new FormData(),
        container = form.find(".result-import"),
        url = form.attr("action"),
        label = $(".box__input").find("label"),
        checkDataStep = modal.find(".wizard-step.check-data"),
        contentOne = modal.find(".wizard-content-1"),
        contentTwo = modal.find(".wizard-content-2"),
        contentThree = modal.find(".wizard-content-3"),
        progressLine = modal.find(".wizard-progress-line"),
        importDone = modal.find(".import-done"),
        fileUpload;

    modal.on("show.bs.modal", function () {
      backBtn.addClass("d-none");
      nextBtn.removeClass("d-none");
      importBtn.addClass("d-none");
      contentOne.addClass("active");
      checkDataStep.removeClass("active");
      contentTwo.removeClass("active");
      progressLine.css("width", "33%");
      $(".import-type #create").prop('checked','checked');
      $(".import-type #update").prop('checked','');
      input.val('');
      modal.find('.box.has-advanced-upload .text-success').html('<strong style="color: blue">Chọn tệp</strong><span class="box__dragndrop" style="color: black"> hoặc kéo thả tệp vào vùng này</span>.');

      if (importJs) return;
      nextBtn.prop("disabled", true);
      $.getScript(baseUrl + "/js/backend/vendor/xlsx.full.min.js", function () {
        nextBtn.prop("disabled", false);
      });
      importJs = true;
    });

    nextBtn.on("click", function (e) {
      e.preventDefault();
      var el = $(this);
      el.prop("disabled", true);
      setTimeout(function () {
        el.prop("disabled", false);
      }, 1500);

      if (!fileInput.files[0]) {
        return false;
      }

      $("#filter_fail").closest(".result-import").removeClass("filter-fail");

      let file = fileInput.files[0],
          reader = new FileReader(),
          ext = file.name.split(".").pop(),
          result = [],
          resultGoods = [];
      fileUpload = file;
      reader.onload = function (e) {
        showLoading();
        (result = []), (resultGoods = []);
        formData = new FormData();
        let data = e.target.result,
            wb,
            readType = { type: rABS ? "binary" : "base64" };
        try {
          wb = XLSX.read(data, readType);
          let res = to_json(wb),
              sheet = res[Object.keys(res)[0]],
              sheetGoods = res[Object.keys(res)[1]];
          let i = typeof headerRow !== "undefined" ? headerRow : 10;
          for (i; i < sheet.length; i++) {
            let row = sheet[i];
            if (isEmpty(row[0])) {
              continue;
            }
            result.push(row);
          }

          for (i = 1; i < sheetGoods.length; i++) {
            let row = sheetGoods[i];
            if (i != 1 && isEmpty(row[0])) {
              continue;
            }
            resultGoods.push(row);
          }

          formData.append("data", JSON.stringify(result));
          formData.append("dataGoods", JSON.stringify(resultGoods));

          if (typeof customImportFormData === "function") {
            customImportFormData(formData);
          }
          sendRequest(
              {
                url: url,
                type: "POST",
                data: formData,
                async: true,
                processData: false,
                contentType: false,
                beforeSend: function () {},
              },
              function (response) {
                if (!response.ok) {
                  return showErrorFlash(response.message);
                } else {
                  container.html(response.data.content);
                  label.html(response.data.label);
                  input.val("");
                  backBtn.removeClass("d-none");
                  nextBtn.addClass("d-none");
                  importBtn.removeClass("d-none");

                  checkDataStep.addClass("active");
                  contentOne.removeClass("active");
                  contentTwo.addClass("active");
                  progressLine.css("width", "66%");

                  let switchBtn = container.find(".switchery");
                  if (switchBtn.length > 0) {
                    new Switchery(switchBtn[0]);
                  }
                }
              }
          );
        } catch (e) {
          console.log(e);
        }
      };
      if (ext === "xlsx" || ext === "xls") {
        if (rABS) reader.readAsBinaryString(file);
        else reader.readAsArrayBuffer(file);
      }
    });

    closeBtn.on("click", function (e) {
      e.preventDefault();
      modal.find(".wizard-content").removeClass("active");
      contentOne.addClass("active");
      checkDataStep.removeClass("active");
      importDone.removeClass("active");
      progressLine.css("width", "33.33%");
      label.html("");

      if (!importBtn.hasClass("d-none")) importBtn.addClass("d-none");
      if (!backBtn.hasClass("d-none")) backBtn.addClass("d-none");
      if (nextBtn.hasClass("d-none")) nextBtn.removeClass("d-none");
    });

    backBtn.on("click", function (e) {
      e.preventDefault();
      importBtn.addClass("d-none");
      nextBtn.removeClass("d-none");
      backBtn.addClass("d-none");

      if($('#create').length)
        $('#create').prop('checked',true);

      modal.find(".wizard-content").removeClass("active");
      contentOne.addClass("active");
      checkDataStep.removeClass("active");
      importDone.removeClass("active");
      progressLine.css("width", "33.33%");
    });

    importBtn.on("click", function (e) {
      e.preventDefault();
      var el = $(this);
      el.prop("disabled", true);
      setTimeout(function () {
        el.prop("disabled", false);
      }, 1500);

      let formDataImport = new FormData();
      formDataImport.append("import_file", "1");
      if (typeof customImportFormData === "function") {
        customImportFormData(formDataImport);
      }
      formDataImport.append("file", fileUpload);

      sendRequest(
          {
            url: url,
            type: "POST",
            data: formDataImport,
            async: true,
            processData: false,
            contentType: false,
          },
          function (response) {
            if (!response.ok) {
              return showErrorFlash(response.message);
            } else {
              label.html(response.data.label);
              contentThree.html(response.data.content);
              input.val("");

              progressLine.css("width", "100%");
              if (!backBtn.hasClass("d-none")) backBtn.addClass("d-none");
              if (!importBtn.hasClass("d-none")) importBtn.addClass("d-none");
              if (!nextBtn.hasClass("d-none")) nextBtn.addClass("d-none");
              contentOne.removeClass("active");
              contentTwo.removeClass("active");
              contentThree.addClass("active");
              importDone.addClass("active");
              importSuccess = true;
            }
          }
      );
    });

    $(document).on("change", "#filter_fail", function () {
      let input = $(this),
          checked = input.is(":checked"),
          importResult = input.closest(".result-import");
      if (checked) {
        !importResult.hasClass("filter-fail")
            ? importResult.addClass("filter-fail")
            : "";
      } else {
        importResult.removeClass("filter-fail");
      }
    });
  });

  importModal.on("hidden.bs.modal", function (e) {
    if (importSuccess) {
      oneLogGrid._ajaxSearch($(".list-ajax"));
      importSuccess = false;
      $(".wizard-step").removeClass("active");
      $(".wizard-step.import-wizard").addClass("active");
      $(".wizard-progress-line").css("width", "33.33%");
      $(".wizard-content-1").addClass("active");
      $(".wizard-content-3").removeClass("active");
    }
  });
}

// Tự động đồng bộ dữ liệu từ Google Drive
// CreatedBy nlhoang 13/02/2020
function uploadFromGoogleDrive() {
  $(document).on("click", "#direct-edit-button", function () {
    window.open(editGoogleSheetUrl, "_blank");
  });
  $(document).on("click", "#google_drive-button", function () {
    var importModal = $("#import_excel");
    var form = importModal.find("form"),
      importBtn = importModal.find("#import-button"),
      nextBtn = importModal.find("#import-button-next"),
      backBtn = importModal.find("#import-button-back"),
      closeBtn = importModal.find(".close-import-modal"),
      container = form.find(".result-import"),
      url = form.attr("action"),
      label = $(".box__input").find("label"),
      checkDataStep = importModal.find(".wizard-step.check-data"),
      contentOne = importModal.find(".wizard-content-1"),
      contentTwo = importModal.find(".wizard-content-2"),
      contentThree = importModal.find(".wizard-content-3"),
      progressLine = importModal.find(".wizard-progress-line"),
      importDone = importModal.find(".import-done"),
      directEditButton = importModal.find("#direct-edit-button");

    var result = [],
      resultGoods = [];
    showLoading();

    fetch(googleSheetUrl)
      .then((resp) => resp.blob())
      .then((blob) => {
        let reader = new FileReader(),
          ext = "xlsx",
          result = [],
          resultGoods = [];
        var formData = new FormData();
        reader.onload = function (e) {
          let data = e.target.result,
            wb,
            readType = { type: rABS ? "binary" : "base64" };
          wb = XLSX.read(data, readType);
          let res = to_json(wb),
            sheet = res[Object.keys(res)[0]],
            sheetGoods = res[Object.keys(res)[1]];
          let i = typeof headerRow !== "undefined" ? headerRow : 10;
          for (i; i < sheet.length; i++) {
            let row = sheet[i];
            if (isEmpty(row[0])) {
              continue;
            }
            result.push(row);
          }

          for (i = 1; i < sheetGoods.length; i++) {
            let row = sheetGoods[i];
            if (i != 1 && isEmpty(row[0])) {
              continue;
            }
            resultGoods.push(row);
          }

          formData.append("data", JSON.stringify(result));
          formData.append("dataGoods", JSON.stringify(resultGoods));

          formData.append("check_data", "1");
          if (typeof customImportFormData === "function") {
            customImportFormData(formData);
          }
          sendRequest(
            {
              url: url,
              type: "POST",
              data: formData,
              async: true,
              processData: false,
              contentType: false,
              beforeSend: function () {},
            },
            function (response) {
              if (!response.ok) {
                return showErrorFlash(response.message);
              } else {
                container.html(response.data.content);
                label.html(response.data.label);
                backBtn.addClass("d-none");
                nextBtn.addClass("d-none");
                directEditButton.removeClass("d-none");
                importBtn.removeClass("d-none");
                checkDataStep.addClass("active");
                contentOne.removeClass("active");
                contentTwo.addClass("active");
                progressLine.css("width", "66%");
                importModal.modal("show");
              }
            }
          );
        };
        if (rABS) reader.readAsBinaryString(blob);
      })
      .catch(() => alert("Get google sheet error!"));
  });
}

// Đăng ký sự kiện xử lý nút Cập nhật chứng từ
// CreatedBy nlhoang 13/02/2020
function registerMassUpdateDocuments() {
  $("#btn-mass-update-documents").on("click", function () {
    var ids = [],
      strIds;
    if ($("#type_update").val() == "normal") {
      var id = $("#order_show").attr("data-id");
      ids.push(id);
      strIds = ids.join(",");
    } else {
      strIds = $(".selected_item").val();
    }

    sendRequest(
      {
        url: updateDocumentsUri,
        type: "POST",
        data: {
          ids: strIds,
          date_collected_documents_reality: $(
            "#date_collected_documents_reality"
          ).val(),
          time_collected_documents_reality: $(
            "#time_collected_documents_reality"
          ).val(),
        },
      },
      function (response) {
        if (!response.ok) {
          toastr["error"](response.message);
        } else {
          toastr["success"]("Cập nhật trạng thái thành công");
          $("#mass_update_documents").modal("hide");
          oneLogGrid._ajaxSearch($(".list-ajax"));
        }
      }
    );
  });
}

// Đăng ký sự kiện xử lý nút In Vận đơn giao
function registerPrintBill() {
  function _base64ToArrayBuffer(base64) {
    var binary_string = window.atob(base64);
    var len = binary_string.length;
    var bytes = new Uint8Array(len);
    for (var i = 0; i < len; i++) {
      bytes[i] = binary_string.charCodeAt(i);
    }
    return bytes.buffer;
  }

  $(document).on("click", ".btn-print-bill", function () {
    var ids = [],
      strIds;
    // Xử lý trường hợp chọn nhiều đơn hàng
    if ($(this).data("type") == "single") {
      var id = $("#order_show").attr("data-id");
      if (!isEmpty(id)) {
        ids.push(id);
        strIds = ids.join(",");
      }
    } else {
      strIds = $(".selected_item").val();
    }

    sendRequest(
      {
        url: printBillFromUrlUri,
        type: "POST",
        data: {
          ids: strIds,
        },
      },
      function (response) {
        try {
          if (!response.ok) {
            console.log(response.message);
            toastr["error"]("Có lỗi xảy ra trong quá trình tải file vận đơn");
            return;
          }
          if (response.results.length === 0) {
            toastr["success"]("Hệ thống không tìm thấy file vận đơn");
            return;
          }
          if (response.results.length > 1) {
            var zip = new JSZip();
            response.results.map((o) => {
              zip.file(o.name + ".pdf", _base64ToArrayBuffer(o.data));
            });
            zip.generateAsync({ type: "blob" }).then(function (t) {
              saveAs(t, "DanhSachVanDon.zip");
            });
          } else {
            let pdfWindow = window.open("");
            pdfWindow.document.write(
              "<iframe width='100%' height='100%' src='data:application/pdf;base64, " +
                encodeURI(response.results[0].data) +
                "'></iframe>"
            );
          }
        } catch (e) {
          console.log(e);
        }
      }
    );
  });
}

// Cập nhật trạng thái chứng từ trên form Chi tiết
// CreatedBy nlhoang 14/02/2020
function registerUpdateDocuments() {
  $(document).on("click", "#btn-update-documents", function () {
    var code = $(this).parents(".form-info-wrap").attr("data-code");
    $("#span_orders").html(code);
    $("#type_update").val("normal");
    $("#mass_update_documents").modal("show");
  });

  $(document).on("click", "#btn_confirm_update_documents", function () {
    var content = "";
    $(".mass-destroy:checked").each(function () {
      var name = $(this).parents("tr").children("td[data-name=true]");
      var names = name
        .map(function (index, value) {
          return $(value).text().trim();
        })
        .get()
        .join("-");
      content = content.concat(names + ",");
    });
    $("#type_update").val("mass");
    $("#span_orders").html(content);
    $("#mass_update_documents").modal("show");
  });
}

// Đăng ký sự kiện export excel các đơn hàng được lựa chọn
function registerExportSelected() {
  $("#export_selected").off("click");
  $("#export_selected").on("click", function (e) {
    e.preventDefault();
    // let ids = [],
    //     btn = $(this),
    //     url = btn.data('url');
    // $(".mass-destroy:checked").each(function () {
    //     ids.push($(this).closest('tr').data('id'));
    // });
    // url = url + '?ids=' + ids.join(',');
    // window.open(url);

    e.preventDefault();
    let ids = $(".selected_item").val(),
      btn = $(this),
      url = btn.data("url");
    url = url + "?ids=" + ids + "&update=1";
    window.open(url);
  });
}

// Tự động gợi ý tên liên hệ, sđt, email khi chọn địa điểm
function registerSuggestion(element) {
  let value = element.val(),
    customerId = $("customer_id").val();
  if (isNaN(value) || value == null) return;

  sendRequest(
    {
      url: urlSuggestion,
      type: "GET",
      data: {
        id: value,
        customer_id: customerId,
      },
    },
    function (response) {
      if (!response.ok) {
        return showErrorFlash(response.message);
      } else {
        let contact = response.data.contact,
          container = element.closest(".location-container");

        if (!contact) return;
        container.find(".contact-email").val(contact.email);
        container.find(".contact-phone").val(contact.phone_number);
        container.find(".contact-name").val(contact.contact_name);
      }
    }
  );
}

// Đăng ký sự kiện xử lý nút tải mã QR
function registerDownloadQRCode() {
  function _base64ToArrayBuffer(base64) {
    var binary_string = window.atob(base64);
    var len = binary_string.length;
    var bytes = new Uint8Array(len);
    for (var i = 0; i < len; i++) {
      bytes[i] = binary_string.charCodeAt(i);
    }
    return bytes.buffer;
  }

  $(document).on("click", ".btn-qrcode", function () {
    var ids = [],
      strIds;

    strIds = $(".selected_item").val();

    sendRequest(
      {
        url: qrcodeUri,
        type: "POST",
        data: {
          ids: strIds,
        },
      },
      function (response) {
        try {
          if (!response.ok) {
            console.log(response.message);
            toastr["error"]("Có lỗi xảy ra trong quá trình tải file vận đơn");
            return;
          }
          if (response.results.length > 1) {
            var zip = new JSZip();
            response.results.map((o) => {
              zip.file(o.name + ".png", _base64ToArrayBuffer(o.content));
            });
            zip.generateAsync({ type: "blob" }).then(function (t) {
              saveAs(t, "DanhSachMaQR.zip");
            });
          } else {
            var [order] = response.results;
            let a = document.createElement("a");
            a.href = "data:image/png;base64," + order.content;
            a.download = order.name;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
          }
        } catch (e) {
          console.log(e);
        }
      }
    );
  });
}

function changeVAT() {
  $("#switchery_vat_default").on("change", function () {
    var checked = $("#switchery_vat_default").is(":checked");
    if (checked || $("#switchery_vat_default").length == 0) {
      $("#vat").val("1");
    } else {
      $("#vat").val("0");
    }
  });
}

// Tự động tính toán khối lượng thể tích của chuyến xe để đưa ra cảnh báo
function registerCalcRoute(element) {
  let routeId = element.val(),
    weight = $("input[name=weight]").val(),
    volume = $("input[name=volume]").val(),
    orderId = $("input[name=id]").val();

  if (isNaN(routeId) || routeId === "") return;

  sendRequest(
    {
      url: urlCalcCapacity,
      type: "GET",
      data: {
        id: routeId,
        weight: weight,
        volume: volume,
        order_id: orderId,
      },
      loading: false,
    },
    function (response) {
      if (!response.ok) {
        return showErrorFlash(response.message);
      } else {
        let warningMessage = $(".warning-message");
        if (response.data.status === "OK") {
          warningMessage.empty();
          return;
        }

        let message = response.data.message;
        warningMessage.html(message);
      }
    }
  );
}

//Tinh doanh thu
function registerCalcFinalAmount() {
  $(document).on(
    "keyup",
    "#commission_value,#amount,#anonymous_amount",
    function (event) {
      calcFinalAmount();
    }
  );

  $('#commission_type').on("change", function(e) {
    calcFinalAmount();
  });

}

function calcFinalAmount(){
  if (typeof $("#commission_value").val() === "undefined") return;
  let inputCommissionValue = $("#commission_value")
          .val()
          .replace(/\./g, "")
          .replace(/,/g, "."),
      commissionValue = Number.isNaN(parseFloat(inputCommissionValue))
          ? 0
          : parseFloat(inputCommissionValue);
  let inputAmount = $("#amount")
          .val()
          .replace(/\./g, "")
          .replace(/,/g, "."),
      amount = Number.isNaN(parseFloat(inputAmount))
          ? 0
          : parseFloat(inputAmount);
  let inputAnonymousAmount = $("#anonymous_amount")
          .val()
          .replace(/\./g, "")
          .replace(/,/g, "."),
      anonymousAmount = Number.isNaN(parseFloat(inputAnonymousAmount))
          ? 0
          : parseFloat(inputAnonymousAmount);

  var finalAmount = 0;
  var commissionAmount =0;
  if ($("#commission_type").val() == 1) {
    commissionAmount = amount * (commissionValue / 100);
    finalAmount =
        amount - commissionAmount - anonymousAmount;
  } else if ($("#commission_type").val() == 2) {
    commissionAmount = commissionValue;
    finalAmount = amount - commissionAmount - anonymousAmount;
  }
  $("#final_amount").val(formatNumber(finalAmount));
  $("#commission_amount").val(formatNumber(commissionAmount));
}


// MỞ form cập nhật doanh thu
// CreatedBy nlhoang 21/09/2020
function registerUpdateRevenue() {
  $(document).on("click", "#btn_confirm_update_revenue", function (e) {
    e.preventDefault();
    let ids = $(".selected_item").val(),
      btn = $(this),
      url = btn.data("url"),
      type = btn.data("type");
    url = url + "?ids=" + ids + "&type=" + type;
    showUpdateReveuneModal(url);
  });

  $(document).on("click", "#btn-mass-update-revenue", function (e) {
    e.preventDefault();
    let btn = $(this),
      url = btn.data("url"),
      modal = $("#modal_update_revenue");
    var data = [];
    modal.find("#body_content .amount").each(function (e, item) {
      let input = $(this),
        id = input.data("id"),
        value = input.val().replace(/\./g, "").replace(/,/g, ".");
      data.push({
        id: id,
        value: value,
      });
    });
    sendRequest(
      {
        url: url,
        data: {
          data: data,
        },
        type: "POST",
      },
      function (response) {
        toastr["success"]("Cập nhật doanh thu thành công");
        modal.modal("hide");
        $(".unselected-all-btn").trigger("click");
        oneLogGrid._ajaxSearch($(".list-ajax"), null, false);
      }
    );
  });

  function showUpdateReveuneModal(url) {
    sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        let modal = $("#modal_update_revenue");
        if (!response.ok) {
          toastr["error"]("Có lỗi xảy ra");
          return;
        }
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
        modal.modal("show");
      }
    );
  }
}

// MỞ form cập nhật số khung
// CreatedBy nlhoang 21/09/2020
function registerUpdateVinNo() {
  $(document).on("click", "#btn_confirm_update_vin_no", function (e) {
    e.preventDefault();
    let ids = $(".selected_item").val(),
      btn = $(this),
      url = btn.data("url"),
      type = btn.data("type");
    url = url + "?ids=" + ids + "&type=" + type;
    showUpdateModal(url);
  });

  $(document).on("click", "#btn-mass-update-vin-no", function (e) {
    e.preventDefault();
    let btn = $(this),
      url = btn.data("url"),
      modal = $("#modal_update_vin_no");
    var data = [];
    modal.find("#body_content .container-vin-no").each(function (e, item) {
      let input = $(this),
        id = input.data("id"),
        vin_no = input.find(".vin_no").val(),
        model_no = input.find(".model_no").val();
      data.push({
        id: id,
        vin_no: vin_no,
        model_no: model_no,
      });
    });
    sendRequest(
      {
        url: url,
        data: {
          data: data,
        },
        type: "POST",
      },
      function (response) {
        toastr["success"]("Cập nhật số khung thành công");
        modal.modal("hide");
        $(".unselected-all-btn").trigger("click");
        oneLogGrid._ajaxSearch($(".list-ajax"), null, false);
      }
    );
  });

  function showUpdateModal(url) {
    sendRequest(
      {
        url: url,
        type: "GET",
      },
      function (response) {
        let modal = $("#modal_update_vin_no");
        let data = response.data;
        modal.find(".modal-content").html(data.content);
        modal.modal("show");
      }
    );
  }
}

// MỞ form cập nhật đơn hàng Excel
// CreatedBy nlhoang 23/09/2020
function registerOpenEditor() {
  var openSheetmodal = function (ids) {
    var editor = $("#modal_order_editor");
    var url = editor.data("url");
    var $frame = $("#frame-order-editor");
    $frame.hide();
    $frame.on("load", function () {
      $frame.contents().find(".topbar").hide();
      $frame.contents().find(".left.side-menu").hide();
      $frame.contents().find(".content-page").css("margin", 0);
      $frame.contents().find(".content-page > .content").css("margin", 0);
      hideLoading();
      $frame.show();
    });
    if (ids) {
      url = url + "?ids=" + ids;
    }
    showLoading();
    $frame.attr("src", url);
    editor.modal("show");
  };

  $("#btn-editor").on("click", function () {
    openSheetmodal();
  });

  $("#btn-selected-editor").on("click", function () {
    let ids = $(".selected_item").val();
    openSheetmodal(ids);
  });
}

// Lựa chọn dữ liệu mặc định của KH
function registerChooseCustomerDefaultData() {
  $("#customer_id").on("change", function (event) {
    var customerId = $(this).val();
    if (customerId == null || customerId == "") return;
    var $modal = $("#default_data_modal");
    sendRequest(
      {
        url: urlDefaultData,
        type: "GET",
        data: {
          id: customerId,
        },
        loading: false,
      },
      function (response) {
        if (!response.ok) {
          return showErrorFlash(response.message);
        } else {
          if (response.data.content) {
            $modal.find(".modal-body").html(response.data.content);
            $modal.modal("show");
          } else {
            if (response.data.systemCodeConfig) {
              $("#code_config").select2("trigger", "select", {
                data: {
                  id: response.data.systemCodeConfig.id,
                  title: response.data.systemCodeConfig.prefix,
                },
              });
            }
            if (response.data.locationDestination) {
              $(
                "select[name=locationDestinations\\[0\\]\\[location_id\\]]"
              ).select2("trigger", "select", {
                data: {
                  id: response.data.locationDestination.id,
                  title: response.data.locationDestination.title,
                },
              });
            }
            if (response.data.locationArrival) {
              $(
                "select[name=locationArrivals\\[0\\]\\[location_id\\]]"
              ).select2("trigger", "select", {
                data: {
                  id: response.data.locationArrival.id,
                  title: response.data.locationArrival.title,
                },
              });
            }
          }
        }
      }
    );
  });

  $("#btn-default-data").on("click", function (event) {
    var itemID = $('input[name="radios"]:checked').val();

    var locationDestinationID = $("#hdf_location_destination_" + itemID).val();
    var locationArrivalID = $("#hdf_location_arrival_" + itemID).val();
    var systemCodeConfigID = $("#hdf_system_code_config_" + itemID).val();

    if (systemCodeConfigID) {
      var prefix = $(
        "span.system-code-config[data-id=" + systemCodeConfigID + "]"
      ).text();
      $("#code_config").select2("trigger", "select", {
        data: { id: systemCodeConfigID, title: prefix },
      });
    }
    if (locationDestinationID) {
      var title = $(
        "span.location-destination-info[data-id=" + locationDestinationID + "]"
      ).text();
      $("select[name=locationDestinations\\[0\\]\\[location_id\\]]").select2(
        "trigger",
        "select",
        {
          data: { id: locationDestinationID, title: title },
        }
      );
    }
    if (locationArrivalID) {
      var title = $(
        "span.location-arrival-info[data-id=" + locationArrivalID + "]"
      ).text();
      $("select[name=locationArrivals\\[0\\]\\[location_id\\]]").select2(
        "trigger",
        "select",
        {
          data: { id: locationArrivalID, title: title },
        }
      );
    }
    var $modal = $("#default_data_modal");
    $modal.modal("hide");
  });
}

function registerUpdatePartner(){
  $("#btn_confirm_update_partner").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);
    var url = $(this).data("url");

    var data = {
      order_ids: $(".selected_item").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
            return;
          }
          var data = response.data;
          if (data.driver.id) {
            $("#driver_ids").select2("trigger", "select", {
              data: { id: data.driver.id, title: data.driver.title },
            });
          } else {
            $("#driver_ids").empty();
            $("#driver_ids").val("").trigger("change");
          }
          if (data.vehicle.id) {
            $("#vehicle_ids").select2("trigger", "select", {
              data: { id: data.vehicle.id, title: data.vehicle.title },
            });
          } else {
            $("#vehicle_ids").empty();
            $("#vehicle_ids").val("").trigger("change");
          }
          if (data.partner.id) {
            $("#partner_id").select2("trigger", "select", {
              data: { id: data.partner.id, title: data.partner.title },
            });
          }

          $("#time").text(data.info.time);
          $("#total_weight").text(formatNumber(data.goods.total_weight));
          $("#total_volume").text(formatNumber(data.goods.total_volume));

          $(".total-order").text($(".selected_item").val().split(",").length);
          $("#update_partner_modal").modal("show");
        }
    );
  });

  $("#btn_update_partner").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn_update_partner").data("url");
    var data = {
      partner_id: $("#partner_id").val(),
      driver_id: $("#driver_ids").val(),
      vehicle_id: $("#vehicle_ids").val(),
      order_ids: $(".selected_item").val(),
    };
    if ($('#merge_route').length > 0 && $('#merge_route').is(":checked")) {
      data.merge_route= true;
    }
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          $("#update_partner_modal").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Cập nhật đối tác vận tải cho danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });

  if($('#partner_id').val() != 0){
    $('#vehicle_ids').prop('disabled', false);
    $('#driver_ids').prop('disabled', false);
  }else{
    $('#vehicle_ids').prop('disabled', true);
    $('#driver_ids').prop('disabled', true);
  }

  $('#partner_id').on("select2:select", function(e) {
    if($('#partner_id').val() != 0){
      $('#vehicle_ids').prop('disabled', false);
      $('#driver_ids').prop('disabled', false);
    }else{
      $('#vehicle_ids').prop('disabled', true);
      $('#driver_ids').prop('disabled', true);
    }
  });
}

$('#submit-quantity-order').on('click', function(){
    let quantities = $('#quantities-order').val();
    let url = $(this).attr('data-url');
    let id = $(this).attr('data-id');
    let urlDriverWithId = urlDriver;

    sendRequest({
            url: url,
            type: "GET",
            data: {
                // back_url_key: backUrlKey,
                // grid: true,
                quantities: parseFloat(quantities)
            },
        },
        function (response) {
            if (!response.ok) {
                return showErrorFlash(response.message);
            }

            $('#quantities-order').val(2);

            $('#split-order').modal('hide');

            $('#content-split-order').html(response.data.content);

            $('#modal-split-order').modal('show');

            $('.select-partner').each(function() {
                cboSelect2.partner(urlPartner, '#' + $( this ).attr('id'));
            });

            $('.select-vehicle-1').each(function() {
                cboSelect2.vehicle("");
            });

            $('.select-driver-1').each(function() {
                cboSelect2.driver("");
            });

            $('.select-partner').on("select2:select", function(e) {
                let partner_id = e.params.data.id;
                let id = e.currentTarget.dataset.index;

                $('#vehicle-id-' + id).val(null).trigger('change');
                $('#driver-id-' + id).val(null).trigger('change');

                $('#vehicle-id-' + id).prop('disabled', false);
                $('#driver-id-' + id).prop('disabled', false);
                cboSelect2.vehicle(urlVehicle, '#vehicle-id-' + id, {all: '', partner_id : partner_id}, '#driver-id-' + id);
            });

            $('.select-vehicle-1').on("select2:select", function(e) {

              let id = e.currentTarget.dataset.index;
              let partner_id = $('#partner-id-' + id).val();

              cboSelect2.driver(urlDriverWithId, '#driver-id-' + id,"", {partner_id : partner_id});
            });
        });
    });

function registerMergeOrder(){
  $("#btn_confirm_merge_order").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $(this).data("url");
    var data = {
      order_ids: $(".selected_item").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          var content = response.content;
          $("#merge_order_content").html(content);
          $("#merge_order_modal").modal("show");
        }
    );
  });

  $(document).on("click","#btn_confirm_merge_order_save",function(e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $(this).data("url");
    var data = {
      order_ids: $(".selected_item").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Gộp đơn vận tải thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
          $("#merge_order_modal").modal("hide");
        }
    );
  });
}
