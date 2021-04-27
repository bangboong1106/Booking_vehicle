let realTimes = {
    orderStatistic: false,
    revenueStatistic: false,
    customerStatistic: false,
  };
  let charts = [
    {
      name: "orderStatistic",
      isToday: true,
      isCustomerHeader: true,
      isRealTime: true,
      customRender: loadOrderStatistic,
    },
    {
      name: "revenueStatistic",
      isToday: true,
      isCustomerHeader: true,
      isRealTime: true,
      customRender: loadRevenueStatistic,
    },
    {
      name: "customerStatistic",
      isToday: true,
      isCustomerHeader: true,
      isRealTime: true,
      customRender: loadCustomerStatistic,
    },
    {
      name: "documentStatistic",
      isCustomerHeader: true,
      customParam: {
        type: null,
      },
      customRender: loadDocumentStatistic,
    },
    {
      name: "turnByTime",
      chartType: "lineChart",
      label: "Đơn hàng",
      color: "#3cba9f",
      type: 3,
      config: {},
    },
    {
      name: "incomeByCustomer",
      chartType: "horizontalBarChart",
      config: {
        minRotation: 30,
        maxRotation: 30,
      },
      label: "Doanh thu",
      color: "#fd7e14",
      isGenerateTable: true,
      type: 2,
    },
    {
      name: "turnByCustomer",
      chartType: "horizontalBarChart",
      config: {
        minRotation: 0,
        maxRotation: 0,
      },
      label: "Đơn hàng",
      color: "#3e95cd",
      isGenerateTable: true,
      type: 4,
    },
    {
      name: "incomeByTime",
      chartType: "lineChart",
      label: ["Doanh thu", "Chi phí", "Lợi nhuận"],
      color: ["#8e5ea2", "#fd7e14", "#3cba9f"],
      type: 1,
      config: {
        display: false,
        displayLegend: true,
      },
      isMultiple: true,
      datasets: ["income", "cost", "profit"],
    },
    {
      name: "goodsByTime",
      chartType: "lineChart",
      label: ["Tải trọng", "Dung tích"],
      color: ["#3cba9f", "#fd7e14"],
      type: 5,
      config: {
        display: false,
        displayLegend: true,
        yAxes: [
          {
            display: true,
            scaleLabel: {
              display: true,
              labelString: "Tải trọng",
            },
            position: "left",
            type: "linear",
            ticks: {
              beginAtZero: true,
              fontColor: "black",
            },
            id: "weight",
          },
          {
            display: true,
            scaleLabel: {
              display: true,
              labelString: "Dung tích",
            },
            position: "right",
            type: "linear",
            id: "volume",
          },
        ],
      },
      isMultiple: true,
      datasets: ["weight", "volume"],
      isMultipleYAxis: true,
    },
  ];
  let chartInfo = {};
  
  $(document).ready(function () {
    let config = {
      format: "DD/MM/YYYY",
      startDate: moment().startOf("month"),
      endDate: moment().endOf("month"),
      dateLimit: {
        days: 31,
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
        "Tháng này": [moment().startOf("month"), moment().endOf("month")],
        "Tháng trước": [
          moment().subtract(1, "month").startOf("month"),
          moment().subtract(1, "month").endOf("month"),
        ],
      },
      opens: "left",
      drops: "down",
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
  
    charts.map((p) => {
      var chartName = camelToSnakeCase(p.name);
      var dateRangeConfig = { ...config };
      if (p.isToday) {
        dateRangeConfig.startDate = moment();
        dateRangeConfig.endDate = moment();
      }
      $("#report-range-" + chartName + " span").html(
        dateRangeConfig.startDate.locale("vi").format("D MMMM, YYYY") +
          " - " +
          dateRangeConfig.endDate.locale("vi").format("D MMMM, YYYY")
      );
      $("#report-range-" + chartName).daterangepicker(dateRangeConfig, function (
        start,
        end,
        label
      ) {
        $("#report-range-" + chartName + " span").html(
          start.locale("vi").format("D MMMM, YYYY") +
            " - " +
            end.locale("vi").format("D MMMM, YYYY")
        );
      });
    });
  
    expandChartRegister();
  
    $(".modal-body.modal-parameter").each(function () {
      var cardHeader = $(this).parents(".card").find(".card-header");
      var dataType = $(this).parents(".card").find("form").attr("data-type");
      var key = Object.keys(Application.Parameters).filter(
        (key) => Application.Parameters[key].type == dataType
      );
      var data = Application.Parameters[key];
  
      chartParam.showInfo(data, cardHeader);
    });
  
    charts.map((chart) => {
      var chartName = camelToSnakeCase(chart.name);
      var chartType = chart.chartType;
      if (chartType) {
        chartInfo[chart.name] = new ChartTemplate(
          $("#canvas-chart-" + chartName),
          {}
        )[chartType]("custom", true, chart.config);
      }
      $("#btn-" + chartName + "," + "#refresh-" + chartName).on(
        "click",
        function () {
          var config = {
            chartType: chart.type,
            dayCondition: $("#report-day-condition-" + chartName).val(),
            generateTable: function (response, type) {
              if (chart.isGenerateTable) {
                return fillDataGrid(
                  response,
                  ["Tên khách hàng", chart.label],
                  "table-" + type
                );
              }
            },
          };
          getChartData(chart, config);
        }
      );
  
      $("#btn-" + chartName).trigger("click");
    });
  });
  
  function camelToSnakeCase(str) {
    return str.replace(/[A-Z]/g, (letter) => `-${letter.toLowerCase()}`);
  }
  
  function getChartData(chart, config) {
    var chartName = camelToSnakeCase(chart.name);
    var data = {};
    if (chart.customParam) {
      data = { type: $("#report-document-statistic").val() };
    } else {
      var fromDate = $("#report-range-" + chartName).data("daterangepicker")
        .startDate;
      var toDate = $("#report-range-" + chartName).data("daterangepicker")
        .endDate;
  
      data = {
        fromDate: fromDate,
        toDate: toDate,
        type: config.chartType,
        customerIds: customerIds,
      };
  
      if (chart.isRealTime) {
        if (!realTimes[chart.name]) {
          data.realTime = realTimes[chart.name];
          realTimes[chart.name] = !realTimes[chart.name];
        } else {
          data.realTime = realTimes[chart.name];
        }
      }
    }
    var customerIds = $("#filter-customer-" + chartName + "-ids").select2("data")
      ? $("#filter-customer-" + chartName + "-ids")
          .select2("data")
          .map((p) => p.id)
          .join(";")
      : "";
    data.customerIds = customerIds;
    if (config.dayCondition) {
      data.dayCondition = config.dayCondition;
    }
  
    var header = chart.isCustomerHeader
      ? $("." + chartName + "-param-info")
      : $("#" + chartName + "-modal")
          .parent(".card")
          .find(".card-header");
    chartParam.showInfo(data, header);
  
    if (!chart.customParam) {
      data.fromDate = fromDate.format("YYYY-MM-DD");
      data.toDate = toDate.format("YYYY-MM-DD");
    }
    ChartUtility.getData(
      Application.Urls[chart.name],
      data,
      function (response) {
        if (chart.customRender) {
          chart.customRender(response);
          return;
        }
        if (config.generateTable) {
          config.generateTable(response, chartName);
        }
        var isMultiple = chart.isMultiple || false;
        var isMultipleYAxis = chart.isMultipleYAxis || false;
  
        ChartObject.getData(
          chartInfo[chart.name],
          response,
          function (response, dataObject) {
            response.data.labels.forEach((item, index) => {
              dataObject.labels.push(item);
              dataObject.data.push(response.data.datasets.data[index]);
            });
            dataObject.extendData = { data: dataObject.data };
          },
          {
            datasets: chart.datasets,
            label: chart.label,
            color: chart.color,
          },
          isMultiple,
          isMultipleYAxis,
          false
        );
        ChartObject.showSum(response, "#" + chartName + "-label", chart);
      },
      null,
      "chart-" + chartName + "-loader"
    );
  }
  
  var ChartUtility = (function () {
    var _getData = function (
      url,
      data,
      action,
      modalElement,
      locationLoader,
      chart,
      beforesendcallback
    ) {
      var normalizeData = {};
      for (var key in data) {
        if (data[key] instanceof moment) {
          normalizeData[key] = data[key].format("YYYY-MM-DD");
        } else {
          normalizeData[key] = data[key];
        }
      }
      var options = {
        url: url,
        dataType: "json",
        data: normalizeData,
        type: "POST",
      };
      options.beforeSend = function () {
        if (typeof beforesendcallback === "function") {
          beforesendcallback();
        }
        if (typeof modalElement !== "undefined") {
          $("#" + modalElement).modal("hide");
        }
        if (typeof locationLoader !== "undefined" && modalElement !== "object") {
          $("#" + locationLoader)
            .show()
            .nextAll()
            .css("opacity", "0.5");
        }
      };
      options.complete = function () {
        if (typeof locationLoader !== "undefined" && modalElement !== "object") {
          $("#" + locationLoader)
            .hide()
            .nextAll()
            .css("opacity", "1");
        }
      };
      sendRequest(options, function (response) {
        action(response);
        if (typeof chart !== "undefined") {
          chart.update();
        }
      });
    };
  
    return {
      getData: _getData,
    };
  })();
  
  var ChartObject = Object.create({
    showSum: function () {
      var chart = arguments[0];
      var el = $(arguments[1]);
      var config = arguments[2];
      var isMultiple = config.isMultiple || false;
      if (isMultiple) {
        var total = config.label
          .map((p, index) => {
            var prop = config.datasets[index];
            var dataset = prop.charAt(0).toUpperCase() + prop.slice(1);
            var item =
              "Tổng " +
              p.toLowerCase() +
              " " +
              formatNumber(chart.data["total" + dataset]);
            return item;
          })
          .join(" - ");
        el.find("span").html(total);
      } else {
        el.find("span").html(formatNumber(chart.data.total));
      }
      el.show();
    },
    getData: function () {
      var chart = arguments[0],
        response = arguments[1],
        callback = arguments[2],
        option = arguments[3],
        isMultiple = arguments[4],
        isMultipleYAxis = arguments[5],
        showLabel = arguments[6];
  
      var dataObject = {
        labels: [],
        data: [],
        extendData: {},
        extendLabel: [],
      };
      callback(response, dataObject);
      var tooltip = [];
      if (Object.keys(dataObject.extendData).length > 0) {
        for (var prop in dataObject.extendData) {
          tooltip.push(dataObject.extendData[prop]);
        }
      }
      var dataChart = {
        labels: dataObject.labels,
        datasets: [],
      };
      if (!isMultiple) {
        var obj = {
          borderColor: option["color"] ? option["color"] : "#3e95cd",
          backgroundColor: option["color"] ? option["color"] : "#3e95cd",
          data: dataObject.data,
          tooltip: tooltip,
          label: option["label"] ? option["label"] : dataObject["extendLabel"][0],
        };
        dataChart.datasets.push(obj);
      } else {
        for (var i = 0; i < option.datasets.length; i++) {
          let label =
            dataObject["extendLabel"][i] == undefined
              ? option["label"][i]
              : dataObject["extendLabel"][i];
          var obj = {
            backgroundColor: option["color"][i],
            borderColor: option["color"][i],
            data: dataObject.data.map((p) => p[option.datasets[i]]),
            label: label,
            pointRadius: 0,
          };
          if (isMultipleYAxis) {
            obj.yAxisID = option["datasets"][i];
          }
          dataChart["datasets"].push(obj);
        }
      }
      chart.data = {};
      chart.data = dataChart;
      if (dataChart.datasets[0].data) {
        var max = Math.max.apply(null, dataChart.datasets[0].data);
        var step = 0;
        for (var z = 1; z <= 10; z++) {
          if (max >= Math.pow(10, z - 1) && max <= Math.pow(10, z)) {
            step = Math.pow(10, z - 1);
            if (max === Math.pow(10, z)) {
              max = (Math.ceil(max / step) + 1) * step;
            } else {
              max = (Math.ceil(max / step) + 1) * step;
            }
            break;
          }
        }
        if (chart.config.type === "horizontalBar") {
          chart.config.options.scales.xAxes[0].ticks.max = max;
        } else {
          chart.config.options.scales.yAxes[0].ticks.max = max;
        }
      }
      chart.update();
    },
  });
  
  var chartParam = Object.create({
    showInfo: function () {
      var data = arguments[0];
      var cardHeader = arguments[1];
      if (cardHeader.find(".parameter-detail").length > 0) {
        cardHeader.find(".parameter-detail").remove();
        cardHeader.append('<div class="parameter-detail row"></div>');
        var paramDetail = cardHeader.find(".parameter-detail");
        for (var key in data) {
          if (key != "type") {
            if (data[key] instanceof moment) {
              paramDetail.append(
                '<div><span class="param-label">' +
                  this.translate(key) +
                  " </span><span>" +
                  data[key].format("DD/MM") +
                  "</span></div>"
              );
            } else if (key == "dayCondition") {
              $dayTitle = "";
              switch (data[key]) {
                case "1":
                  $dayTitle = "Thời gian nhận hàng dự kiến";
                  break;
                case "2":
                  $dayTitle = "Thời gian nhận hàng thực tế";
                  break;
                case "3":
                  $dayTitle = "Thời gian trả hàng dự kiến";
                  break;
                case "4":
                  $dayTitle = "Thời gian trả hàng thực tế";
                  break;
              }
              paramDetail.append(
                '<div><span class="param-label">Loại thời gian </span><span>' +
                  $dayTitle +
                  "</span></div>"
              );
            } else {
              if (
                this.translate(key)
                  ? paramDetail.append(
                      '<div><span class="param-label">' +
                        this.translate(key) +
                        " </span><span>" +
                        data[key] +
                        "</span></div>"
                    )
                  : ""
              );
            }
          }
        }
      } else {
        var description = [];
        for (var key in data) {
          if (data[key] instanceof moment) {
            description.push(data[key].format("DD/MM"));
          }
        }
        if (description.every((val, i, arr) => val === arr[0])) {
          if (description[0] == moment().format("DD/MM")) {
            cardHeader.text(" trong ngày");
          } else {
            cardHeader.text(description[0] ? " ngày " + description[0] : "");
          }
        } else {
          cardHeader.text(description ? description.join("-") : "");
        }
      }
    },
    translate: function (text) {
      var obj = {
        fromDate: "Từ ngày",
        toDate: "Đến ngày",
      };
      return obj[text];
    },
  });
  
  function expandChartRegister() {
    var lstItem = $(".navigate-chart span");
    $.each(lstItem, function (index, item) {
      $(item).click(function () {
        var $target = $(this).closest(".card");
        var lsticon = $target.find(".navigate-chart img");
  
        var $canvas = $target.find("canvas");
        $target.toggleClass("full-screen-chart");
        $canvas.toggleClass("stretch-canvas");
        $(item).toggleClass("collapse-chart-container");
        // căn chỉnh cho table
        var $coltable = $target.find("div[name=table]");
        var $colcanvas = $target.find("div[name=canvas]");
        var $table = $coltable.children();
        $table.toggleClass("block-display");
        $coltable.toggleClass("col-3");
        $colcanvas.toggleClass("col-9");
        $colcanvas.hasClass("col-9")
          ? $colcanvas.removeClass("col-12")
          : $colcanvas.addClass("col-12");
        $.each(lsticon, function (index, item2) {
          $(item2).toggleClass("none-display");
        });
      });
    });
  }
  
  function loadOrderStatistic(response) {
    $(".order-statistic")
      .find(".info")
      .each(function () {
        var field = $(this).attr("name");
        var total = response.data[field].total;
  
        $(this).text(formatNumber(Math.round(total)));
        var percent = $(this).parents(".card-box").find(".general-percent");
        var extraPer = response.data[field].extraPer;
        var extraPerType = response.data[field].extraPerType;
        percent.text(formatNumber(Math.round(extraPer * 100) / 100) + "%");
  
        var fa = $(this).parents(".card-box").find(".fa");
        if (extraPerType >= 1) {
          fa.addClass("fa-arrow-up")
            .addClass("text-success")
            .removeClass("fa-arrow-down")
            .removeClass("text-danger");
          percent.addClass("text-success").removeClass("text-danger");
        } else {
          fa.removeClass("fa-arrow-up")
            .removeClass("text-success")
            .addClass("fa-arrow-down")
            .addClass("text-danger");
          percent.removeClass("text-success").addClass("text-danger");
        }
        var amount = $(this).parents(".card-box").find(".amount");
        amount.text(formatNumber(response.data[field].extra));
  
        switch (field) {
          case "order":
            if (response.data[field].status) {
              var lstItem = $("span[data-status]");
              $.each(lstItem, function (index, item) {
                $(item).text(0);
                var dataStatus = $(item).attr("data-status");
                var statusGroup = response.data[field].status.filter((p) => {
                  return p.status == dataStatus;
                });
                if (statusGroup.length > 0) {
                  $(item).text(statusGroup[0].total);
                }
              });
            }
            break;
        }
      });
  }
  
  function loadCustomerStatistic(response) {
    $(".customer-statistic")
      .find(".info")
      .each(function () {
        var field = $(this).attr("name");
        var total = response.data[field].total;
  
        $(this).text(formatNumber(Math.round(total)));
        var percent = $(this).parents(".card-box").find(".general-percent");
        var extraPer = response.data[field].extraPer;
        var extraPerType = response.data[field].extraPerType;
        percent.text(formatNumber(Math.round(extraPer * 100) / 100) + "%");
  
        var fa = $(this).parents(".card-box").find(".fa");
        if (extraPerType >= 1) {
          fa.addClass("fa-arrow-up")
            .addClass("text-success")
            .removeClass("fa-arrow-down")
            .removeClass("text-danger");
          percent.addClass("text-success").removeClass("text-danger");
        } else {
          fa.removeClass("fa-arrow-up")
            .removeClass("text-success")
            .addClass("fa-arrow-down")
            .addClass("text-danger");
          percent.removeClass("text-success").addClass("text-danger");
        }
        var amount = $(this).parents(".card-box").find(".amount");
        amount.text(formatNumber(response.data[field].extra));
  
        switch (field) {
          case "customer":
            if (response.data[field].labels.length > 0) {
              var customers = response.data[field].labels
                .slice(0, 5)
                .map(
                  (item) =>
                    `<div class="col item"><div class="row"><div class="col-12"><span class="badge badge-light">${item}</span></div></div></div><div class="w-100"></div>`
                )
                .join("");
              $(".customers-content").html("").append(customers);
              $(".customers .customer-title").css("display", "flex");
            } else {
              $(".customers .customer-title").css("display", "none");
            }
            break;
        }
      });
  }
  
  function loadRevenueStatistic(response) {
    $(".revenue-statistic")
      .find(".info")
      .each(function () {
        var field = $(this).attr("name");
        var total = response.data[field].total;
  
        $(this).text(formatNumber(Math.round(total)));
        var percent = $(this).parents(".card-box").find(".general-percent");
        var extraPer = response.data[field].extraPer;
        var extraPerType = response.data[field].extraPerType;
        percent.text(formatNumber(Math.round(extraPer * 100) / 100) + "%");
  
        var fa = $(this).parents(".card-box").find(".fa");
        if (extraPerType >= 1) {
          fa.addClass("fa-arrow-up")
            .addClass("text-success")
            .removeClass("fa-arrow-down")
            .removeClass("text-danger");
          percent.addClass("text-success").removeClass("text-danger");
        } else {
          fa.removeClass("fa-arrow-up")
            .removeClass("text-success")
            .addClass("fa-arrow-down")
            .addClass("text-danger");
          percent.removeClass("text-success").addClass("text-danger");
        }
        var amount = $(this).parents(".card-box").find(".amount");
        amount.text(formatNumber(response.data[field].extra));
  
        switch (field) {
          case "revenue":
            $(".revenues-content").html("");
            if (response.data[field].labels.length > 0) {
              var customers = response.data[field].labels
                .slice(0, 5)
                .map(
                  (item, index) => `<div class="col item"><div class="row">
                                          <div class="col-8">
                                          <span class="badge badge-light" title="${item}">${item}</span>
                                          </div>
                                          <div class="col-4 text-right">
                                          <span class=" item-count">${formatNumber(
                                            response.data[field].datasets.data[
                                              index
                                            ]
                                          )}</span>
                                          </div></div></div></div><div class="w-100"></div>`
                )
                .join("");
              $(".revenues-content").html("").append(customers);
              $(".revenues .revenues-title").css("display", "flex");
            } else {
              $(".revenues .revenues-title").css("display", "none");
            }
            break;
        }
      });
  }
  
  function loadDocumentStatistic(response) {
    var total = response.data.document.total;
    $(".document-statistic")
      .find(".info")
      .text(formatNumber(Math.round(total)));
    if (response.data.document) {
      var lstItem = $("span[data-document]");
      $.each(lstItem, function (index, item) {
        $(item).text(0);
        var documentType = $(item).attr("data-document");
        var totalDocument = response.data.document.status[documentType];
        if (totalDocument) {
          $(item).text(formatNumber(totalDocument));
        }
      });
    }
    var total_day_late = response.data.document.status.total_day_late;
    $("#total_day_late").text(formatNumber(Math.round(total_day_late)));
  }
  
  function fillDataGrid(response, params, tableName) {
    var result = "<tr>";
    var $targetTable = $("#" + tableName);
    $targetTable.empty();
    $.each(params, function (index, item) {
      result += "<th>" + item + "</th>";
    });
    result += "</tr>";
    var lengthHeader = params.length;
    $.each(response.data.labels, function (index, item) {
      result += "<tr>";
      result += "<td>" + item + "</td>";
      for (var i = 0; i < lengthHeader - 1; ++i) {
        result +=
          "<td style='text-align: right'>" +
          formatNumber(response.data.datasets.data[index]) +
          "</td>";
      }
      result += "</tr>";
    });
    $targetTable.html(result);
  }
  