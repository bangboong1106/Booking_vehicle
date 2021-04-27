let reportData;
let start;
let end;
let status_enum = {
  status_all: "Tất cả",
  status_complete: "Đơn hoàn thành",
  status_future: "Đơn dự kiến",
  status_incomplete: "Đơn chưa hoàn thành",
  status_late: "Đơn giao muộn",
  status_on_time: "Đơn giao đúng giờ",
  status_cancel: "Đơn hàng đã hủy",
};
let stt_enum = {
  status_all: 0,
  status_incomplete: 1,
  status_complete: 2,
  status_on_time: 4,
  status_late: 5,
  status_future: 3,
  status_cancel: 6,
};
let object_enum = {
  vehicle_team: "Đội tài xế",
  vehicle: "Số xe",
  driver: "Tài xế",
  customer: "Khách hàng",
};
let me = this;

$(function () {
  if (typeof cboSelect2 !== "undefined") {
    if (typeof comboDriverUri !== "undefined") {
      cboSelect2.driver(comboDriverUri);
    }
    if (typeof comboVehicleUri !== "undefined") {
      cboSelect2.vehicle(comboVehicleUri);
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
  if (typeof createVehicleQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createVehicleQuickSearch();
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

  $(".display-type input[type=radio]").on("change", function () {
    if ($(this).get(0).checked) {
      if ($(this).val() == 1) {
        $(".lbl-wrap-status").css("display", "block");
        $(".wrap-status").css("display", "block");
        $(".wrap-status input[type=checkbox]").prop("checked", false);
      } else {
        $(".lbl-wrap-status").css("display", "none");
        $(".wrap-status").css("display", "none");
        $(".wrap-status input[type=checkbox]").prop("checked", false);
      }
      if ($(this).val() != 1) {
        $("#wrap-day-condition").css("display", "block");
      } else {
        $("#wrap-day-condition").css("display", "none");
      }
    }
  });

  $("#wrapper-status input[type=checkbox]").on("change", function (e) {
    e.preventDefault();
    if ($(this).get(0).checked) {
      $(this).parent("label").addClass("active");
    } else {
      $(this).parent("label").removeClass("active");
    }
    if ($("#wrapper-status input[type=checkbox]:checked").length === 0) {
      $(this).prop("checked", "checked");
      $(this).parent("label").addClass("active");
    }
  });

  $("#statistic").on("change", function (e) {
    e.preventDefault();
    $("#reportrange span").html(
      moment().startOf("month").locale("vi").format("D MMMM, YYYY") +
        " - " +
        moment().endOf("month").locale("vi").format("D MMMM, YYYY")
    );
    if ($(this).val() == "day") {
      options.dateLimit.days = 31;
    } else {
      options.dateLimit.months = 36;
    }
    $("#reportrange").daterangepicker(options, function (start, end, label) {
      $("#reportrange span").html(
        start.locale("vi").format("D MMMM, YYYY") +
          " - " +
          end.locale("vi").format("D MMMM, YYYY")
      );
    });
  });

  $("#btnApply").on("click", function () {
    var data = createRequestData();

    sendRequest(
      {
        url: reportUri,
        type: "POST",
        data: data.data,
      },
      function (response) {
        if (!response) return;
        var entityType = "";
        switch (data.EntityType) {
          case 1:
            entityType = object_enum.vehicle_team;
            break;
          case 2:
            entityType = object_enum.vehicle;
            break;
          case 3:
            entityType = object_enum.driver;
            break;
          case 4:
            entityType = object_enum.customer;
            break;
        }
        var results = generateReportTable(response.data);
        if (results.length != 0) {
          Tool.serverData = results;
          var template = generateReportTemplate(
            results,
            entityType,
            data.clientData,
            response.summary
          );
        }

        var start = $("#reportrange")
          .data("daterangepicker")
          .startDate.format("DD-MM-YYYY");
        var end = $("#reportrange")
          .data("daterangepicker")
          .endDate.format("DD-MM-YYYY");

        var displayType = "";
        switch (Number(data.data.DisplayType)) {
          case 1:
            displayType = "hoạt động theo đơn hàng";
            break;
          case 2:
            displayType = "hoạt động theo doanh thu";
            break;
          case 3:
            displayType = "hoạt động theo chi phí";
            break;
          case 4:
            displayType = "hoạt động theo lợi nhuận";
            break;
        }

        $(".row.title span.content").html(
          `Báo cáo ${entityType.toLowerCase()} ${displayType}`
        );
        $(".row.title span.parameter").html(` (từ ${start} đến ${end})`);
        $(".report-content").html(template);
        $(".card-box.result").css("display", "block");
        if (results.length != 0) {
          $(".empty-box").addClass("hide");
          $(".report-content").removeClass("hide");
        } else {
          $(".empty-box").removeClass("hide");
          $(".report-content").addClass("hide");
        }
      }
    );
  });

  $("#btnDefault").on("click", function () {
    $("#vehicle_team_ids").empty().trigger("change");
    $("#driver_ids").empty().trigger("change");
    $("#vehicle_ids").empty().trigger("change");
    $("#customer_ids").empty().trigger("change");
    $("#wrapper-status")
      .find("input[type=checkbox]")
      .prop("checked", "checked");
    $("#wrapper-status label").addClass("active");
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

function beforeGenerateFile(data) {
  var entityType = "";
  switch (data.EntityType) {
    case 1:
      entityType = object_enum.vehicle_team;
      break;
    case 2:
      entityType = object_enum.vehicle;
      break;
    case 3:
      entityType = object_enum.driver;
      break;
    case 4:
      entityType = object_enum.customer;
      break;
  }
  return entityType;
}

function createRequestData() {
  var data = {};
  data.EntityType = $(".entity").find("a.active").data("entity"); //Hiển thị loại báo cáo: 1 đội xe, 2 xe, 3 tài xế, 4 khách hàng

  data.VehicleTeamIDs = $("#vehicle_team_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.DriverIDs = $("#driver_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  data.VehicleIDs = $("#vehicle_ids")
    .select2("data")
    .map((p) => p.id)
    .join(",");

  if ($('#customer_ids').length > 0) {
    data.CustomerIDs = $("#customer_ids")
      .select2("data")
      .map((p) => p.id)
      .join(",");
  }

  if ($('#partner_id').length > 0) {
    data.PartnerIds = $("#partner_id")
    .select2("data")
    .map((p) => p.id)
    .join(",");
  }

  data.DisplayType = $(".display-type").find("input[type=radio]:checked").val(); //Hiển thị theo lượt

  var reportType = "";
  var tmp = [];
  var statusList = $("#wrapper-status").find("input[type=checkbox]:checked");
  if (statusList) {
    var temp = [];
    statusList.each((index, item) => {
      var st = $(item).data("status");
      temp.push(st);
      tmp.push(st);
      if (st == stt_enum.status_complete) {
        tmp.push(stt_enum.status_late, stt_enum.status_on_time);
      }
    });
    reportType = temp.join(",");
  }
  data.ReportType = reportType; //Hiển thị trạng thái đơn

  data.FromDate = $("#reportrange")
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  data.ToDate = $("#reportrange")
    .data("daterangepicker")
    .endDate.format("YYYY-MM-DD");
  data.StatisticBy = $("#statistic").val();
  data.DayCondition = $("#dayCondition").val();
  var clientData = { ...data };
  clientData.ReportType = tmp.join(",");
  return {
    data: data,
    clientData: clientData,
  };
}

// Tạo dữ liệu báo cáo
// CreatedBy nlhoang 23/05/2019
function generateReportTable(response) {
  var results = [];
  if (!response) return [];
  response.forEach((item) => {
    if (results.filter((p) => p.id == item.entity_id).length > 0) {
      var result = results.find((p) => p.id === item.entity_id);
      if (!result.hasOwnProperty([item.date])) {
        result[item.date] = {};
        var itemDate = result[item.date];
        for (var key in stt_enum) {
          itemDate[key] = item[key] == null ? 0 : item[key];
        }
      }
    } else {
      var result = {};
      result.id = item.entity_id;
      result.key = item.entity_name;
      result[item.date] = {};
      var itemDate = result[item.date];
      for (var key in stt_enum) {
        itemDate[key] = item[key] == null ? 0 : item[key];
      }

      results.push(result);
    }
  });
  console.log(results);
  return results;
}

// Tạo mẫu báo cáo
// CreatedBy nlhoang 23/05/2019
function generateReportTemplate(results, object, data, summary) {
  var reportTypes =
    data.ReportType == ""
      ? []
      : data.ReportType.split(",").map((t) => Number(t));

  var table = '<table class="table table-bordered">';
  var headerTemplate = `<thead>`;
  var header = results[0];
  for (var key in header) {
    if (key !== "key" && key !== "id") {
      headerTemplate += `<th style = "width: 120px" > ${key} </th>`;
    } else if (key === "key") {
      headerTemplate += `<th style="width: 200px">${object}</th>`;
      // headerTemplate += '<th style="width: 120px">Tổng</th>';
    }
  }
  headerTemplate += "</thead>";
  var bodyTemplate = `<tbody>`;

  results.forEach((result, index) => {
    var obj = {
      status_all: [],
      status_complete: [],
      status_future: [],
      status_incomplete: [],
      status_late: [],
      status_on_time: [],
      status_cancel: [],
    };
    for (var date in result) {
      if (date != "key" && date != "id") {
        for (var keys in result[date]) {
          obj[keys].push(result[date][keys]);
        }
      }
    }

    bodyTemplate += `<tr class="parent" data-index="${index}">`;
    if (data.DisplayType == 1 && reportTypes.length != 1) {
      bodyTemplate += `<td class="accordion"><span>${result.key}</span></td> `;
    } else {
      bodyTemplate += `<td ><span>${result.key}</span></td> `;
    }
    var sums = 0;

    if (reportTypes.length == 0) {
      var tmp = "status_all";
      if (data.DisplayType != 1) {
        tmp = "status_complete";
      }
      var childTemplate = "";
      for (var current_date of obj[tmp]) {
        var sum = 0;
        sums += current_date;
        childTemplate += `<td class="text-right">${formatNumber(
          current_date
        )}</td>`;
      }
      bodyTemplate += `<!--<td class="text-right">${formatNumber(
        sums
      )}</td>-->`;
      bodyTemplate += childTemplate;
      bodyTemplate += `</tr>`;
    } else if (
      reportTypes.length > 1 &&
      reportTypes.indexOf(stt_enum["status_all"]) > -1
    ) {
      var childTemplate = "";
      for (var current_date of obj["status_all"]) {
        var sum = 0;
        sums += current_date;
        childTemplate += `<td class="text-right">${formatNumber(
          current_date
        )}</td>`;
      }
      bodyTemplate += `<!--<td class="text-right">${formatNumber(
        sums
      )}</td>-->`;
      bodyTemplate += childTemplate;
      bodyTemplate += `</tr>`;
    } else if (reportTypes.length == 1) {
      var stt = "status_all";
      var childTemplate = "";
      for (var current_date of obj[stt]) {
        childTemplate += `<td class="text-right">${formatNumber(
          current_date
        )}</td>`;
      }
      // bodyTemplate += `<td class="text-right">${formatNumber(sums)}</td>`;
      bodyTemplate += childTemplate;
      bodyTemplate += `</tr>`;
    } else {
      for (var current_date of obj["status_all"]) {
        bodyTemplate += `<td class="text-right">${formatNumber(
          current_date
        )}</td>`;
      }
      // bodyTemplate += `<td class="text-right">/</td>`;
      bodyTemplate += `</tr>`;
    }

    if (reportTypes.length == 0 || reportTypes.length > 1) {
      var tempStatuses = [];

      var checkedStatus = [stt_enum.status_complete];
      if (
        reportTypes.length === checkedStatus.length &&
        reportTypes.sort().every(function (value, index) {
          return value === checkedStatus.sort()[index];
        })
      ) {
        tempStatuses = ["status_on_time", "status_late"];
      } else {
        tempStatuses = [
          "status_all",
          "status_incomplete",
          "status_complete",
          "status_on_time",
          "status_late",
          "status_future",
          "status_cancel",
        ];
      }

      for (var status of tempStatuses) {
        if (status === "status_all") continue;
        if (
          data.DisplayType == 1 &&
          (reportTypes.length == 0 ||
            reportTypes.indexOf(stt_enum[status]) >= 0)
        ) {
          bodyTemplate += `<tr class="child" data-index="${index}">`;
          if (status === "status_on_time" || status === "status_late") {
            bodyTemplate += `<td style="background-color: white">&nbsp;&nbsp;&nbsp;<span style="color:#757575">${status_enum[status]}</span></td> `;
          } else {
            bodyTemplate += `<td style="background-color: white"><span>${status_enum[status]}</span></td> `;
          }

          var statusSum = 0;
          var childTemplate = "";
          for (var val of obj[status]) {
            childTemplate += `<td class="text-right">${formatNumber(val)}</td>`;
          }
          bodyTemplate += childTemplate;
          bodyTemplate += `</tr>`;
        }
      }
    }
  });
  if (summary) {
    var summaryTemplate = "<tfoot><tr>";

    var childSummaryTemplate = "";
    var summarySum = 0;
    for (var index in summary) {
      childSummaryTemplate += `<td class="text-right"><b>${
        summary[index].total == null ? 0 : formatNumber(summary[index].total)
      }</b></td>`;
    }
    summaryTemplate += `<td></td>`;
    summaryTemplate += childSummaryTemplate;
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
  var header = results[0];
  for (var key in header) {
    if (key !== "key" && key !== "id") {
      headerTemplate += n.generateCellString(key);
    } else if (key === "key") {
      headerTemplate += n.generateCellString(config.object);
    }
  }
  headerTemplate += "</row>";
  return headerTemplate;
}

function generateRows(results, config, n) {
  var object = config.object,
    data = config.data,
    summary = config.summary;
  var reportTypes =
    data.ReportType == ""
      ? []
      : data.ReportType.split(",").map((t) => Number(t));
  var bodyTemplate = ``;
  results.forEach((result, index) => {
    var obj = {
      status_all: [],
      status_complete: [],
      status_future: [],
      status_incomplete: [],
      status_late: [],
      status_on_time: [],
      status_cancel: [],
    };
    for (var date in result) {
      if (date != "key" && date != "id") {
        for (var keys in result[date]) {
          obj[keys].push(result[date][keys]);
        }
      }
    }
    bodyTemplate += `<row>`;
    bodyTemplate += n.generateCellString(result.key);
    var sums = 0;
    if (reportTypes.length == 0) {
      var tmp = "status_all";
      if (data.DisplayType != 1) {
        tmp = "status_complete";
      }
      var childTemplate = "";
      for (var current_date of obj[tmp]) {
        var sum = 0;
        sums += current_date;
        childTemplate += n.generateCellNumber(current_date);
      }
      // bodyTemplate += n.generateCellNumber(sums);
      bodyTemplate += childTemplate;
      bodyTemplate += `</row>`;
    } else if (
      reportTypes.length > 1 &&
      reportTypes.indexOf(stt_enum["status_all"]) > -1
    ) {
      var childTemplate = "";
      for (var current_date of obj["status_all"]) {
        var sum = 0;
        sums += current_date;
        childTemplate += n.generateCellNumber(current_date);
      }
      // bodyTemplate += n.generateCellNumber(sums);
      bodyTemplate += childTemplate;
      bodyTemplate += `</row>`;
    } else if (reportTypes.length == 1) {
      var stt = "status_all";
      var childTemplate = "";
      for (var current_date of obj["status_all"]) {
        var sum = 0;
        sums += current_date;
        childTemplate += n.generateCellNumber(current_date);
      }
      // bodyTemplate += n.generateCellNumber(sums);
      bodyTemplate += childTemplate;
      bodyTemplate += `</row>`;
    } else {
      for (var current_date of obj["status_all"]) {
        bodyTemplate += n.generateCellString(current_date);
      }
      bodyTemplate += `</row>`;
    }

    if (reportTypes.length == 0 || reportTypes.length > 1) {
      var tempStatuses = [];

      var checkedStatus = [stt_enum.status_complete];
      if (
        reportTypes.length === checkedStatus.length &&
        reportTypes.sort().every(function (value, index) {
          return value === checkedStatus.sort()[index];
        })
      ) {
        tempStatuses = ["status_on_time", "status_late"];
      } else {
        tempStatuses = [
          "status_all",
          "status_incomplete",
          "status_complete",
          "status_on_time",
          "status_late",
          "status_future",
          "status_cancel",
        ];
      }
      for (var status in tempStatuses) {
        if (status === "status_all") continue;
        if (
          data.DisplayType == 1 &&
          (reportTypes.length == 0 ||
            reportTypes.indexOf(stt_enum[status]) >= 0)
        ) {
          bodyTemplate += `<row>`;
          if (status === "status_on_time" || status === "status_late") {
            bodyTemplate += n.generateCellString(`${status_enum[status]}`);
          } else {
            bodyTemplate +=
              "  " + n.generateCellString(`${status_enum[status]}`);
          }
          bodyTemplate += n.generateCellString(`${status_enum[status]}`);
          var statusSum = 0;
          var childTemplate = "";
          for (var val of obj[status]) {
            statusSum += val;
            childTemplate += n.generateCellNumber(val);
          }
          bodyTemplate += childTemplate;
          bodyTemplate += `</row>`;
        }
      }
    }
  });
  if (summary) {
    var summaryTemplate = "<row>";

    var childSummaryTemplate = "";
    var summarySum = 0;
    for (var index in summary) {
      summarySum += summary[index].total == null ? 0 : summary[index].total;
      childSummaryTemplate += n.generateCellNumber(
        summary[index].total == null ? 0 : summary[index].total
      );
    }
    summaryTemplate += n.generateCellString("");
    summaryTemplate += childSummaryTemplate;
    summaryTemplate += "</row>";

    bodyTemplate += summaryTemplate;
  }
  return bodyTemplate;
}
