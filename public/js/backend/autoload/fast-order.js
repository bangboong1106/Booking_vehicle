var cboFastOrder = (function () {
  this.createSelection = function () {
    if (typeof cboSelect2 !== "undefined") {
      if (typeof fastDriverDropdownUri !== "undefined") {
        cboSelect2.driver(fastDriverDropdownUri, ".fast-order-driver");
      }
      if (typeof fastVehicleDropdownUri !== "undefined") {
        cboSelect2.vehicle(fastVehicleDropdownUri, ".fast-order-vehicle");
      }
      if (typeof fastLocationDropdownUri !== "undefined") {
        cboSelect2.location(
          fastLocationDropdownUri,
          ".fast-order-location",
          true
        );
      }
      if (typeof fastCodeConfigDropDownUri !== "undefined") {
        cboSelect2.codeConfig(
          fastCodeConfigDropDownUri,
          ".fast-order-code-config"
        );
      }
      if (typeof fastCustomerDropdownUri !== "undefined") {
        cboSelect2.customer(fastCustomerDropdownUri, ".fast-order-customer");
      }
    }
  };

  this.changeSelection = function () {
    $("input[name=order_status]").click(function () {
      $("#status").val($(this).val());
    });

    let selectCustomer = $("#fast_order_customer_id"),
      inputCustomerName = $("#fast_order_customer_name"),
      inputCustomerMobileNo = $("#fast_order_customer_mobile_no");
    selectCustomer.on("select2:select", function (e) {
      let data = $(this).select2("data")[0],
        customerName = "",
        customerMobileNo = "";

      if (data) {
        customerName = data.title;
        customerMobileNo = data.mobile_no;
      }
      inputCustomerName.val(customerName);
      inputCustomerMobileNo.val(customerMobileNo);
    });

    selectCustomer.on("select2:select", function (event) {
      var customerId = $(this).val();
      var $modal = $("#default_data_modal");
      var url = $(this).data("url");
      sendRequest(
        {
          url: url,
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
                $("#fast_order_code_config").select2("trigger", "select", {
                  data: {
                    id: response.data.systemCodeConfig.id,
                    title: response.data.systemCodeConfig.prefix,
                  },
                });
              }
              if (response.data.locationDestination) {
                $(
                  "#fast_order_location_destination_id"
                ).select2("trigger", "select", {
                  data: {
                    id: response.data.locationDestination.id,
                    title: response.data.locationDestination.title,
                  },
                });
              }
              if (response.data.locationArrival) {
                $(
                  "#fast_order_location_arrival_id"
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

    $(document).on("click", ".fast-order-form #btn-default-data", function (event) {
      event.preventDefault();
      var itemID = $('input[name="radios"]:checked').val();

      var locationDestinationID = $(
        "#hdf_location_destination_" + itemID
      ).val();
      var locationArrivalID = $("#hdf_location_arrival_" + itemID).val();
      var systemCodeConfigID = $("#hdf_system_code_config_" + itemID).val();

      if (systemCodeConfigID) {
        var prefix = $(
          "span.system-code-config[data-id=" + systemCodeConfigID + "]"
        ).text();
        $("#fast_order_code_config").select2("trigger", "select", {
          data: { id: systemCodeConfigID, title: prefix },
        });
      }
      if (locationDestinationID) {
        var title = $(
          "span.location-destination-info[data-id=" +
            locationDestinationID +
            "]"
        ).text();
        $("#fast_order_location_destination_id").select2(
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
        $("#fast_order_location_arrival_id").select2(
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
  };

  return {
    createSelection: createSelection,
    changeSelection: changeSelection,
  };
})();
