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
  if (typeof cboSelect2 !== "undefined") {
    if (typeof driverDropdownUri !== "undefined") {
      cboSelect2.driver(driverDropdownUri);
    }
    if (typeof vehicleDropdownUri !== "undefined") {
      cboSelect2.vehicle(vehicleDropdownUri);
    }
    if (typeof routeDropdownUri !== "undefined") {
      cboSelect2.routes(routeDropdownUri);
    }
  }

  if (typeof createDriverQuickSearch != "undefined") {
    var quickSearch = createDriverQuickSearch();

    var config = {};
    quickSearch(config).init();
  }

  if (typeof createVehicleQuickSearch != "undefined") {
    var quickSearch = createVehicleQuickSearch();
    var config = {};
    quickSearch(config).init();
  }

  if (typeof createRouteQuickSearch != "undefined") {
    var quickSearch = createRouteQuickSearch();
    var config = {};
    config.searchElement = "btn-merge-order";
    config.searchType = "element";
    config.searchCallback = function (selectData, items) {
      var data = {
        route_id: items[0].id,
        order_ids: $(".selected_item").val(),
      };
      var url = $("#btn-merge-order").data("url");
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
            toastr["success"]("Ghép chuyến cho danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
      );
    };
    quickSearch(config).init();
  }

  if ($("#mapChild").length > 0) {
    google.maps.event.addDomListener(window, "load", initMapPage);
  }

  $("#btn-confirm-create-route").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);
    var url = $(this).data("default");

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
        $("#time").text(data.info.time);
        $("#total_weight").text(formatNumber(data.goods.total_weight));
        $("#total_volume").text(formatNumber(data.goods.total_volume));

        $(".total-order").text($(".selected_item").val().split(",").length);
        $("#create_route_modal").modal("show");
      }
    );
  });

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

  registerCreateRoute();

  registerChangeStatusOrder();

  registerMassUpdateDocuments();

});

function registerCreateRoute(){
  $("#btn-create-route").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn-merge-order").data("url");
    var data = {
      route_id: null,
      driver_id: $("#driver_ids").val(),
      vehicle_id: $("#vehicle_ids").val(),
      order_ids: $(".selected_item").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          $("#create_route_modal").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Tạo chuyến cho danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });
}

function registerChangeStatusOrder(){
  $("#btn_request_edit").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn_request_edit").data("url");
    var data = {
      order_ids: $(".selected_item").val(),
      reason: $("#reason").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          $("#dialog_request_edit").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Yêu cầu sửa cho danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });

  $("#btn_order_cancel").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn_order_cancel").data("url");
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
          $("#dialog_order_cancel").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Hủy danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });

  $("#btn_accept_order").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn_accept_order").data("url");
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
          $("#dialog_accept_order").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Xác nhận danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });

  $("#btn_order_complete").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    var url = $("#btn_order_complete").data("url");
    var data = {
      order_ids: $(".selected_item").val(),
      ETD_date_reality : $("#complete_etd_date_reality").val(),
      ETD_time_reality : $("#complete_etd_time_reality").val(),
      ETA_date_reality : $("#complete_eta_date_reality").val(),
      ETA_time_reality : $("#complete_eta_time_reality").val(),
    };
    sendRequest(
        {
          url: url,
          type: "POST",
          data: data,
        },
        function (response) {
          $("#dialog_order_complete").modal("hide");
          if (response.errorCode != 0) {
            toastr["error"](response.errorMessage);
          } else {
            toastr["success"]("Hoàn thành danh sách đơn hàng thành công");
            $(".unselected-all-btn").trigger("click");
            oneLogGrid._ajaxSearch($(".list-ajax"));
          }
        }
    );
  });
}

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
