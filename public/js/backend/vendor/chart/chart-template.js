// hàm thực hiện format 100000 => 100,000
function formatNumbercoma(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function formatNumberdot(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

var serializeObj = function (obj) {
  var str = [];
  for (var i in obj) {
    if (obj.hasOwnProperty(i)) {
      str.push(
        encodeURIComponent(i).replace("item.", "") +
          "=" +
          encodeURIComponent(obj[i])
      );
    }
  }
  return str.join("&");
};

class ChartTemplate {
  constructor(ctx, data) {
    (this.ctx = ctx), (this.data = data);
  }

  //Biểu đồ tròn
  pieChart(formatType, config) {
    var pieChart = new Chart(this.ctx, {
      type: "pie",
      data: this.data,
      options: {
        legend: {
          display: true,
          position: "right",
          labels: {
            fontColor: "black",
            generateLabels: function (chart) {
              var data = chart.data;
              if (data.labels.length && data.datasets.length) {
                return data.labels.map(function (label, i) {
                  var meta = chart.getDatasetMeta(0);
                  var ds = data.datasets[0];
                  var arc = meta.data[i];
                  var custom = (arc && arc.custom) || {};
                  var getValueAtIndexOrDefault =
                    Chart.helpers.getValueAtIndexOrDefault;
                  var arcOpts = chart.options.elements.arc;
                  var fill = custom.backgroundColor
                    ? custom.backgroundColor
                    : getValueAtIndexOrDefault(
                        ds.backgroundColor,
                        i,
                        arcOpts.backgroundColor
                      );
                  var stroke = custom.borderColor
                    ? custom.borderColor
                    : getValueAtIndexOrDefault(
                        ds.borderColor,
                        i,
                        arcOpts.borderColor
                      );
                  var bw = custom.borderWidth
                    ? custom.borderWidth
                    : getValueAtIndexOrDefault(
                        ds.borderWidth,
                        i,
                        arcOpts.borderWidth
                      );

                  // We get the value of the current label
                  var value =
                    chart.config.data.datasets[arc._datasetIndex].data[
                      arc._index
                    ];
                  return {
                    text: label + " : " + formatNumberdot(value),
                    fillStyle: fill,
                    strokeStyle: stroke,
                    lineWidth: bw,
                    hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                    index: i,
                  };
                });
              } else {
                return [];
              }
            },
          },
        },
        maintainAspectRatio: false,
        tooltips: {
          mode: "index",
          intersect: true,
          callbacks: {
            title: function (tooltipItem, data) {
              return data["labels"][tooltipItem[0]["index"]];
            },
            label: function (tooltipItem, data) {
              return formatNumber(
                data["datasets"][0]["data"][tooltipItem["index"]]
              );
            },
          },
        },
        hover: {
          mode: "index",
          intersect: true,
        },
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              switch (formatType) {
                case "percent":
                  let sum = 0;
                  let dataArr = ctx.chart.data.datasets[0].data;
                  dataArr.map((data) => {
                    sum += data;
                  });
                  value = ((value * 100) / sum).toFixed(2) + "%";
                  break;
                default:
                  break;
              }
              return value.replace(".", ",");
            },
            anchor: "end",
            align: "start",
            color: "black",
          },
        },
        responsive: true,
        onClick: function (event, array) {
          var actionPoints = pieChart.getElementsAtEvent(event);
          if (actionPoints.length > 0) {
            var nameCol = actionPoints[0]._model.label;
            var idChart = chartID;
            var c = $("[chartid=" + idChart + "]")
              .find("form")
              .serializeArray();

            var data = objectifyForm(c);
            data["item.ColumnName"] = nameCol;
            data["item.ChartID"] = chartID;
            var query = serializeObj(data);
            window.open("/Customer/IndexChartDetail?" + query, "_blank");
          }
        },
      },
    });
    return pieChart;
  }

  //Biểu đồ cột
  barChart(formatType, tooltipFormatType, config) {
    tooltipFormatType = tooltipFormatType || formatType; //ptly: Nếu không nhập loại định dạng của tooltip thì mặc định giống định dạng của label
    var barChart = new Chart(this.ctx, {
      type: "bar",
      data: this.data,
      options: {
        legend: {
          display: false,
        },
        maintainAspectRatio: false,
        tooltips: {
          mode: "label",
          intersect: false,
          callbacks: {
            label: function (tooltipItems, data) {
              var arr = [
                data.datasets[0].label[0] +
                  ": " +
                  formatNumber(
                    data.datasets[0].tooltip[0][tooltipItems.index]
                  ).toString(),
              ];
              switch (tooltipFormatType) {
                case "percent":
                  arr = [
                    data.datasets[0].label[0] +
                      ": " +
                      formatNumber(
                        data.datasets[0].tooltip[0][tooltipItems.index]
                      )
                        .toString()
                        .replace(".", ","),
                    data.datasets[0].label[1] +
                      ": " +
                      formatNumber(
                        data.datasets[0].tooltip[1][tooltipItems.index]
                      )
                        .toString()
                        .replace(".", ","),
                  ];
                  break;
                default:
                  break;
              }
              return arr;
            },
          },
        },
        layout: {
          padding: {
            top: 20,
          },
        },
        scales: {
          xAxes: [
            {
              barPercentage: 0.5,
              allowOverlap: true,
              ticks: {
                autoSkip: false,
                fontColor: "black",
              },
            },
          ],
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
                autoSkip: false,
                fontColor: "black",
                callback: function (label, index, labels) {
                  let number = formatNumberdot(parseInt(label));
                  switch (formatType) {
                    case "percent":
                      number =
                        parseFloat(Math.round(label * 100) / 100)
                          .toFixed(1)
                          .toString()
                          .replace(".", ",") + "%";
                      break;
                    default:
                      break;
                  }
                  return number;
                },
              },
            },
          ],
        },
        responsive: true,
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              let string = value !== 0 ? formatNumber(value) : "0";
              switch (formatType) {
                case "percent":
                  string =
                    value !== 0
                      ? value.toString().replace(".", ",") + "%"
                      : "0,00 %";
                  break;
                default:
                  break;
              }
              return string;
            },
            anchor: "end",
            align: "end",
            color: "black",
          },
        },
      },
    });
    return barChart;
  }

  //Biểu đồ cột
  horizontalBarChart(formatType, tooltipFormatType, config) {
    tooltipFormatType = tooltipFormatType || formatType;
    var barChart = new Chart(this.ctx, {
      type: "horizontalBar",
      data: this.data,
      options: {
        legend: {
          display: false,
        },
        maintainAspectRatio: false,
        tooltips: {
          mode: "label",
          intersect: false,
          callbacks: {
            title: function (tooltipItem, data) {
              return data["labels"][tooltipItem[0]["index"]];
            },
            label: function (tooltipItems, data) {
              var arr = [
                data.datasets[0].label[0] +
                  ": " +
                  formatNumber(
                    data.datasets[0].tooltip[0][tooltipItems.index]
                  ).toString(),
              ];
              switch (tooltipFormatType) {
                case "percent":
                  arr = [
                    data.datasets[0].label[0] +
                      ": " +
                      formatNumber(
                        data.datasets[0].tooltip[0][tooltipItems.index]
                      )
                        .toString()
                        .replace(".", ","),
                    data.datasets[0].label[1] +
                      ": " +
                      formatNumber(
                        data.datasets[0].tooltip[1][tooltipItems.index]
                      )
                        .toString()
                        .replace(".", ","),
                  ];
                  break;
                default:
                  break;
              }
              return arr;
            },
          },
        },
        layout: {
          padding: {
            top: 20,
          },
        },
        scales: {
          xAxes: [
            {
              barPercentage: 0.5,
              allowOverlap: true,
              ticks: {
                stacked: true,
                fontColor: "black",
                autoSkip: false,
                maxRotation: config.maxRotation || 0,
                minRotation: config.minRotation || 0,
                suggestedMin: 0,
                suggestedMax: 50,
                callback: function (label, index, labels) {
                  let number = formatNumberdot(parseInt(label));
                  switch (formatType) {
                    case "percent":
                      number =
                        parseFloat(Math.round(label * 100) / 100)
                          .toFixed(1)
                          .toString()
                          .replace(".", ",") + "%";
                      break;
                    default:
                      break;
                  }
                  return number;
                },
              },
            },
          ],
          yAxes: [
            {
              ticks: {
                stacked: true,
                beginAtZero: true,
                autoSkip: false,
                fontColor: "black",
                callback: function (label, index, labels) {
                  if (typeof label == "string") {
                    var arr = label.split("|||");
                    if (arr.length > 1) {
                      var [code, name] = arr;
                      return (
                        code +
                        " (" +
                        name
                          .split(/\s/)
                          .reduce(
                            (response, word) => (response += word.slice(0, 1)),
                            ""
                          )
                          .toUpperCase() +
                        ")"
                      );
                    }
                    return label
                      .split(/\s/)
                      .reduce(
                        (response, word) => (response += word.slice(0, 1)),
                        ""
                      )
                      .toUpperCase();
                  }
                  return label;
                },
              },
            },
          ],
        },
        responsive: true,
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              let string = value !== 0 ? formatNumber(value) : "0";
              switch (formatType) {
                case "percent":
                  string =
                    value !== 0
                      ? value.toString().replace(".", ",") + "%"
                      : "0,00 %";
                  break;
                default:
                  break;
              }
              return string;
            },
            anchor: "end",
            align: "end",
            color: "black",
          },
          beforeInit: function (chart) {
            chart.data.labels.forEach(function (value, index, array) {
              var a = [];
              a.push(value.slice(0, 5));
              var i = 1;
              while (value.length > i * 5) {
                a.push(value.slice(i * 5, (i + 1) * 5));
                i++;
              }
              array[index] = a;
            });
          },
        },
        // animation: {
        //     onComplete: function () {
        //         var chartInstance = this.chart;
        //         var ctx = chartInstance.ctx;
        //         // ctx.textAlign = "left";
        //         // ctx.font = "9px Open Sans";
        //         // ctx.fillStyle = "#fff";
        //
        //         Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
        //             var meta = chartInstance.controller.getDatasetMeta(i);
        //             Chart.helpers.each(meta.data.forEach(function (bar, index) {
        //                 data = dataset.data[index];
        //                 if (i == 0) {
        //                     ctx.fillText(data, 50, bar._model.y + 4);
        //                 } else {
        //                     ctx.fillText(data, bar._model.x - 25, bar._model.y + 4);
        //                 }
        //             }), this)
        //         }), this);
        //     }
        // }
      },
    });
    return barChart;
  }

  //Biểu đồ gồm nhiều cột
  barsChart(formatType, stack, config) {
    var barsChart = new Chart(this.ctx, {
      type: "bar",
      data: this.data,
      options: {
        legend: {
          display: true,
          labels: {
            fontColor: "black",
          },
        },
        maintainAspectRatio: false,
        tooltips: {
          mode: "label",
          intersect: false,
          callbacks: {
            label: function (tooltipItem, data) {
              return (
                data.datasets[tooltipItem.datasetIndex].label +
                ": " +
                formatNumber(tooltipItem.yLabel)
              );
            },
          },
        },
        scales: {
          xAxes: [
            {
              barPercentage: 0.55,
              allowOverlap: true,
              stacked: stack,
              ticks: {
                beginAtZero: true,
                fontColor: "black",
                autoSkip: false,
              },
            },
          ],
          yAxes: [
            {
              stacked: stack,
              ticks: {
                beginAtZero: true,
                fontColor: "black",
                callback: function (label, index, labels) {
                  return formatNumber(parseInt(label));
                },
              },
            },
          ],
        },
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              let string = "";
              switch (formatType) {
                case "custom":
                  string = formatNumber(value);
                  break;
                default:
                  break;
              }
              return string;
            },
            anchor: "center",
            align: "center",
            color: "black",
          },
        },
        responsive: true,
      },
    });
    return barsChart;
  }

  //Biểu đồ dòng
  lineChart(formatType, tooltipFormatType, config) {
    var yAxes = typeof config.yAxes != 'undefined' ? config.yAxes :  [
      {
        ticks: {
          beginAtZero: true,
          fontColor: "black",
          callback: function (label, index, labels) {
            let string = formatNumberdot(parseInt(label));
            switch (formatType) {
              case "percent":
                string =
                  parseFloat(Math.round(label * 100) / 100)
                    .toFixed(1)
                    .toString()
                    .replace(".", ",") + "%";
                break;
              default:
                break;
            }
            return string;
          },
        },
      }
    ];
    return new Chart(this.ctx, {
      type: "line",
      data: this.data,
      options: {
        legend: {
          display: config.displayLegend || false,
          labels: {
            fontColor: "black",
          },
        },
        maintainAspectRatio: false,
        tooltips: {
          mode: "label",
          intersect: false,
          callbacks: {
            label: function (tooltipItem, data) {
              let toolTip =
                data.datasets[tooltipItem.datasetIndex].label +
                ": " +
                formatNumber(tooltipItem.yLabel);
              switch (formatType) {
                case "percent":
                  toolTip =
                    data.datasets[tooltipItem.datasetIndex].label +
                    ": " +
                    tooltipItem.yLabel.toString().replace(".", ",") +
                    " %";
                  break;
                default:
                  break;
              }
              return toolTip;
            },
          },
        },
        elements: {
          line: {
            tension: 0,
            fill: false,
          },
        },
        scales: {
          xAxes: [
            {
              ticks: {
                fontColor: "black",
                autoSkip: false,
              },
            },
          ],
          yAxes: yAxes
        },
        responsive: true,
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              let string =
                value !== 0
                  ? formatNumber(value).toLocaleString(/* ... */)
                  : "0";
              switch (formatType) {
                case "percent":
                  string =
                    value !== 0
                      ? value
                          .toString()
                          .replace(".", ",")
                          .toLocaleString(/* ... */) + "%"
                      : "0.00 %";
                  break;
                default:
                  break;
              }
              return string;
            },
            anchor: "end",
            align: "end",
            color: "black",
            display:
              typeof config.display == "undefined" ? true : config.display,
          },
        },
        onClick: function (event, array) {},
      },
    });
  }
}
