// Hàm tạo đối tượng tìm kiếm Xe nhanh
// CreatedBy nlhoang 08/04/2020
function createDriverQuickSearch() {
    var quickSearch = (function (searchConfig) {
        var driverTable,
            selectDriver,
            selectedDrivers = [];
        var config = searchConfig || {};

        var searchElement = config.searchElement || 'driver-search',
            comboElement = config.comboElement || 'select-driver',
            buttonElement = config.buttonElement || 'btn-driver',
            modalElement = config.modalElement || 'driver_modal',
            tableElement = config.tableElement || 'table_drivers';
        config.exceptIds = config.exceptIds || [];
        config.url = config.url || searchDriverUrl;
        var $searchElement = '.' + searchElement;
        if (config.searchType == 'element') {
            $searchElement = '#' + searchElement;
        }
        var loadSearchData = function () {
            if (config.url) {
                $('body').on('click', $searchElement, function () {
                    var self = $(this);
                    selectDriver = self;

                    $('#' + modalElement).modal();

                    var type = self.attr('data-type');
                    if (type == null) {
                        type = 'single';
                    }

                    var all = self.attr('data-all');
                    var url = config.url;

                    var data = function (d) {
                        d.all = all;
                        d.exceptIds = config.exceptIds.join(',');
                        d.partnerId = config.partnerId
                    };

                    var drivers = self.closest(".select2-bootstrap-prepend").find("." + comboElement).val();
                    selectedDrivers = drivers ? selectedDrivers.filter(p => drivers.indexOf(p.id) > -1) : [];

                    if (!$.fn.dataTable.isDataTable(driverTable)) {
                        driverTable = $('#' + tableElement).DataTable({
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
                                        1: "Đã chọn 1 dòng"
                                    }
                                },
                                paginate: {
                                    first: "Đầu",
                                    previous: "Trước",
                                    next: "Tiếp",
                                    last: "Cuối"
                                },
                                processing: '<div class="loader"><img src="' + publicUrl + '/css/backend/img/loader.gif"></div>'
                            },
                            ajax: {
                                "url": url,
                                "data": data
                            },
                            select: type,
                            columns: [
                                {data: 'id'},
                                // {data: 'DT_Row_Index', name: 'DT_Row_Index'},
                                {data: 'code', searchable: false},
                                {data: 'full_name', searchable: false},
                                {data: 'mobile_no', searchable: false},
                                {data: 'driver_license', searchable: false}
                            ],
                            'columnDefs': [
                                {
                                    'targets': 0,
                                    'checkboxes': {
                                        'selectRow': true,
                                        'selectCallback': function (nodes, selected) {
                                            var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
                                            if (selected) {
                                                selectedDrivers.push(selectedItem);
                                            } else {
                                                selectedDrivers = selectedDrivers.filter(p => p.id !== selectedItem.id);
                                            }
                                        },
                                        'selectAllCallback': function (nodes, selected, indeterminate) {
                                            if (indeterminate) return;
                                            if (selected) {
                                                var items = $('#' + tableElement).DataTable().rows({selected: true}).data();
                                                $.each(items, function (index, item) {
                                                    selectedDrivers.push(item);
                                                });
                                            } else {
                                                var items = $('#' + tableElement).DataTable().data();
                                                $.each(items, function (index, item) {
                                                    selectedDrivers = selectedDrivers.filter(p => p.id !== item.id);
                                                });
                                            }
                                        }
                                    }
                                },
                            ],
                            'order': [[0, 'asc']],
                            "createdRow": function (row, data, index) {
                                if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find("." + comboElement).val()) > -1) {
                                    driverTable.rows(index).select();
                                }
                            },
                        });
                    } else {
                        driverTable.columns().checkboxes.deselectAll();
                        driverTable.ajax.reload();
                    }
                });


            }
        };

        var clickSearchButton = function () {
            var $this = $(this);
            $('#' + buttonElement).on('click', function (e) {
                e.preventDefault();
                var drivers = [];

                var rows_selected = selectedDrivers;
                $.each(rows_selected, function (index, item) {
                    drivers.push({id: item.id, title: item.full_name});
                });
                if (typeof config.searchCallback !== 'undefined') {
                    config.searchCallback(selectDriver, drivers);
                } else {
                    searchCallback(selectDriver, drivers);

                }
                $('#' + modalElement).modal('hide');
            });
        }

        var searchCallback = function (self, drivers) {
            var $selectedDriver = self.parent().find('.' + comboElement);
            // selectedDriver.empty().trigger("change");
            drivers.forEach(function (item) {
                $selectedDriver.select2("trigger", "select", {
                    data: {id: item.id, title: item.title}
                });
            });
            config.exceptIds = config.exceptIds.concat(drivers.map(p => p.id));
            self = null;
        }

        var changeDataCombo = function () {
            var $combo = $($searchElement).parent().find('.' + comboElement);
            if ($combo) {
                $combo.on('change', function (e) {
                    config.exceptIds = $(this).select2('data').map(p => p.id);
                });
            }
        }
        var _init = function () {
            loadSearchData();
            clickSearchButton();
            changeDataCombo();
        };

        return {
            init: _init
        };
    });
    return quickSearch;
}
