var $excel = $("#spreadsheet");
var $jexcel = null;
var precedenceData = {
  3: "Đặc biệt",
  4: "Bình thường",
  5: "Thấp",
};
var statusData = {
  1: "Khởi tạo",
  2: "Sẵn sàng",
  7: "Chờ tài xế xác nhận",
  3: "Chờ nhận hàng",
  4: "Đang vận chuyển",
  5: "Hoàn thành",
  6: "Huỷ",
};
var yesNoData = {
  1: "Có",
  0: "Không",
};
var commission_typeData = {
  2: "Số tiền",
  1: "Phần trăm",
};
var status_collected_documentsData = {
  1: "Chưa thu đủ",
  2: "Đã thu đủ",
};
var payment_typeData = {
  1: "Chuyển khoản",
  2: "Tiền mặt",
};
var vehicleData = "",
  driverData = "",
  locationData = "",
  customerData = "";

var columns = [];
var timeColumn = {
  // Methods
  closeEditor: function (cell, save) {
    var value = "";
    if (cell.children[0]) {
      value = cell.children[0].value;
      cell.innerHTML = value;
    } else {
      value = cell.innerHTML;
    }
    return value;
  },
  openEditor: function (cell) {
    // Create input
    var element = document.createElement("input");
    element.value = cell.innerHTML;
    // Update cell
    cell.classList.add("editor");
    cell.innerHTML = "";
    cell.appendChild(element);
    $(element).clockpicker({
      donetext: "Xong",
      afterDone: function (value) {
        cell.innerHTML = value;
      },
      afterHide: function () {
        setTimeout(function () {
          $jexcel.closeEditor(cell, true);
        });
      },
    });
    // Focus on the element
    element.focus();
  },
  getValue: function (cell) {
    return cell.innerHTML;
  },
  setValue: function (cell, value) {
    cell.innerHTML = value;
  },
};

var dateTimeOptions = {
  months: [
    "T1",
    "T2",
    "T3",
    "T4",
    "T5",
    "T6",
    "T7",
    "T8",
    "T9",
    "T10",
    "T11",
    "T12",
  ],
  weekdays: ["Chủ nhật", "Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7"],
  weekdays_short: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
  format: "DD/MM/YYYY",
};

$(function () {
  registerExcel();

  registerImport();

  registerExport();
});

function getColumns() {
  return columns;
}

function registerExcel() {
  var requests = [];
  var window = this;
  for (let url in spreadSheetConfig.url.entity) {
    requests.push(
      $.ajax({
        url: spreadSheetConfig.url.entity[url],
      })
    );
  }

  $.when.apply(undefined, requests).done(function () {
    var i = 0;
    for (let url in spreadSheetConfig.url.entity) {
      window[url + "Data"] = arguments[i][0];
      i++;
    }
    requestColumns();
  });
}

function requestColumns() {
  sendRequest(
    {
      url: spreadSheetConfig.url.columns,
      type: "GET",
    },
    function (response) {
      var data = response.excel_column_mapping_configs;
      data.forEach((item) => {
        item.title = item.column_name;
        item.width = item.width * 10;
        item.readOnly = item.is_import == 0;
        switch (item.data_type) {
          case "list":
            item.type = "dropdown";
            if (item.entity) {
              item.field = item.original_field || item.field;
              item.source = this[item.entity + "Data"]
                ? JSON.parse(this[item.entity + "Data"])
                : [];
              // item.url = spreadSheetConfig.url.entity[item.entity];
              item.autocomplete = true;
            } else {
              if (item.function == "convertYesNo") {
                item.source = Object.values(yesNoData);
              } else {
                item.source = this[item.field + "Data"]
                  ? Object.values(this[item.field + "Data"])
                  : null;
              }
            }
            break;
          case "number":
            item.type = "numeric";
            // item.mask = "#.###.00";
            // item.decimal = ",";
            // item.mask ='#.##,00';
            //  decimal:','
            break;
          case "date":
            item.type = "calendar";
            item.options = dateTimeOptions;
            break;
          case "time":
            item.type = "text";
            // item.editor = timeColumn;
            break;
          default:
            item.type = "text";
            break;
        }
      });
      columns = data;
      initSheet();
    }
  );
}
function initSheet() {
  var data = [];
  var temps = spreadSheetConfig.data;
  var listColumns = getColumns();
  var count = temps.length;

  if (count > 0) {
    for (var i = 0; i < count; i++) {
      if (i < 50) {
        var item = [];
        listColumns.forEach(function (column, index) {
          var val = temps[i][column.field];
          if (column.field == "precedence") {
            item.push(precedenceData[val]);
          } else if (column.field == "status") {
            val = Number(val);
            item.push(statusData[val]);
          } else {
            if (column.type == "numeric") {
              item.push(formatNumber(val));
            } else {
              item.push(val);
            }
          }
        });
        data.push(item);
      }
    }
  } else {
    for (var i = 0; i < 50; i++) {
      var item = [];
      listColumns.forEach(function (column, index) {
        if (column.field == "order_date") {
          item.push(moment().format("YYYY-MM-DD"));
        } else {
          item.push("");
        }
      });
      data.push(item);
    }
  }

  var nestedHeaders = [];
  listColumns.forEach(function (column, index) {
    if (column.header_group) {
      var filter = nestedHeaders.find((p) => {
        return p.title == column.header_group;
      });
      if (filter) {
        filter.colspan = filter.colspan + 1;
      } else {
        nestedHeaders.push({
          title: column.header_group,
          colspan: 1,
        });
      }
    } else {
      nestedHeaders.push({
        title: "",
        colspan: 1,
      });
    }
  });

  var changed = function (instance, cell, x, y, value) {
    var row = Number(y) + 1;
    var field = listColumns[x].field;
    switch (field) {
      case "customer_id":
        var url = spreadSheetConfig.url.customerDetail.replace("-1", value);
        sendRequestNotLoading(
          {
            url: url,
            type: "GET",
          },
          function (response) {
            var idx = listColumns.findIndex((v) => v.field == "customer_name");
            if (idx != -1) {
              $excel.jexcel("setValueFromCoords", idx, y, response.full_name);
            }
            idx = listColumns.findIndex((v) => v.field == "customer_mobile_no");
            if (idx != -1) {
              $excel.jexcel("setValueFromCoords", idx, y, response.mobile_no);
            }
          }
        );
        break;
      case "vehicle_id":
        var url = spreadSheetConfig.url.driverDefault + "?vehicle_id=" + value;
        sendRequestNotLoading(
          {
            url: url,
            type: "GET",
          },
          function (response) {
            var idx = listColumns.findIndex(
              (v) => v.field == "primary_driver_id"
            );
            if (idx != -1) {
              var driver = response.data;
              $excel.jexcel("setValueFromCoords", idx, y, driver.driver.id);
            }
          }
        );
        break;
    }
  };

  var loaded = function (instance) {
    hideLoading();
  };

  showLoading();

  $jexcel = jexcel(document.getElementById("spreadsheet"), {
    data: data,
    tableOverflow: true,
    rowResize: true,
    columnDrag: true,
    tableWidth: "calc(100vw - 24px)",
    tableHeight: "85vh",
    freezeColumns: 1,
    lazyLoading: true,
    loadingSpin: true,
    contextMenu: function () {
      return false;
    },
    columns: listColumns,
    onchange: changed,
    onload: loaded,
    csvFileName: "DanhSachDonHang",
    allowComments: true,
    updateTable: function (instance, cell, col, row, val, label, cellName) {
      var direction = "";
      var type = listColumns[col].type;
      switch (type) {
        case "numeric":
          direction = "right";
          break;
        case "checkbox":
        case "calendar":
          direction = "center";
          break;
        default:
          direction = "left";
          break;
      }
      $(cell).css("text-align", direction);
      if (row % 2) {
        $(cell).css("background-color", "#edf3ff");
      }
    },
    nestedHeaders: nestedHeaders,
  });
}

var listCoordination = [];

function registerImport() {
  $("#btn-import").on("click", function () {
    var items = $excel.jexcel("getData", false);
    var result = [];
    var listColumns = getColumns();
    items.forEach(function (item) {
      var temp = {};
      listColumns.forEach(function (column, index) {
        if (column.type == "numeric") {
          temp[column.field] = item[index]
            .replace(/\./g, "")
            .replace(/\,/g, ".");
        } else {
          temp[column.field] = item[index];
        }
      });

      result.push(temp);
    });
    result = result.filter(function (item) {
      return item.order_code != "";
    });
    listCoordination.forEach((element) => {
      $jexcel.setStyle(element, "background-color", "none");
      $jexcel.setComments(element, "");
    });
    toastr.options = {
      positionClass: "toast-top-center",
    };
    sendRequest(
      {
        url: spreadSheetConfig.url.import,
        data: {
          update: spreadSheetConfig.data.length > 0,
          data: result,
        },
        type: "POST",
      },
      function (response) {
        if (response.errorCode == 0) {
          toastr["success"]("Cập nhật danh sách đơn hàng thành công");
        } else {
          toastr["error"](
            "Có lỗi xảy ra khi cập nhật đơn hàng.<br/>Vui lòng kiểm tra lại dữ liệu ở các dòng " +
              Object.keys(response.data)
          );

          listCoordination = [];
          for (var rowIndex in response.data) {
            var row = response.data[rowIndex];
            for (var prop in row) {
              var index = listColumns.findIndex((v) => v.field == prop);
              if (index == -1) continue;
              var columnName = jexcel.getColumnName(index);
              var coordination = columnName + rowIndex;
              listCoordination.push(coordination);
              $jexcel.setStyle(coordination, "background-color", "red");
              $jexcel.setComments(coordination, row[prop]);
            }
          }
        }
      }
    );
  });
}

function registerExport() {
  $("#btn-export").on("click", function () {
    $excel.jexcel("download", true);
  });
}
