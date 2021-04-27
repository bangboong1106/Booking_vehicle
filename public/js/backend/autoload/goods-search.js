// Hàm tạo đối tượng tìm kiếm Đội Xe nhanh
// CreatedBy nlhoang 08/04/2020
function createGoodsQuickSearch() {
  var quickSearch = function (searchConfig) {
    var dataTable,
      selectData,
      selectedData = [];
    var config = searchConfig || {};
    var entity = "goods";
    var searchElement = config.searchElement || entity + "-search",
      comboElement = config.comboElement || "select-" + entity,
      buttonElement = config.buttonElement || "btn-" + entity,
      modalElement = config.modalElement || entity + "_modal",
      tableElement = config.tableElement || "table_" + entity;
    config.exceptIds = config.exceptIds || [];
    config.url = config.url || searchGoodsUrl;
    customerId = 0;

    var loadSearchData = function () {
      if (config.url) {
        var $searchElement = "." + searchElement;
        if (config.searchType == "element") {
          $searchElement = "#" + searchElement;
        }

        $("#goods_group_id").change(function () {
          if ($(this).val() != "") {
            dataTable.search("").draw();
          }
        });
        $("#goods_owner_id").change(function () {
          if ($(this).val() != "") {
            dataTable.search("").draw();
          }
        });

        if ($('#customer_id_hidden').length > 0 && $("#customer_id_hidden").val()) {
          customerId = $("#customer_id_hidden").val();
        }
        
        $("body").on("click", $searchElement, function () {
          var self = $(this);
          selectData = self;

          $("#" + modalElement).modal();

          var type = self.attr("data-type");
          if (type == null) {
            type = "single";
          }

          var url = config.url;

          var data = function (d) {
            d.exceptIds = config.exceptIds.join(",");
            d.goodsGroupId = $("#goods_group_id").val();
            d.customerId = customerId > 0 ? customerId : $("#goods_owner_id").val();
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
                search: "Tìm kiếm :",
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
                { data: "code", searchable: false },
                { data: "title", searchable: false },
                { data: "name_of_goods_group_id", searchable: false },
                { data: "volume", searchable: false },
                { data: "weight", searchable: false },
                { data: "goods_unit", searchable: false },
                { data: "note", searchable: false },
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
                  targets: [4, 5],
                  render: function (data, type, row) {
                    return data == null
                      ? ""
                      : '<div class="text-right">' +
                          formatNumber(data) +
                          "</div>";
                  },
                },
              ],
              select: type,
              pagingType: "full_numbers",
              order: [[0, "asc"]],
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
        var datas = [];

        var rows_selected = selectedData;
        $.each(rows_selected, function (index, item) {
          if (datas.findIndex(x => x.id === item.id) == -1) {
            datas.push({
              id: item.id,
              title: item.code,
              code: item.code,
              goods_type_id: item.id,
              goods_unit_id: item.goods_unit_id,
              goods_type: item.title,
              goods_unit: item.goods_unit,
              title: item.title,
              volume: item.volume,
              weight: item.weight,
              total_volume: item.volume,
              total_weight: item.weight,
            });
          }
        });
        if (typeof config.searchCallback !== "undefined") {
          config.searchCallback(selectData, datas);
        } else {
          searchCallback(selectData, datas);
        }
        $("#" + modalElement).modal("hide");
      });
    };

    var searchCallback = function (self, vehicles) {
      var $selectedVehicle = self.parent().find("." + comboElement);
      // selectedVehicle.empty().trigger("change");
      vehicles.forEach(function (item) {
        $selectedVehicle.select2("trigger", "select", {
          data: { id: item.id, title: item.title },
        });
      });
      config.exceptIds = config.exceptIds.concat(vehicles.map((p) => p.id));
      self = null;
    };

    var _init = function () {
      loadSearchData();
      clickSearchButton();
    };

    return {
      init: _init,
    };
  };
  return quickSearch;
}
