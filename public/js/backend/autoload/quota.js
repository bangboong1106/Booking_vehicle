$(function () {
    if (typeof cboSelect2 !== "undefined") {
        if (typeof driverDropdownUri !== "undefined") {
            cboSelect2.driver(driverDropdownUri);
        }
        if (typeof urlVehicle !== "undefined") {
            cboSelect2.vehicle(urlVehicle);
        }
        if (typeof urlLocation !== "undefined") {
            cboSelect2.location(urlLocation);
        }
        if (typeof orderDropdownUri !== "undefined") {
            cboSelect2.order(orderDropdownUri);
        }
        if (typeof routeDropdownUri !== "undefined") {
            cboSelect2.routes(routeDropdownUri);
        }

    }

    let addCompleteModal = $('#add_complete');
    addCompleteModal.on('hide.bs.modal', function (e) {
        let entity = addCompleteModal.data('entity'),
            model = addCompleteModal.data('model'),
            button = addCompleteModal.data('button');

        switch (model) {
            case 'location':
                addLocationComplete(entity, button);
                break;
            default:
                return;
        }
    });

    function addLocationComplete(entity, button) {
        let locationSelect = button.closest('.input-group').find('.select-location');

        locationSelect.empty().append('<option value="' + entity.id + '" title="' + entity.title + '">'
            + entity.title + '</option>').val(entity.id).trigger('change');
    }

    bindLocations();

    bindCosts();

    // Xử lý thêm địa điểm
    // Createdby nlhoang 01.05.2019
    $(document).on('click', '.add-plus', function (e) {
        generateLocationItem(void 0);
    });

    $(document).on('keyup', '.timeline.location .datepicker,.timeline.location .timepicker', function (e) {
        getLocations(false);
    });

    $('.timeline.location .datepicker').datetimepicker().on('dp.change', function (ev) {
        getLocations(false);
    });

    $('.timeline.location .timepicker').datetimepicker().on('dp.change', function (ev) {
        getLocations(false);
    });

    $(document).on('select2:select', '.select-location', function (e) {
        let data = e.params.data,
            text = $(data.text),
            groupName = $(text[text.length - 1]).data('group'),
            groupId = $(text[text.length - 1]).data('id'),
            select = $(e.target),
            panelGroup = select.closest('.panel').find('.panel-group');
        if (groupName == '') {
            panelGroup.find('span').text('');
            panelGroup.find('.location-group-id').val(0);
            panelGroup.removeClass('active');
        } else {
            panelGroup.find('span').text(groupName);
            panelGroup.hasClass('active') ? null : panelGroup.addClass('active');
            panelGroup.find('.location-group-id').val(groupId);
        }
        getLocations(true);
    });

    $(document).on('select2:clear', '.select-location', function (e) {
        let select = $(e.target),
            panelGroup = select.closest('.panel').find('.panel-group');
        panelGroup.find('span').text('');
        panelGroup.removeClass('active');
    });

    // Xóa trên bảng chi phí
    // Createdby nlhoang 01.05.2019
    $(document).on('click', '.delete-cost', function (e) {
        e.preventDefault();
        e.stopPropagation();
        //Neu xoa het thi add them 1 item
        var count = $('#body_content').find('tr').length;
        if (count <= 1) {
            generateCostItem(void 0);
        }
        $(this).parent('td').parent('tr:first').remove();
        var rowNumber = $('.table-cost').find('.row-number');
        rowNumber.html($('.table-cost').find("tbody tr").length);
        getCosts();
        self.getTotalCost();
    });

    // THực hiện xóa địa điểm trên lộ trình
    // Createdby nlhoang 01.05.2019
    $(document).on('click', '.delete-timeline-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($('.timeline.location').find('.timeline-item:not(:last)').length === 2) {
            $('#warning-delete').modal('show');
            return;
        }
        $(this).closest('.timeline-item').remove();
        getLocations(true);

    });

    // Thêm mới 1 dòng dữ liệu chi phí
    // Createdby nlhoang 01.05.2019
    $(document).on('click', '.l-create.cost', function (e) {
        generateCostItem(void 0);
    });

    // Lưu thông tin địa điểm vào hidden field
    // Createdby nlhoang 03.05.2019
    $(document).on('change', '.table-cost .select-cost', function (e) {
        getCosts();
    });

    // Tự động tính tổng chi phí
    // Createdby nlhoang 01.05.2019
    $(document).on("keyup", " .number-input", function (event) {
        getTotalCost();
        getCosts();
    });


    // Load thông tin địa điểm chi phí theo chuyển
    // Createdby nlhoang 09.05.2019
    $(document).on('change', '.select-route', function (event) {
        var id = $(event.target).val();
        confirmLocation(id);
    });

    $(document).on('click', '#update_quota', function (event) {
        $('#confirm-route').modal('show');
    });

    $(document).on('click', '#cancel-confirm-route', function (event) {
        $("#update_route").val(false);
        $("#quota_form").submit()
    });

    $(document).on('click', '#update-confirm-route', function (event) {
        $("#update_route").val(true);
        $("#quota_form").submit()
    });

});

// Lấy thông tin danh sách địa điểm
// Createdby nlhoang 03.05.2019
function getLocations(isCallServer = false) {
    var self = this;
    var locations = [];
    $('.timeline.location').find(".select-location").each((index, item) => {
        let panel = $(item).closest('.panel'),
            location = {
                location_id: $(item).val(),
                location_title: $(item).select2('data')[0] ? $(item).select2('data')[0].title : '',
                location: {
                    group: {
                        id: panel.find('.location-group-id').val()
                    }
                }
            };
        var date = moment($($('.timeline.location').find(".datepicker")[index]).val(), 'DD-MM-YYYY');
        if (date.isValid()) {
            location.location_date = date.format('YYYY-MM-DD')
        } else {
            location.location_date = null;
        }
        var time = $($('.timeline.location').find(".timepicker")[index]).val();
        location.location_time = time != "" ? time : null;
        locations.push(location);
    });

    $('#locations').val(JSON.stringify(locations));


    if (isCallServer) {
        // Load thông tin chi phí khi thay đổi địa điểm
        if (locations.filter(p => p.location_id == '' || p.location_id == null).length == 0) {
            let quotaId = $('input[name="id"]').val();
            var vehicleGroupId = $('#vehicle_group_id').val();
            sendRequestNotLoading({
                    url: costsUri,
                    type: 'POST',
                    data: {
                        'locations': JSON.stringify(locations),
                        'quota-id': quotaId ? quotaId : 0,
                        'vehicle_group_id': vehicleGroupId
                    }
                },
                function (response) {
                    if (response == '' || response == null) return;
                    $('#confirm-costs').modal('show');
                    $('#close-confirm-costs').unbind().on('click', function () {
                        $('#confirm-costs').modal('hide');
                    });

                    $('#update-confirm-costs').unbind().on('click', function () {
                        $('#costs').val(response);
                        $(".table-cost").find("tbody tr:gt(0)").remove();
                        self.bindCosts();
                        self.getTotalCost();
                        $('#confirm-costs').modal('hide');
                    });
                });
        }

    }
}

// Lấy danh sách chi phí
function getCosts() {
    var $dataRows = $('.table-cost').find("tbody tr");
    var costs = [];
    $dataRows.each(function (index, item) {
        var cost = $(item).find('.select-cost');
        var amount = $(item).find('.number-input').val().replace(/\./g, "").replace(/,/g, '.');
        costs.push(
            {
                receipt_payment_id: cost.val(),
                receipt_payment_name: cost.select2('data')[0] ? cost.select2('data')[0].text.trim() : '',
                amount: amount
            }
        )
    });

    $('#costs').val(JSON.stringify(costs));
}

// Xử lý thêm địa điểm
// Createdby nlhoang 04.05.2019
function generateLocationItem(entity) {
    var locations = $('.timeline.location');

    var items = locations.find('.timeline-item:not(:last)');
    if (!items) return;
    var lastItem = $(items[items.length - 1]);

    lastItem.find('.select-location').select2('destroy');

    var id = parseInt(lastItem.find('.select-location').prop('id').match(/\d/), 10) + 1;
    var newItem = lastItem.clone();

    newItem.find('.datepicker').val(null);
    newItem.find('.timepicker').val(null);
    newItem.find('.select-location').removeAttr('data-select2-id');

    newItem.find('.select-location')
        .prop('id', 'location_' + id)
        .prop('name', 'location_' + id);

    newItem.find('.datepicker')
        .prop('id', 'location_date_' + id)
        .prop('name', 'location_date_' + id)
        .prop('placeholder', 'Ngày');
    newItem.find('.datepicker').datetimepicker({
        format: 'DD-MM-YYYY',
        locale: 'vi',
        useCurrent: false,
    });

    newItem.find('.timepicker')
        .prop('id', 'location_time_' + id)
        .prop('name', 'location_time_' + id)
        .prop('placeholder', 'Giờ');
    newItem.find('.timepicker').datetimepicker({
        format: 'HH:mm',
        locale: 'vi'
    });

    lastItem.after(newItem);
    var options = {
        allowClear: true,
        placeholder: "Vui lòng chọn địa điểm",
        ajax: {
            url: urlLocation,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                data.page = data.page || 1;
                return {
                    results: data.items.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            title: item.title
                        };
                    }),
                    pagination: {
                        more: data.pagination
                    }
                };
            },
        },
        templateResult: function (data) {
            return data.text;
        },
        templateSelection: function (data) {
            if (data.id === '') { // adjust for custom placeholder values
                return 'Vui lòng chọn địa điểm';
            }
            return data.title;
        },
        escapeMarkup: function (m) {
            return m;
        },
        language: 'vi'
    };
    lastItem.find(".select-location").select2(options);

    var newLocation = newItem.find(".select-location");
    newLocation.select2(options);
    // $(newLocation).off('change');

    if (entity) {
        $(newLocation).data('select2')
            .dataAdapter.select({
            id: entity.location_id,
            text: entity.location_title,
            title: entity.location_title
        });
        $(newLocation).on('change', function (e) {
            if (e.currentTarget.value == '') {
                $(e.currentTarget).select2('clear');
            }
        });

        var newDate = newItem.find('.datepicker');
        if (newDate) {
            var date = moment(entity.location_date);
            if (date.isValid()) {
                $(newDate).val(date.format('DD-MM-YYYY'));
            }
        }
        var newTime = newItem.find('.timepicker');
        if (newTime) {
            $(newTime).val(entity.location_time);
        }
        if (entity.location.group) {
            newItem.find('.location-group-id').val(entity.location.group.id);
            newItem.find('.panel-group span').text(entity.location.group.title);
            if (!newItem.find('.panel-group').hasClass('active')) {
                newItem.find('.panel-group').addClass('active');
            }
        } else {
            newItem.find('.panel-group').removeClass('active');
            newItem.find('.panel-group span').text('');
            newItem.find('.location-group-id').val(0);
        }
    } else {
        newLocation.empty();
        newLocation.val("").trigger('change');
        newItem.find('.panel-group').removeClass('active')
        newItem.find('.location-group-id').val(0);
    }

}

// Load thông tin địa điểm
// Createdby nlhoang 04.05.2019
function bindLocations() {
    let locations = $('#locations').val(),
        timeLine = $('.timeline.location');
    if (locations) {
        locations = JSON.parse(locations);
        if (Array.isArray(locations)) {
            locations.forEach((entity, index) => {
                if (index == 0 || index == 1) {
                    var newLocation = timeLine.find('.select-location')[index];
                    if (newLocation) {
                        $(newLocation).select2('data', {
                            id: entity.location_id,
                            text: entity.location_title,
                            title: entity.location_title
                        });

                        $(newLocation).data('select2')
                            .dataAdapter.select({
                            id: entity.location_id,
                            text: entity.location_title,
                            title: entity.location_title
                        });
                        if (entity.location.group) {
                            let panelGroup = $(newLocation).closest('.panel').find('.panel-group');
                            panelGroup.addClass('active');
                            panelGroup.find('span').text(entity.location.group.title);
                            panelGroup.find('.location-group-id').val(entity.location.group.id);
                        }
                    }
                    var newDate = timeLine.find('.datepicker')[index];
                    if (newDate) {
                        var date = moment(entity.location_date);
                        if (date.isValid()) {
                            $(newDate).val(date.format('DD-MM-YYYY'));
                        }
                    }
                    var newTime = timeLine.find('.timepicker')[index];
                    if (newTime) {
                        $(newTime).val(entity.location_time);
                    }
                } else {
                    generateLocationItem(entity);
                }
            });
        }
    }
};

// Tự động tính tổng chi phí
// Createdby nlhoang 01.05.2019
function getTotalCost() {
    var total = 0;
    var $dataRows = $('.table-cost').find("tbody tr");

    $dataRows.each(function () {
        var amount = $(this).find('.number-input').val();
        if (amount != "") {
            amount = $(this).find('.number-input').val().replace(/\./g, "").replace(/,/g, '.');
            total += parseFloat(amount);
        }
    });

    var result = $('.table-cost').find('.result-cost');
    result.html(formatNumber(total));

}

// Load thông tin địa điểm
// Createdby nlhoang 04.05.2019st
function bindCosts() {
    var costs = $('#costs').val();
    if (costs) {
        costs = JSON.parse(costs);
        if (Array.isArray(costs)) {
            costs.forEach((entity, index) => {
                if (index == 0) {
                    var firstCost = $('.table-cost').find('.select-cost')[index];
                    if (firstCost) {
                        $(firstCost).val(entity.receipt_payment_id).trigger('change');
                    }
                    var firstAmount = $('.table-cost').find('.number-input')[index];
                    if (firstAmount) {
                        $(firstAmount).val(formatNumber(entity.amount));
                    }

                } else {
                    generateCostItem(entity);
                }
            });
        }
        $('.number-input').toArray().forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: 'thousand'
            })
        });
        self.getTotalCost();
    }
}

// Thêm mới 1 dòng dữ liệu chi phí
// Createdby nlhoang 04.05.2019
function generateCostItem(entity) {
    var $tableBody = $('.table-cost').find("tbody"),
        $trLast = $tableBody.find("tr:last"),
        $trNew = $trLast.clone();
    $trLast.find('.select-cost').select2('destroy');
    var $trNew = $trLast.clone();
    $trNew.find('.select-cost').removeAttr('data-select2-id');
    $trNew.find('.select-cost option').removeAttr('data-select2-id');

    $trNew.find('td').each(function () {
        var el = $(this).find('.select-cost,input.number-input');
        var id = el.attr('id') || null;
        if (id) {
            var i = id.substr(id.length - 1);
            var prefix = id.substr(0, (id.length - 1));
            el.attr('id', prefix + (+i + 1));
            el.attr('name', prefix + (+i + 1));
        }
    });
    $trLast.after($trNew);
    $trLast.find('.select-cost').select2();
    $trNew.find('.select-cost').select2();
    if (entity) {
        $trNew.find('.select-cost').val(entity.receipt_payment_id).trigger('change');
        $trNew.find('.number-input').val(formatNumber(entity.amount));
    } else {
        $trNew.find('.select-cost').val('').trigger('change');
        $trNew.find('.number-input').val(0);
    }

    var rowNumber = $('.table-cost').find('.row-number');
    rowNumber.html($('.table-cost').find("tbody tr").length);
}

// Xử lý sự kiện khi chọn lại 1 chuyến xe
// Createdby nlhoang 09.05.2019
function routeSearchCallback(routes) {
    var self = this;
    $('.select-route').empty().trigger("change");
    routes.forEach(function (item) {
        $(".select-route").select2("trigger", "select", {
            data: {id: item.id, title: item.title}
        });
    });
    if (routes && routes.length > 0) {
        confirmLocation(routes[0].id);
    }
}

// Xử lý sự kiện khi chọn lại 1 chuyến xe
// Createdby nlhoang 09.05.2019
function confirmLocation(id) {
    sendRequestNotLoading({
            url: routeInfoUri,
            type: 'GET',
            data: {route_id: id}
        },
        function (response) {
            if (response == '' || response == null ||
                (response.locations == '[]' && response.costs == '[]')) return;
            $('#confirm-location').modal('show');
            $('#close-confirm-location').unbind().on('click', function () {
                $('#confirm-location').modal('hide');
            });

            $('#update-confirm-location').unbind().on('click', function () {
                if (response.locations) {
                    $('#locations').val(response.locations);
                    $(".timeline.location").find(".timeline-item:gt(1):not(:last-child)").remove();
                    self.bindLocations();
                }
                if (response.costs) {
                    $('#costs').val(response.costs);
                    $(".table-cost").find("tbody tr:gt(0)").remove();
                    self.bindCosts();
                    self.getTotalCost();
                }
                $('#confirm-location').modal('hide');
            });
        });
}
