// Hàm tạo đối tượng tìm kiếm Đơn hàng nhanh
// CreatedBy nlhoang 09/04/2020
function createQuotaQuickSearch() {
    var quickSearch = (function (searchConfig) {
        var dataTable,
            selectData,
            selectedData = [];
        var config = searchConfig || {};
        var entity = 'quota';
        var searchElement = config.searchElement || entity + '-search',
            comboElement = config.comboElement || 'select-' + entity,
            buttonElement = config.buttonElement || 'btn-' + entity,
            modalElement = config.modalElement || entity + '_modal',
            tableElement = config.tableElement || 'table_' + entity;
        config.exceptIds = config.exceptIds || [];
        config.url = config.url || searchQuotaUrl;
        var $searchElement = '.' + searchElement;
        if (config.searchType == 'element') {
            $searchElement = '#' + searchElement;
        }
        var loadSearchData = function () {
            if (config.url) {

                $('body').on('click', $searchElement, function () {
                    var self = $(this);
                    selectData = self;

                    $('#' + modalElement).modal();

                    var type = self.attr('data-type');
                    if (type == null) {
                        type = 'single';
                    }

                    var all = self.attr('data-all');
                    var url = config.url;

                    var data = function (d) {
                        d.route_id = $('#route_id').val();
                        d.vehicle_id = $('#vehicle_id').val();
                        d.driver_id = $('#driver_id').length > 0 ? $('#driver_id').val() : $('#primary_driver_id').val();
                        d.exceptIds = config.exceptIds.join(',');
                    };

                    var vehicles = self.closest(".select2-bootstrap-prepend").find("." + comboElement).val();
                    selectedData = vehicles ? selectedData.filter(p => vehicles.indexOf(p.id) > -1) : [];

                    if (!$.fn.dataTable.isDataTable(dataTable)) {
                        dataTable = $('#' + tableElement).DataTable({
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
                                {data: 'quota_code', searchable: false},
                                {data: 'name', searchable: false},
                                {data: 'title', searchable: false},
                                {data: 'total_cost', searchable: false},
                            ],
                            "columnDefs": [
                                {
                                    'targets': 0,
                                    'checkboxes': {
                                        'selectRow': true,
                                        'selectCallback': function (nodes, selected) {
                                            var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
                                            if (selected) {
                                                selectedData.push(selectedItem);
                                            } else {
                                                selectedData = selectedData.filter(p => p.id !== selectedItem.id);
                                            }
                                        },
                                        'selectAllCallback': function (nodes, selected, indeterminate) {
                                            if (indeterminate) return;
                                            if (selected) {
                                                var items = $('#' + tableElement).DataTable().rows({selected: true}).data();
                                                $.each(items, function (index, item) {
                                                    selectedData.push(item);
                                                });
                                            } else {
                                                var items = $('#' + tableElement).DataTable().data();
                                                $.each(items, function (index, item) {
                                                    selectedData = selectedData.filter(p => p.id !== item.id);
                                                });
                                            }
                                        }
                                    },

                                },
                                {
                                    "targets": 4,
                                    "render": function (data, type, row) {
                                        return data == null ? '' : '<div class="text-right">' + formatNumber(data) + '</div>';
                                    }
                                }
                            ],
                            select: type,
                            pagingType: "full_numbers",
                            'order': [[0, 'asc']],
                            "createdRow": function (row, data, index) {
                                if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find("." + comboElement).val()) > -1) {
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
            $('#' + buttonElement).on('click', function (e) {
                e.preventDefault();
                var datas = [];

                var rows_selected = selectedData;
                $.each(rows_selected, function (index, item) {
                    datas.push({id: item.id, title: item.name});
                });
                if (typeof config.searchCallback !== 'undefined') {
                    config.searchCallback(selectData, datas);
                } else {
                    searchCallback(selectData, datas);

                }
                $('#' + modalElement).modal('hide');
            });
        }

        var searchCallback = function (self, datas) {
            var $selectedVehicle = self.parent().find('.' + comboElement);
            // selectedVehicle.empty().trigger("change");
            datas.forEach(function (item) {
                $selectedVehicle.select2("trigger", "select", {
                    data: {id: item.id, title: item.title}
                });
            });
            config.exceptIds = config.exceptIds.concat(datas.map(p => p.id));
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

//
// $(function () {
//     let quotaTable;
//
//     $('#quota-search').on('click', function () {
//         var self = $(this);
//         $('#quota_modal').modal();
//         var type = self.attr('data-type');
//         if (type == null) {
//             type = 'single';
//         }
//         if (!$.fn.dataTable.isDataTable(quotaTable)) {
//             quotaTable = $('#table_quota').DataTable({
//                 serverSide: true,
//                 processing: true,
//                 fixedHeader: {
//                     header: true,
//                     footer: true
//                 },
//                 responsive: true,
//                 language: {
//                     search: "Tìm kiếm :",
//                     lengthMenu: "Hiển thị _MENU_ bản ghi",
//                     info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
//                     infoEmpty: "Không có dữ liệu",
//                     loadingRecords: "Đang tải...",
//                     zeroRecords: "Không có dữ liệu",
//                     emptyTable: "Không có dữ liệu trong bảng",
//                     sInfoFiltered: "(được lọc từ _MAX_ mục)",
//                     select: {
//                         rows: {
//                             _: "Đã chọn %d dòng",
//                             0: "",
//                             1: "Đã chọn 1 dòng"
//                         }
//                     },
//                     paginate: {
//                         first: "Đầu",
//                         previous: "Trước",
//                         next: "Tiếp",
//                         last: "Cuối"
//                     },
//                     processing: '<div class="loader"><img src="' + backendUri + '/css/backend/img/loader.gif"></div>'
//                 },
//                 ajax: quotaUri,
//                 pagingType: "full_numbers",
//                 columns: [
//                     {data: 'id'},
//                     {data: 'quota_code'},
//                     {data: 'name'},
//                     {data: 'title'},
//                     {data: 'total_cost'},
//                 ],
//                 'columnDefs': [
//                     {
//                         'targets': 0,
//                         'checkboxes': {
//                             'selectRow': true
//                         }
//                     },
//                     {
//                         "targets": 4,
//                         "render": function (data, type, row) {
//                             return data == null ? '' : '<div class="text-right">' + formatNumber(data) + '</div>';
//                         }
//                     }
//                 ],
//                 select: type,
//                 'order': [[1, 'asc']],
//                 "createdRow": function (row, data, index) {
//                     if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find(".select-quota").val()) > -1) {
//                         quotaTable.rows(index).select();
//                     }
//                 },
//             });
//         } else {
//             quotaTable.columns().checkboxes.deselectAll();
//             quotaTable.ajax.reload();
//         }
//     });
//
//     // Handle form submission event
//     $('#btn-quota').on('click', function (e) {
//         e.preventDefault();
//
//         //
//         var quotas = [];
//         // $.each(rows_selected, function (index, rowId) {
//         //     orders.push({id: rowId, title: rowId});
//         // });
//
//         var rows_selected = quotaTable.rows({selected: true}).data();
//         // var rows_selected = vehicleTable.column(0, { search: 'applied' }).checkboxes.selected();
//         $.each(rows_selected, function (index, item) {
//             quotas.push({id: item.id, title: item.name});
//         });
//
//         quotaSearchCallback(quotas);
//         $('#quota_modal').modal('hide');
//
//     });
//
// });
//
//
// function quotaSearchCallback(quotas) {
//     $('.select-quota').empty().trigger("change");
//     quotas.forEach(function (item) {
//         $(".select-quota").select2("trigger", "select", {
//             data: {id: item.id, title: item.title}
//         });
//     });
// }

