// $(function () {
//     let customerTable;
//     let selectedDatas = [];
//     $('body').on('click', '.vehicle-team-search', function () {
//         var self = $(this);
//
//         $('#vehicle_team_modal').modal();
//
//         var type = self.attr('data-type');
//         if (type == null) {
//             type = 'single';
//         }
//         var vehicleTeams = self.closest(".select2-bootstrap-prepend").find(".select-vehicle-team").val();
//         selectedDatas = vehicleTeams ? selectedDatas.filter(p => vehicleTeams.indexOf(p.id) > -1) : [];
//         if (!$.fn.dataTable.isDataTable(customerTable)) {
//             customerTable = $('#table_vehicle_teams').DataTable({
//                 serverSide: true,
//                 processing: true,
//                 responsive: true,
//                 language: {
//                     search: "Tìm kiếm :",
//                     lengthMenu: "Hiển thị _MENU_ bản ghi",
//                     info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
//                     infoEmpty: "Không có dữ liệu",
//                     loadingRecords: "Đang tải...",
//                     zeroRecords: "Không có dữ liệu",
//                     sInfoFiltered: "(được lọc từ _MAX_ mục)",
//                     select: {
//                         rows: {
//                             _: "Đã chọn %d dòng",
//                             0: "",
//                             1: "Đã chọn 1 dòng"
//                         }
//                     },
//                     emptyTable: "Không có dữ liệu trong bảng",
//                     paginate: {
//                         first: "Đầu",
//                         previous: "Trước",
//                         next: "Tiếp",
//                         last: "Cuối"
//                     },
//                     processing: '<div class="loader"><img src="' + backendUri + '/css/backend/img/loader.gif"></div>'
//                 },
//                 ajax: vehicleTeamUri,
//                 select: type,
//                 pagingType: "full_numbers",
//                 columns: [
//                     {data: 'id'},
//                     // {data: 'DT_Row_Index', name: 'DT_Row_Index'},
//                     {data: 'code'},
//                     {data: 'name'},
//
//                 ],
//                 "columnDefs": [
//                     {
//                         'targets': 0,
//                         'checkboxes': {
//                             'selectRow': true,
//                             'selectCallback': function (nodes, selected) {
//                                 var selectedItem = nodes.table().data()[nodes[0]._DT_CellIndex.row];
//                                 if (selected) {
//                                     selectedDatas.push(selectedItem);
//                                 } else {
//                                     selectedDatas = selectedDatas.filter(p => p.id !== selectedItem.id);
//                                 }
//                             },
//                             'selectAllCallback': function (nodes, selected, indeterminate) {
//                                 if (indeterminate) return;
//                                 if (selected) {
//                                     var items = $('#table_vehicle_teams').DataTable().rows({selected: true}).data();
//                                     $.each(items, function (index, item) {
//                                         selectedDatas.push(item);
//                                     });
//                                 } else {
//                                     var items = $('#table_vehicle_teams').DataTable().data();
//                                     $.each(items, function (index, item) {
//                                         selectedDatas = selectedDatas.filter(p => p.id !== item.id);
//                                     });
//                                 }
//                             }
//                         },
//
//                     }
//                 ],
//                 "createdRow": function (row, data, index) {
//                     if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find(".select-vehicle-team").val()) > -1) {
//                         customerTable.rows(index).select();
//                     }
//                 },
//             });
//         } else {
//             customerTable.columns().checkboxes.deselectAll();
//             customerTable.ajax.reload();
//         }
//     });
//
//     $('#btn-vehicle-team').on('click', function (e) {
//         e.preventDefault();
//         var vehicleTeams = [];
//
//
//         var rows_selected = selectedDatas;
//         $.each(rows_selected, function (index, item) {
//             vehicleTeams.push({id: item.id, title: item.name});
//         });
//
//         vehicleTeamSearchCallback(vehicleTeams);
//         $('#vehicle_team_modal').modal('hide');
//     });
//
// });
//
//
// function vehicleTeamSearchCallback(vehicleTeams) {
//     $('.select-vehicle-team').empty().trigger("change");
//
//     vehicleTeams.forEach(function (item) {
//         $(".select-vehicle-team").select2("trigger", "select", {
//             data: {id: item.id, title: item.title}
//         });
//     });
// }


// Hàm tạo đối tượng tìm kiếm Đội Xe nhanh
// CreatedBy nlhoang 08/04/2020
function createVehicleTeamQuickSearch() {
    var quickSearch = (function (searchConfig) {
        var dataTable,
            selectData,
            selectedData = [];
        var config = searchConfig || {};

        var searchElement = config.searchElement || 'vehicle-team-search',
            comboElement = config.comboElement || 'select-vehicle-team',
            buttonElement = config.buttonElement || 'btn-vehicle-team',
            modalElement = config.modalElement || 'vehicle_team_modal',
            tableElement = config.tableElement || 'table_vehicle_team';
        config.exceptIds = config.exceptIds || [];
        config.url = config.url || searchVehicleTeamUrl;
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
                        d.all = all;
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
                                {data: 'code'},
                                {data: 'name'},

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
