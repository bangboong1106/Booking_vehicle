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
    if (typeof comboCustomerUri !== "undefined") {
      cboSelect2.customer(comboCustomerUri);
    }
    if (typeof comboCustomerGroupUri !== "undefined") {
      cboSelect2.customerGroup(comboCustomerGroupUri);
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
        //   customer_name: 1233,
        //   order_number: 122,
        // };
        try {
          if (!response) return;
          var results = generateReportTable(response.data);
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
    $("#customer_group_ids").empty().trigger("change");
    $("#customer_ids").empty().trigger("change");
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
  data.CustomerGroupIDs = $("#customer_group_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.CustomerIDs = $("#customer_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.FromDate = $("#reportrange")
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  data.ToDate = $("#reportrange")
    .data("daterangepicker")
    .endDate.format("YYYY-MM-DD");
  data.DayCondition = $("#dayCondition").val();
  return {
    data: data,
    clientData: data,
  };
}

// Tạo dữ liệu báo cáo
// CreatedBy nlhoang 13/07/2020
function generateReportTable(response) {
  //   response = [
  //     {
  //       customer_name: "Hoàng",
  //       order_number: 122,
  //       revenue: 300,
  //       cost: 100,
  //       profit: 200,
  //     },
  //     {
  //       customer_name: "Tuấn",
  //       order_number: 133,
  //       revenue: 400,
  //       cost: 40,
  //       profit: 230,
  //     },
  //   ];
  return response;
}

function generateReportTemplate(results, data, summary) {
  var table = '<table class="table table-bordered">';
  var headerTemplate = `<thead>`;
  headerTemplate += `<th style="width: 180px">Khách hàng</th>`;
  headerTemplate += `<th >Số lượng đơn hàng</th>`;
  headerTemplate += `<th >Doanh thu</th>`;
  headerTemplate += `<th >Chi phí</th>`;
  headerTemplate += `<th >Lợi nhuận</th>`;

  headerTemplate += "</thead>";
  var bodyTemplate = `<tbody>`;

  results.forEach((result, index) => {
    bodyTemplate += `<tr class="parent" data-index="${index}">`;
    bodyTemplate += `<td ><span>${
      result.customer_name == null ? "" : result.customer_name
    }</span></td> `;
    bodyTemplate += `<td class="text-right"><span>${formatNumber(
      result.order_number == null ? 0 : result.order_number
    )}</span></td> `;
    bodyTemplate += `<td class="text-right"><span>${formatNumber(
      result.revenue == null ? 0 : result.revenue
    )}</span></td> `;
    bodyTemplate += `<td class="text-right"><span>${formatNumber(
      result.cost == null ? 0 : result.cost
    )}</span></td> `;
    bodyTemplate += `<td class="text-right"><span>${formatNumber(
      result.profit == null ? 0 : result.profit
    )}</span></td> `;

    bodyTemplate += `</tr>`;
  });
  if (summary) {
    var summaryTemplate = "<tfoot><tr>";
    summaryTemplate +=
      `<td class="text-right">` +
      formatNumber(summary["total_customer"] || 0) +
      `</td>`;
    summaryTemplate +=
      `<td class="text-right">` +
      formatNumber(summary["total_order"] || 0) +
      `</td>`;
    summaryTemplate +=
      `<td class="text-right">` +
      formatNumber(summary["total_revenue"] || 0) +
      `</td>`;
    summaryTemplate +=
      `<td class="text-right">` +
      formatNumber(summary["total_cost"] || 0) +
      `</td>`;
    summaryTemplate +=
      `<td class="text-right">` +
      formatNumber(summary["total_profit"] || 0) +
      `</td>`;
    summaryTemplate += "</tr></tfoot>";

    bodyTemplate += summaryTemplate;
  }
  bodyTemplate += "</tbody>";

  table += headerTemplate + bodyTemplate;
  table += "</table>";
  return table;
}

function generateHeader(results, config, n) {
  var headerTemplate = `<row>`;
  headerTemplate += n.generateCellString("STT");
  headerTemplate += n.generateCellString("Khách hàng");
  headerTemplate += n.generateCellString("Số lượng đơn hàng");
  headerTemplate += n.generateCellString("Doanh thu");
  headerTemplate += n.generateCellString("Chi phí");
  headerTemplate += n.generateCellString("Lợi nhuận");
  headerTemplate += "</row>";
  return headerTemplate;
}

function generateRows(results, config, n) {
  var object = config.object,
    summary = config.summary;
  var bodyTemplate = ``;
  results.forEach((result, index) => {
    var childTemplate = "<row>";
    childTemplate += n.generateCellNumber(index + 1);
    childTemplate += n.generateCellString(
      result.customer_name == null ? "" : result.customer_name
    );
    childTemplate += n.generateCellNumber(
      result.order_number == null ? 0 : result.order_number
    );
    childTemplate += n.generateCellNumber(
      result.revenue == null ? 0 : result.revenue
    );
    childTemplate += n.generateCellNumber(
      result.cost == null ? 0 : result.cost
    );
    childTemplate += n.generateCellNumber(
      result.profit == null ? 0 : result.profit
    );
    childTemplate += "</row>";
    bodyTemplate += childTemplate;
  });
  if (summary) {
    var summaryTemplate = "<row>";
    summaryTemplate += n.generateCellString("");
    summaryTemplate += n.generateCellNumber(
      summary.total_customer == null ? 0 : summary.total_customer
    );
    summaryTemplate += n.generateCellNumber(
      summary.total_order == null ? 0 : summary.total_order
    );
    summaryTemplate += n.generateCellNumber(
      summary.total_revenue == null ? 0 : summary.total_revenue
    );
    summaryTemplate += n.generateCellNumber(
      summary.total_cost == null ? 0 : summary.total_cost
    );
    summaryTemplate += n.generateCellNumber(
      summary.total_profit == null ? 0 : summary.total_profit
    );
    summaryTemplate += "</row>";

    bodyTemplate += summaryTemplate;
  }
  return bodyTemplate;
}
