// Hàm tạo đối tượng tìm kiếm Xe nhanh
// CreatedBy nlhoang 08/04/2020
function createVehicleQuickSearch() {
    var vehicleQuickSearch = (function (searchConfig) {
        var vehicleTable,
            selectVehicle,
            selectedVehicles = [];
        var config = searchConfig || {};

        var searchElement = config.searchElement || 'vehicle-search',
            comboElement = config.comboElement || 'select-vehicle',
            buttonElement = config.buttonElement || 'btn-vehicle',
            modalElement = config.modalElement || 'vehicle_modal',
            tableElement = config.tableElement || 'table_vehicles';
        config.exceptIds = config.exceptIds || [];
        config.url = config.url || searchVehicleUrl;
        var $searchElement = '.' + searchElement;
        if (config.searchType == 'element') {
            $searchElement = '#' + searchElement;
        }
        var loadSearchData = function () {
            if (config.url) {
                $('body').on('click', $searchElement, function () {
                    var self = $(this);
                    selectVehicle = self;

                    $('#' + modalElement).modal();

                    var type = self.attr('data-type');
                    if (type == null) {
                        type = 'single';
                    }

                    var all = self.attr('data-all');
                    var url = config.url;

                    var isDistance = self.attr('data-distance');

                    var data = function (d) {
                        d.all = all;
                        d.isDistance = isDistance;
                        d.locationType = $('#hdfLocationType').val();
                        d.destinationLocationId = $('#hdfDestinationLocationId').val();
                        d.arrivalLocationId = $('#hdfArrivalLocationId').val();
                        d.exceptIds = config.exceptIds.join(',');
                    };

                    var vehicles = self.closest(".select2-bootstrap-prepend").find("." + comboElement).val();
                    selectedVehicles = vehicles ? selectedVehicles.filter(p => vehicles.indexOf(p.id) > -1) : [];

                    if (!$.fn.dataTable.isDataTable(vehicleTable)) {
                        vehicleTable = $('#' + tableElement).DataTable({
                            serverSide: true,
                            processing: true,
                            responsive: true,
                            dom: '<"toolbar">frtip',
                            pageLength: 10,
                            initComplete: function () {
                                var content = '';
                                if (typeof isDisplayDistance !== 'undefined' && isDisplayDistance == true) {
                                    content = '<span>Sắp xếp theo <select id="hdfLocationType" class="select2"><option value="1" selected>Điểm nhận hàng</option><option value="2">Điểm trả hàng</option></select></span>';
                                }
                                $("div.toolbar").html(content);
                            },
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
                                {data: 'reg_no', searchable: false},
                                {data: 'volume', searchable: false},
                                {data: 'weight', searchable: false},
                                {data: 'reg_no', searchable: false},
                                {
                                    data: 'distance_in_km',
                                    'searchable': false
                                }
                            ],
                            "columnDefs": [
                                {
                                    'targets': 0,
                                    'checkboxes': {
                                        'selectRow': true,
                                        'selectCallback': function (nodes, selected) {
                                            var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
                                            if (selected) {
                                                selectedVehicles.push(selectedItem);
                                            } else {
                                                selectedVehicles = selectedVehicles.filter(p => p.id !== selectedItem.id);
                                            }
                                        },
                                        'selectAllCallback': function (nodes, selected, indeterminate) {
                                            if (indeterminate) return;
                                            if (selected) {
                                                var items = $('#' + tableElement).DataTable().rows({selected: true}).data();
                                                $.each(items, function (index, item) {
                                                    selectedVehicles.push(item);
                                                });
                                            } else {
                                                var items = $('#' + tableElement).DataTable().data();
                                                $.each(items, function (index, item) {
                                                    selectedVehicles = selectedVehicles.filter(p => p.id !== item.id);
                                                });
                                            }
                                        }
                                    },

                                },
                                {
                                    "targets": 1,
                                    "render": function (data, type, row) {
                                        return data == null ? '' : '<b>' + data + '</b>';
                                    }
                                },
                                {
                                    "targets": 4,
                                    "data": 'reg_no',
                                    "render": function (data, type, row) {
                                        return '<div class="text-right">' + (row['length'] == null ? "0" : formatNumber(row['length'])) + '*' +
                                            (row['width'] == null ? "0" : formatNumber(row['width'])) + '*' +
                                            (row['height'] == null ? "0" : formatNumber(row['height'])) + '</div>';
                                    }
                                },
                                {
                                    "targets": [2, 3],
                                    "render": function (data, type, row) {
                                        return data == null ? '' : '<div class="text-right">' + formatNumber(data) + '</div>';
                                    }
                                },
                                {
                                    "targets": [5],
                                    "render": function (data, type, row) {
                                        data = data == null ? 0 : data;
                                        var roundData = Math.round(data * 10) / 10;
                                        var color = roundData < 10 ? 'red' : 'gray';
                                        if (roundData == data) {
                                            return '<div class="text-right"><b><span style="color: ' + color + '">' + formatNumber(roundData) + ' (km)</span></b></div>';
                                        }
                                        return roundData == null ? '' :
                                            '<div class="text-right"><b><span style="color: ' + color + '">~' + formatNumber(roundData) + ' (km)<span></b></div>';
                                    },
                                    "visible": typeof isDisplayDistance !== 'undefined' && isDisplayDistance == true ? true : false
                                }
                            ],
                            select: type,
                            pagingType: "full_numbers",
                            'order': [[0, 'asc']],
                            "createdRow": function (row, data, index) {
                                if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find("." + comboElement).val()) > -1) {
                                    vehicleTable.rows(index).select();
                                }
                            },
                        });
                    } else {
                        vehicleTable.columns().checkboxes.deselectAll();
                        vehicleTable.ajax.reload();
                    }
                });


            }
        };

        var clickSearchButton = function () {
            var $this = $(this);
            $('#' + buttonElement).on('click', function (e) {
                e.preventDefault();
                var vehicles = [];

                var rows_selected = selectedVehicles;
                $.each(rows_selected, function (index, item) {
                    vehicles.push({id: item.id, title: item.reg_no});
                });
                if (typeof config.searchCallback !== 'undefined') {
                    config.searchCallback(selectVehicle, vehicles);
                } else {
                    searchCallback(selectVehicle, vehicles);

                }
                $('#' + modalElement).modal('hide');
            });
        }

        var searchCallback = function (self, vehicles) {
            var $selectedVehicle = self.parent().find('.' + comboElement);
            // selectedVehicle.empty().trigger("change");
            vehicles.forEach(function (item) {
                $selectedVehicle.select2("trigger", "select", {
                    data: {id: item.id, title: item.title}
                });
            });
            config.exceptIds = config.exceptIds.concat(vehicles.map(p => p.id));
            self = null;

        }

        var loadDataByDistance = function () {
            $(document).on('change', '#hdfLocationType', function () {
                $('#' + tableElement).DataTable().ajax.reload();
            });
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
            loadDataByDistance();
            clickSearchButton();
            changeDataCombo();
        };

        return {
            init: _init
        };
    });
    return vehicleQuickSearch;
}
