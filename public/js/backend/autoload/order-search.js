// Hàm tạo đối tượng tìm kiếm Đơn hàng nhanh
// CreatedBy nlhoang 09/04/2020
function createOrderQuickSearch() {
    var quickSearch = (function (searchConfig) {
        var dataTable,
            selectData,
            selectedData = [];
        var config = searchConfig || {};
        var entity = 'order';
        var searchElement = config.searchElement || entity + '-search',
            comboElement = config.comboElement || 'select-' + entity,
            buttonElement = config.buttonElement || 'btn-' + entity,
            modalElement = config.modalElement || entity + '_modal',
            tableElement = config.tableElement || 'table_' + entity;
        config.exceptIds = config.exceptIds || [];
        config.url = config.url || searchOrderUrl;
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

                    var data;
                    if (typeof config.filter !== 'undefined') {
                        data = config.filter;
                    } else {
                        data = function (d) {
                            d.route_id = $('#route_id').val();
                            d.vehicle_id = $('#vehicle_id').val();
                            d.driver_id = $('#driver_id').length > 0 ? $('#driver_id').val() : $('#primary_driver_id').val();
                            d.exceptIds = config.exceptIds.join(',');
                        };
                    }

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
                                {data: 'order_code'},
                                {data: 'order_no'},
                                {data: 'status'},
                                {data: 'precedence'},
                                {data: 'customer_name'},
                                {data: 'customer_mobile_no'}
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
                                    "targets": [3],
                                    "render": function (data, type, row) {
                                        var statusName = '';
                                        var className = '';
                                        switch (Number(data)) {
                                            case 1:
                                                if (row['status_partner'] == 1) {
                                                    statusName = 'Chờ đối tác vận tải xác nhận';
                                                } else if (row['status_partner'] == 4) {
                                                    statusName = 'Đối tác vận tải yêu cầu sửa';
                                                }
                                                className = 'light';
                                                break;
                                            case 2:
                                                statusName = 'Sẵn sàng';
                                                className = 'secondary';
                                                break;
                                            case 3:
                                                statusName = 'Chờ nhận hàng';
                                                className = 'brown';
                                                break;
                                            case 4:
                                                statusName = 'Đang vận chuyển';
                                                className = 'blue';
                                                break;
                                            case 5:
                                                statusName = 'Hoàn thành';
                                                className = 'success';
                                                break;
                                            case 6:
                                                statusName = 'Hủy';
                                                className = 'dark';
                                                break;
                                            case 7:
                                                statusName = 'Tài xế xác nhận';
                                                className = 'stpink';
                                                break;

                                        }
                                        return '<span class="badge badge-' + className + '">' + statusName + '</span>';
                                    }
                                },
                                {
                                    "targets": [4],
                                    "render": function (data, type, row) {
                                        var result = '';
                                        switch (Number(data)) {
                                            case 3:
                                                result = '<span class="fa fa-star text-warning"></span>\n' +
                                                    '                                    <span class="fa fa-star text-warning"></span>\n' +
                                                    '                                    <span class="fa fa-star text-warning"></span>';
                                                break;
                                            case 4:
                                                result = ' <span class="fa fa-star text-warning"></span>\n' +
                                                    '                                    <span class="fa fa-star text-warning"></span>';
                                                break;
                                            case 5:
                                                result = '<span class="fa fa-star text-warning"></span>';
                                                break;
                                        }
                                        return result;
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
                    datas.push({id: item.id, title: item.order_code});
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
//
// $(function () {
//     let orderTable;
//     let selectOrder;
//     var selectedOrder = [];
//     if (orderUri) {
//         $('#order-search').on('click', function () {
//             var self = $(this);
//             selectOrder = self;
//             $('#order_modal').modal();
//             var type = self.attr('data-type');
//             if (type == null) {
//                 type = 'single';
//             }
//
//             var data = function (d) {
//                 d.route_id = $('#route_id').val();
//                 d.vehicle_id = $('#vehicle_id').val();
//                 d.driver_id = $('#driver_id').length > 0 ? $('#driver_id').val() : $('#primary_driver_id').val();
//             };
//             var orders = self.closest(".select2-bootstrap-prepend").find(".select-order").val();
//             selectedOrder = orders ? selectedOrder.filter(p => orders.indexOf(p.id) > -1) : [];
//
//             if (!$.fn.dataTable.isDataTable(orderTable)) {
//                 orderTable = $('#table_order').DataTable({
//                     serverSide: true,
//                     processing: true,
//                     responsive: true,
//                     fixedHeader: {
//                         header: true,
//                         footer: true
//                     },
//                     language: {
//                         search: "Tìm kiếm :",
//                         lengthMenu: "Hiển thị _MENU_ bản ghi",
//                         info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
//                         infoEmpty: "Không có dữ liệu",
//                         loadingRecords: "Đang tải...",
//                         zeroRecords: "Không có dữ liệu",
//                         emptyTable: "Không có dữ liệu trong bảng",
//                         sInfoFiltered: "(được lọc từ _MAX_ mục)",
//                         select: {
//                             rows: {
//                                 _: "Đã chọn %d dòng",
//                                 0: "",
//                                 1: "Đã chọn 1 dòng"
//                             }
//                         },
//                         paginate: {
//                             first: "Đầu",
//                             previous: "Trước",
//                             next: "Tiếp",
//                             last: "Cuối"
//                         },
//                         processing: '<div class="loader"><img src="' + backendUri + '/css/backend/img/loader.gif"></div>'
//                     },
//                     // ajax: {
//                     //     "url": orderUri,
//                     //     "dataType": "json",
//                     //     "type": "POST",
//                     //     "data": {
//                     //         _token: token
//                     //     }
//                     // },
//                     ajax: {
//                         "url": orderUri,
//                         "data": data
//                     },
//                     columns: [
//                         {data: 'id'},
//                         {data: 'order_code'},
//                         {data: 'order_no'},
//                         {data: 'status'},
//                         {data: 'precedence'},
//                         {data: 'customer_name'},
//                         {data: 'customer_mobile_no'}
//                     ],
//                     'columnDefs': [
//                         {
//                             'targets': 0,
//                             'checkboxes': {
//                                 'selectRow': true,
//                                 'selectCallback': function (nodes, selected) {
//                                     var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
//                                     if (selected) {
//                                         selectedOrder.push(selectedItem);
//                                     } else {
//                                         selectedOrder = selectedOrder.filter(p => p.id !== selectedItem.id);
//                                     }
//                                 },
//                                 'selectAllCallback': function (nodes, selected, indeterminate) {
//                                     if (indeterminate) return;
//                                     if (selected) {
//                                         var items = $('#table_order').DataTable().rows({selected: true}).data();
//                                         $.each(items, function (index, item) {
//                                             selectedOrder.push(item);
//                                         });
//                                     } else {
//                                         var items = $('#table_order').DataTable().data();
//                                         $.each(items, function (index, item) {
//                                             selectedOrder = selectedOrder.filter(p => p.id !== item.id);
//                                         });
//                                     }
//                                 }
//                             }
//                         },
//                         {
//                             "targets": [3],
//                             "render": function (data, type, row) {
//                                 var statusName = '';
//                                 var className = '';
//                                 switch (Number(data)) {
//                                     case 1:
//                                         statusName = 'Khởi tạo';
//                                         className = 'light';
//                                         break;
//                                     case 2:
//                                         statusName = 'Sẵn sàng';
//                                         className = 'secondary';
//                                         break;
//                                     case 3:
//                                         statusName = 'Chờ nhận hàng';
//                                         className = 'brown';
//                                         break;
//                                     case 4:
//                                         statusName = 'Đang vận chuyển';
//                                         className = 'blue';
//                                         break;
//                                     case 5:
//                                         statusName = 'Hoàn thành';
//                                         className = 'success';
//                                         break;
//                                     case 6:
//                                         statusName = 'Hủy';
//                                         className = 'dark';
//                                         break;
//                                     case 7:
//                                         statusName = 'Tài xế xác nhận';
//                                         className = 'stpink';
//                                         break;
//
//                                 }
//                                 return '<span class="badge badge-' + className + '">' + statusName + '</span>';
//                             }
//                         },
//                         {
//                             "targets": [4],
//                             "render": function (data, type, row) {
//                                 var result = '';
//                                 switch (Number(data)) {
//                                     case 3:
//                                         result = '<span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>';
//                                         break;
//                                     case 4:
//                                         result = ' <span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>';
//                                         break;
//                                     case 5:
//                                         result = '<span class="fa fa-star text-warning"></span>';
//                                         break;
//                                 }
//                                 return result;
//                             }
//                         }
//                     ],
//                     select: type,
//                     pagingType: "full_numbers",
//                     "createdRow": function (row, data, index) {
//                         if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find(".select-order").val()) > -1) {
//                             orderTable.rows(index).select();
//                         }
//                     },
//                     "rowCallback": function (row, data) {
//                         if ($.inArray(data['id'], selectedOrder) !== -1) {
//                             $(row).addClass('selected');
//                         }
//                     }
//                 });
//             } else {
//                 orderTable.columns().checkboxes.deselectAll();
//                 orderTable.ajax.reload();
//             }
//         });
//
//         // Handle form submission event
//         $('#btn-order').on('click', function (e) {
//             e.preventDefault();
//
//             //
//             var orders = [];
//             // $.each(rows_selected, function (index, rowId) {
//             //     orders.push({id: rowId, title: rowId});
//             // });
//
//             var rows_selected = selectedOrder;
//             $.each(rows_selected, function (index, item) {
//                 orders.push({id: item.id, title: item.order_code});
//             });
//
//             orderSearchCallback(selectOrder, orders);
//             $('#order_modal').modal('hide');
//
//         });
//     }
//     if (orderCustomerUri) {
//         $('#order-customer-search').on('click', function () {
//             var self = $(this);
//             selectOrder = self;
//             $('#order_modal').modal();
//             var type = self.attr('data-type');
//             if (type == null) {
//                 type = 'single';
//             }
//
//             var data = function (d) {
//             };
//             var orders = self.closest(".select2-bootstrap-prepend").find(".select-order").val();
//             selectedOrder = orders ? selectedOrder.filter(p => orders.indexOf(p.id) > -1) : [];
//
//             if (!$.fn.dataTable.isDataTable(orderTable)) {
//                 orderTable = $('#table_order').DataTable({
//                     serverSide: true,
//                     processing: true,
//                     responsive: true,
//                     fixedHeader: {
//                         header: true,
//                         footer: true
//                     },
//                     language: {
//                         search: "Tìm kiếm :",
//                         lengthMenu: "Hiển thị _MENU_ bản ghi",
//                         info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
//                         infoEmpty: "Không có dữ liệu",
//                         loadingRecords: "Đang tải...",
//                         zeroRecords: "Không có dữ liệu",
//                         emptyTable: "Không có dữ liệu trong bảng",
//                         sInfoFiltered: "(được lọc từ _MAX_ mục)",
//                         select: {
//                             rows: {
//                                 _: "Đã chọn %d dòng",
//                                 0: "",
//                                 1: "Đã chọn 1 dòng"
//                             }
//                         },
//                         paginate: {
//                             first: "Đầu",
//                             previous: "Trước",
//                             next: "Tiếp",
//                             last: "Cuối"
//                         },
//                         processing: '<div class="loader"><img src="' + backendUri + '/css/backend/img/loader.gif"></div>'
//                     },
//                     // ajax: {
//                     //     "url": orderUri,
//                     //     "dataType": "json",
//                     //     "type": "POST",
//                     //     "data": {
//                     //         _token: token
//                     //     }
//                     // },
//                     ajax: {
//                         "url": orderUri,
//                         "data": data
//                     },
//                     columns: [
//                         {data: 'id'},
//                         {data: 'order_code'},
//                         {data: 'order_no'},
//                         {data: 'status'},
//                         {data: 'precedence'},
//                         {data: 'customer_name'},
//                         {data: 'customer_mobile_no'}
//                     ],
//                     'columnDefs': [
//                         {
//                             'targets': 0,
//                             'checkboxes': {
//                                 'selectRow': true,
//                                 'selectCallback': function (nodes, selected) {
//                                     var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
//                                     if (selected) {
//                                         selectedOrder.push(selectedItem);
//                                     } else {
//                                         selectedOrder = selectedOrder.filter(p => p.id !== selectedItem.id);
//                                     }
//                                 },
//                                 'selectAllCallback': function (nodes, selected, indeterminate) {
//                                     if (indeterminate) return;
//                                     if (selected) {
//                                         var items = $('#table_order').DataTable().rows({selected: true}).data();
//                                         $.each(items, function (index, item) {
//                                             selectedOrder.push(item);
//                                         });
//                                     } else {
//                                         var items = $('#table_order').DataTable().data();
//                                         $.each(items, function (index, item) {
//                                             selectedOrder = selectedOrder.filter(p => p.id !== item.id);
//                                         });
//                                     }
//                                 }
//                             }
//                         },
//                         {
//                             "targets": [3],
//                             "render": function (data, type, row) {
//                                 var statusName = '';
//                                 var className = '';
//                                 switch (Number(data)) {
//                                     case 1:
//                                         statusName = 'Khởi tạo';
//                                         className = 'light';
//                                         break;
//                                     case 2:
//                                         statusName = 'Sẵn sàng';
//                                         className = 'secondary';
//                                         break;
//                                     case 3:
//                                         statusName = 'Chờ nhận hàng';
//                                         className = 'brown';
//                                         break;
//                                     case 4:
//                                         statusName = 'Đang vận chuyển';
//                                         className = 'blue';
//                                         break;
//                                     case 5:
//                                         statusName = 'Hoàn thành';
//                                         className = 'success';
//                                         break;
//                                     case 6:
//                                         statusName = 'Hủy';
//                                         className = 'dark';
//                                         break;
//                                     case 7:
//                                         statusName = 'Tài xế xác nhận';
//                                         className = 'stpink';
//                                         break;
//
//                                 }
//                                 return '<span class="badge badge-' + className + '">' + statusName + '</span>';
//                             }
//                         },
//                         {
//                             "targets": [4],
//                             "render": function (data, type, row) {
//                                 var result = '';
//                                 switch (Number(data)) {
//                                     case 3:
//                                         result = '<span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>';
//                                         break;
//                                     case 4:
//                                         result = ' <span class="fa fa-star text-warning"></span>\n' +
//                                             '                                    <span class="fa fa-star text-warning"></span>';
//                                         break;
//                                     case 5:
//                                         result = '<span class="fa fa-star text-warning"></span>';
//                                         break;
//                                 }
//                                 return result;
//                             }
//                         }
//                     ],
//                     select: type,
//                     pagingType: "full_numbers",
//                     "createdRow": function (row, data, index) {
//                         if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find(".select-order").val()) > -1) {
//                             orderTable.rows(index).select();
//                         }
//                     },
//                     "rowCallback": function (row, data) {
//                         if ($.inArray(data['id'], selectedOrder) !== -1) {
//                             $(row).addClass('selected');
//                         }
//                     }
//                 });
//             } else {
//                 orderTable.columns().checkboxes.deselectAll();
//                 orderTable.ajax.reload();
//             }
//         });
//
//         // Handle form submission event
//         $('#btn-order').on('click', function (e) {
//             e.preventDefault();
//
//             //
//             var orders = [];
//             // $.each(rows_selected, function (index, rowId) {
//             //     orders.push({id: rowId, title: rowId});
//             // });
//
//             var rows_selected = selectedOrder;
//             $.each(rows_selected, function (index, item) {
//                 orders.push({id: item.id, title: item.order_code});
//             });
//
//             orderCustomerSearchCallback(selectOrder, orders);
//             $('#order_modal').modal('hide');
//
//         });
//     }
// });
//
//
// function orderSearchCallback(self, orders) {
//     // $('.select-order').empty().trigger("change");
//     // orders.forEach(function (item) {
//     //     $(".select-order").select2("trigger", "select", {
//     //         data: {id: item.id, title: item.title}
//     //     });
//     // });
//
//     var selectOrder = self.parent().parent().parent().find('.select-order');
//     selectOrder.empty().trigger("change");
//     orders.forEach(function (item) {
//         selectOrder.select2("trigger", "select", {
//             data: {id: item.id, title: item.title}
//         });
//     });
//
//     self = null;
// }
// function orderCustomerSearchCallback(self, orders) {
//
//     var selectOrder = self.parent().parent().parent().find('.select-order-customer');
//     selectOrder.empty().trigger("change");
//     orders.forEach(function (item) {
//         selectOrder.select2("trigger", "select", {
//             data: {id: item.id, title: item.title}
//         });
//     });
//
//     self = null;
// }