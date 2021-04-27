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
            toastr["error"](response.message);
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
          toastr["error"](response.message);
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
          toastr["error"](response.message);
        } else {
          toastr["success"]("Tạo chuyến cho danh sách đơn hàng thành công");
          $(".unselected-all-btn").trigger("click");
          oneLogGrid._ajaxSearch($(".list-ajax"));
        }
      }
    );
  });
});
