// Hàm tạo đối tượng tìm kiếm Khách hàng nhanh
// CreatedBy nlhoang 08/04/2020
function createRouteQuickSearch() {
  var quickSearch = function (searchConfig) {
    var dataTable,
      selectData,
      selectedData = [];
    var config = searchConfig || {};
    var entity = "route";
    var searchElement = config.searchElement || entity + "-search",
      comboElement = config.comboElement || "select-" + entity,
      buttonElement = config.buttonElement || "btn-" + entity,
      modalElement = config.modalElement || entity + "_modal",
      tableElement = config.tableElement || "table_" + entity;
    config.exceptIds = config.exceptIds || [];
    config.url = config.url || searchRouteUrl;
    var $searchElement = "." + searchElement;
    if (config.searchType == "element") {
      $searchElement = "#" + searchElement;
    }
    var loadSearchData = function () {
      if (config.url) {
        $("body").on("click", $searchElement, function () {
          var self = $(this);
          selectData = self;

          $("#" + modalElement).modal();

          var type = self.attr("data-type");
          if (type == null) {
            type = "single";
          }

          var all = self.attr("data-all");
          var url = config.url;

          var data = function (d) {
            d.all = all;
            d.exceptIds = config.exceptIds.join(",");
            d.vehicle_id = $("#vehicle_id").val();
            d.driver_id =
              $("#driver_id").length > 0
                ? $("#driver_id").val()
                : $("#primary_driver_id").val();
          };

          var vehicles = self
            .closest(".select2-bootstrap-prepend")
            .find("." + comboElement)
            .val();
          selectedData = vehicles
            ? selectedData.filter((p) => vehicles.indexOf(p.id) > -1)
            : [];

          if (!$.fn.dataTable.isDataTable(dataTable)) {
            dataTable = $("#" + tableElement).DataTable({
              serverSide: true,
              processing: true,
              responsive: true,
              language: {
                search: "Tìm kiếm theo biển số xe, tài xế hoặc tên chuyến:",
                searchPlaceholder: "Vui lòng nhập để tìm kiếm",
                lengthMenu: "Hiển thị _MENU_ bản ghi",
                info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
                infoEmpty: "Không có dữ liệu",
                loadingRecords: "Đang tải...",
                zeroRecords: "Không có dữ liệu",
                emptyTable: "Không có dữ liệu trong bảng",
                sInfoFiltered: "(được lọc từ _MAX_ mục)",
                select: {
                  rows: {
                    _: "Đã chọn %d dòng",
                    0: "",
                    1: "Đã chọn 1 dòng",
                  },
                },
                paginate: {
                  first: "Đầu",
                  previous: "Trước",
                  next: "Tiếp",
                  last: "Cuối",
                },
                processing:
                  '<div class="loader"><img src="' +
                  publicUrl +
                  '/css/backend/img/loader.gif"></div>',
              },
              ajax: {
                url: url,
                data: data,
              },
              select: type,
              columns: [
                { data: "id" },
                { data: "route_code", searchable: false },
                { data: "name", searchable: false },
                { data: "reg_no", searchable: false, orderable: false },
                { data: "ETD_date", searchable: false },
                { data: "ETA_date", searchable: false },
                { data: "loading_ratio", searchable: false, orderable: false },
              ],
              columnDefs: [
                {
                  targets: 0,
                  checkboxes: {
                    selectRow: true,
                    selectCallback: function (nodes, selected) {
                      var selectedItem = nodes.table().data()[
                        nodes[0]._DT_CellIndex.row
                      ];
                      if (selected) {
                        selectedData.push(selectedItem);
                      } else {
                        selectedData = selectedData.filter(
                          (p) => p.id !== selectedItem.id
                        );
                      }
                    },
                    selectAllCallback: function (
                      nodes,
                      selected,
                      indeterminate
                    ) {
                      if (indeterminate) return;
                      if (selected) {
                        var items = $("#" + tableElement)
                          .DataTable()
                          .rows({ selected: true })
                          .data();
                        $.each(items, function (index, item) {
                          selectedData.push(item);
                        });
                      } else {
                        var items = $("#" + tableElement)
                          .DataTable()
                          .data();
                        $.each(items, function (index, item) {
                          selectedData = selectedData.filter(
                            (p) => p.id !== item.id
                          );
                        });
                      }
                    },
                  },
                },
                {
                  targets: 3,
                  render: function (data, type, row) {
                    return `${row["reg_no"] || ""}<br/>${
                      row["driver_name"] || ""
                    }`;
                  },
                },
                {
                  targets: [4],
                  render: function (data, type, row) {
                    return data == null
                      ? ""
                      : '<div class="text-center">' +
                          moment(
                            row["ETD_date"] + " " + row["ETD_time"]
                          ).format("DD-MM-YYYY HH:mm") +
                          "</div>";
                  },
                },
                {
                  targets: [5],
                  render: function (data, type, row) {
                    return data == null
                      ? ""
                      : '<div class="text-center">' +
                          moment(
                            row["ETA_date"] + " " + row["ETA_time"]
                          ).format("DD-MM-YYYY HH:mm") +
                          "</div>";
                  },
                },
                {
                  targets: [6],
                  render: function (data, type, row) {
                    var weight = row["capacity_weight_ratio"] ? (Math.round(row["capacity_weight_ratio"] * 100) / 100).toFixed(2) : 0;
                    var volume = row["capacity_volume_ratio"] ? (Math.round(row["capacity_volume_ratio"] * 100) / 100).toFixed(2) : 0;

                    $html = "<div>";
                    $html += `<label for="file">Tải trọng (${weight}%) </label>`;
                    $html += `<progress max=100 value=${weight} class="${
                      weight > 100 ? "overload" : ""
                    }">${weight}</progress><br/>`;
                    $html += `<label for="file">Thể tích (${volume}%) </label>`;
                    $html += `<progress max=100 value=${volume} class="${
                      volume > 100 ? "overload" : ""
                    }">${volume}</progress>`;
                    $html += "</div>";
                    return $html;
                  },
                },
              ],
              select: type,
              pagingType: "full_numbers",
              order: [[ 5, "desc" ]],
              createdRow: function (row, data, index) {
                if (
                  $.inArray(
                    data["id"],
                    self
                      .closest(".select2-bootstrap-prepend")
                      .find("." + comboElement)
                      .val()
                  ) > -1
                ) {
                  dataTable.rows(index).select();
                }
              },
            });
          } else {
            dataTable.columns().checkboxes.deselectAll();
            dataTable.ajax.reload();
          }
        });
      }
    };

    var clickSearchButton = function () {
      var $this = $(this);
      $("#" + buttonElement).on("click", function (e) {
        e.preventDefault();
        var el = $(this);
        el.prop("disabled", true);
        setTimeout(function () {
          el.prop("disabled", false);
        }, 1500);

        var datas = [];

        var rows_selected = selectedData;
        $.each(rows_selected, function (index, item) {
          datas.push({ id: item.id, title: item.name });
        });
        if (typeof config.searchCallback !== "undefined") {
          config.searchCallback(selectData, datas);
        } else {
          searchCallback(selectData, datas);
        }
        $("#" + modalElement).modal("hide");
      });
    };

    var searchCallback = function (self, datas) {
      var $selectedVehicle = self.parent().find("." + comboElement);
      datas.forEach(function (item) {
        $selectedVehicle.select2("trigger", "select", {
          data: { id: item.id, title: item.title },
        });
      });
      config.exceptIds = config.exceptIds.concat(datas.map((p) => p.id));
      self = null;
    };

    var changeDataCombo = function () {
      var $combo = $($searchElement)
        .parent()
        .find("." + comboElement);
      if ($combo) {
        $combo.on("change", function (e) {
          config.exceptIds = $(this)
            .select2("data")
            .map((p) => p.id);
        });
      }
    };

    var _init = function () {
      loadSearchData();
      clickSearchButton();
      changeDataCombo();
    };

    return {
      init: _init,
    };
  };
  return quickSearch;
}
