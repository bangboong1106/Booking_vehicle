let me = this;
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
    if (typeof comboDriverUri !== "undefined") {
      cboSelect2.driver(comboDriverUri);
    }
    if (typeof comboCustomerUri !== "undefined") {
      cboSelect2.customer(comboCustomerUri);
    }
    if (typeof comboVehicleTeamUri !== "undefined") {
      cboSelect2.vehicleTeam(comboVehicleTeamUri);
    }
    if (typeof comboPartnerUri !== "undefined" && $('.select-partner').length > 0) {
      cboSelect2.partner(comboPartnerUri);
    }
  }

  if (typeof createDriverQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createDriverQuickSearch();
    if (typeof exceptIds != "undefined") {
      var config = {};
      config.exceptIds = exceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createVehicleTeamQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createVehicleTeamQuickSearch();
    if (typeof exceptIds != "undefined") {
      var config = {};
      config.exceptIds = exceptIds;
      quickSearch(config).init();
    }
  }

  if (typeof createCustomerQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createCustomerQuickSearch();
    if (typeof exceptIds != "undefined") {
      var config = {};
      config.exceptIds = exceptIds;
      quickSearch(config).init();
    }
  }

  $("#btnApply").on("click", function () {
    var data = createRequestData();

    sendRequest(
      {
        url: reportUri,
        type: "POST",
        data: data.data,
      },
      function (response) {
        // response.summary = {
        //   driver_name: 1233,
        //   order_number: 122,
        // };
        try {
          if (!response) return;
          var results = response.data;
          var start = $("#reportrange")
            .data("daterangepicker")
            .startDate.format("DD-MM-YYYY");
          var end = $("#reportrange")
            .data("daterangepicker")
            .endDate.format("DD-MM-YYYY");
          $(".row.title span.parameter").html(` (từ ${start} đến ${end})`);

          $(".card-box.result").css("display", "block");

          if (results.length != 0) {
            Tool.serverData = results;
            var template = generateReportTemplate(
              results,
              data.clientData,
              response.summary
            );
            $(".report-content").html(template);
            $(".report-content").removeClass("hide");
            $(".empty-box").addClass("hide");
          } else {
            $(".empty-box").removeClass("hide");
            $(".report-content").addClass("hide");
          }
        } catch (ex) {
          console.log(ex);
          toastr["error"](
            "Có lỗi xảy ra khi lấy thông tin. Vui lòng thử lại sau"
          );
        }
      }
    );
  });

  $("#btnDefault").on("click", function () {
    $("#vehicle_team_ids").empty().trigger("change");
    $("#driver_ids").empty().trigger("change");
    $("#reportrange span").html(
      moment().startOf("month").format("D MMMM, YYYY") +
        " - " +
        moment().endOf("month").format("D MMMM, YYYY")
    );
    $("#reportrange")
      .data("daterangepicker")
      .setStartDate(moment().startOf("month"));
    $("#reportrange")
      .data("daterangepicker")
      .setEndDate(moment().endOf("month"));
  });
});

function createRequestData() {
  var data = {};
  data.VehicleTeamIDs = $("#vehicle_team_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.DriverIDs = $("#driver_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.CustomerIDs = $("#customer_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.DayCondition = $("#dayCondition").val();
  data.FromDate = $("#reportrange")
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  data.ToDate = $("#reportrange")
    .data("daterangepicker")
    .endDate.format("YYYY-MM-DD");
  
  if ($('#partner_id').length >0 ) {
    data.PartnerIDs = $("#partner_id")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  }
  
  return {
    data: data,
    clientData: data,
  };
}

function generateReportTemplate(results) {
  var table = '<table class="table table-bordered">';
  var headerTemplate = `<thead>`;
  headerTemplate += "<tr>";
  headerTemplate += `<th class="vertical-middle" rowspan=2>Đội tài xế</th>`;
  headerTemplate += `<th class="vertical-middle" rowspan=2>Tài xế</th>`;
  headerTemplate += `<th class="vertical-middle" rowspan=2>Số lượng đơn hàng</th>`;
  headerTemplate += `<th colspan=2>Đơn hoàn thành</th>`;
  headerTemplate += `<th colspan=2>Đơn hoàn thành đúng giờ</th>`;
  headerTemplate += `<th colspan=2>Tương tác tài xế</th>`;
  headerTemplate += "</tr>";
  headerTemplate += "<tr class='sub-header'>";
  headerTemplate += `<th >Số lượng hoàn thành</th>`;
  headerTemplate += `<th >Tỉ lệ (%)</th>`;
  headerTemplate += `<th >Số lượng hoàn thành đúng giờ</th>`;
  headerTemplate += `<th >Tỉ lệ (%)</th>`;
  headerTemplate += `<th >Số lượng đơn có tương tác</th>`;
  headerTemplate += `<th >Tỉ lệ (%)</th>`;
  headerTemplate += "</tr>";
  headerTemplate += "</thead>";
  var bodyTemplate = `<tbody>`;

  results.forEach((result) => {
    if (!result.drivers || result.drivers.length === 0) {
      bodyTemplate += `<tr>`;
      bodyTemplate += `<td >${result.vehicle_team_name}</td>`;
      bodyTemplate += `<td ></td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `<td class="text-right">0</td>`;
      bodyTemplate += `</tr>`;
    } else {
      var rowSpan = result.drivers.length;
      result.drivers.map((driver, index) => {
        bodyTemplate += `<tr >`;
        if (index == 0) {
          bodyTemplate += `<td rowspan=${rowSpan}>${result.vehicle_team_name}</td>`;
        }
        bodyTemplate += `<td ><span>${driver.driver_name}</span></td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.total_order || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.total_order_complete || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.ratio_order_complete || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.total_order_on_time || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.ratio_order_on_time || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.total_order_interactive || 0
        )}</td>`;
        bodyTemplate += `<td class="text-right">${formatNumber(
          driver.ratio_order_interactive || 0
        )}</td>`;
        bodyTemplate += `</tr>`;
      });
    }
  });

  bodyTemplate += "</tbody>";

  table += headerTemplate + bodyTemplate;
  table += "</table>";
  return table;
}

// Tạo dữ liệu báo cáo
// CreatedBy nlhoang 13/07/2020
function generateReportTable(response) {
  return response;
}

function generateHeader(results, config, n) {
  var headerTemplate = `<row>`;
  headerTemplate += n.generateCellString("Đội tài xế");
  headerTemplate += n.generateCellString("Tài xế");
  headerTemplate += n.generateCellString("Số lượng đơn hàng");
  headerTemplate += n.generateCellString("Số lượng đơn hoàn thành");
  headerTemplate += n.generateCellString("Tỉ lệ đơn hoàn thành");
  headerTemplate += n.generateCellString("Số lượng đơn hoàn thành đúng giờ");
  headerTemplate += n.generateCellString("Tỉ lệ đơn hoàn thành đúng giờ");
  headerTemplate += n.generateCellString("Số lượng đơn tài xế tương tác");
  headerTemplate += n.generateCellString("Tỉ lệ đơn tài xế tương tác");
  headerTemplate += "</row>";
  return headerTemplate;
}

function generateRows(results, config, n) {
  var bodyTemplate = ``;
  results.forEach((result, index) => {
    var childTemplate = "";
    if (!result.drivers || result.drivers.length === 0) {
      childTemplate += `<row>`;
      childTemplate += n.generateCellString(result.vehicle_team_name);
      childTemplate += n.generateCellString("");
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += n.generateCellNumber(0);
      childTemplate += `</row>`;
    } else {
      result.drivers.map((driver, idx) => {
        childTemplate += "<row>";

        if (idx === 0) {
          childTemplate += n.generateCellString(
            result.vehicle_team_name == null ? "" : result.vehicle_team_name
          );
        } else {
          childTemplate += n.generateCellString("");
        }
        childTemplate += n.generateCellString(
          driver.driver_name == null ? "" : driver.driver_name
        );
        childTemplate += n.generateCellNumber(
          driver.total_order == null ? 0 : driver.total_order
        );
        childTemplate += n.generateCellNumber(
          driver.total_order_complete == null ? 0 : driver.total_order_complete
        );
        childTemplate += n.generateCellNumber(
          driver.ratio_order_complete == null ? 0 : driver.ratio_order_complete
        );
        childTemplate += n.generateCellNumber(
          driver.total_order_on_time == null ? 0 : driver.total_order_on_time
        );
        childTemplate += n.generateCellNumber(
          driver.ratio_order_on_time == null ? 0 : driver.ratio_order_on_time
        );
        childTemplate += n.generateCellNumber(
          driver.total_order_interactive == null
            ? 0
            : driver.total_order_interactive
        );
        childTemplate += n.generateCellNumber(
          driver.ratio_order_interactive == null
            ? 0
            : driver.ratio_order_interactive
        );
        childTemplate += "</row>";
      });
    }
    bodyTemplate += childTemplate;
  });
  return bodyTemplate;
}
