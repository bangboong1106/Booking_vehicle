$(function () {

    if (typeof cboSelect2 !== "undefined") {
        if (typeof urlVehicle !== "undefined") {
            cboSelect2.vehicle(urlVehicle);
        }
    }

    if (typeof uploadUrl != 'undefined') {
        var config = {};
        config.uploadUrl = uploadUrl;
        config.downloadUrl = downloadUrl;
        config.removeUrl = removeUrl;
        config.publicUrl = publicUrl;
        config.existingFiles = existingFiles;

        config.customSuccessUpload = function (configID, response) {
            var fileIDs = $('#' + configID + '_file_id');
            if (configID == 'avatar') {
                fileIDs = $('#' + configID + '_id');
            }
            fileIDs.val(fileIDs.val() == '' ? response.id : fileIDs.val() + ';' + response.id);

        };
        config.customRemovedUpload = function (configID) {
            var fileIDs = $('#' + configID + '_file_id');
            if (configID == 'avatar') {
                fileIDs = $('#' + configID + '_id');
            }
            return fileIDs;
        };
        config.customFilterFile = function (configID, existingFiles) {
            return existingFiles.filter(item => item.driver_config_id == configID);

        };
        var dropzoneOneLog = createDropzone();
        dropzoneOneLog(config).init();
    }


    function diff_years(dt2, dt1) {
        var diff = (dt2.getTime() - dt1.getTime()) / 1000;
        diff /= (60 * 60 * 24);
        if (Math.round(diff / 365.25) < 0)
            return 0;
        return Math.round(diff / 365.25);
    }

    //Event change date
    $('#work_date').on('dp.change', function (e) {
        var workDateString = $('#work_date').val();
        if (moment(workDateString, "DD-MM-YYYY", true).isValid()) {
            var workDate = moment(workDateString, "DD-MM-YYYY", true).toDate();
            var currentDate = new Date();
            $('#experience_work').attr('value', diff_years(currentDate, workDate));
        }
    });
    // An-hien view nhap tai khoan

    changeCreateAccountView();

    $('#create_account').on('change', function (e) {
        changeCreateAccountView();
    });

    function changeCreateAccountView() {
        var checked = $('#create_account').is(':checked');
        if (checked || $('#create_account').length == 0) {
            $('.create_account_form').show();
            $('#create_account').value = "1";
        } else {
            $('.create_account_form').hide();
            $('#create_account').value = "0";
            // $('#user_name').val(null);
            // $('#email').val(null);
            // $('#password').val(null);
            // $('#password_confirmation').val(null);

        }
    }

    let switchBtn = $('#switchery_is_active');
    switchBtn.on('change', function () {
        if (switchBtn.is(":checked") || switchBtn.length === 0) {
            $("#form_is_active").val("1");
        } else {
            $("#form_is_active").val("0");
        }
    });
});

function getDriverHistory() {
    var driver_id = $('#driver_id').val();
    var start_date = $("#driver_start_date").val();
    var end_date = $("#driver_end_date").val();
    var vehicle_id = $("#vehicle_id").val();
    var customer_id = $("#customer_id").val();
    sendRequest({
        url: driverHistoryUrl,
        type: 'GET',
        data: {
            driver_id: driver_id,
            start_date: start_date,
            end_date: end_date,
            vehicle_id: vehicle_id,
            customer_id: customer_id
        }
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        } else {
            if (response.data.error_code == 100) {
                $('#order_size').html(response.data.orders_size);
                $('#order_money').html(response.data.orders_money);
                $('#body_content_order').html('');
                $('#body_content_order').append(response.data.orders_content);
                $('#paginate_content_order').html('');
                $('#paginate_content_order').append(response.data.orders_pagination);

                $('#vehicle_size').html(response.data.vehicles_size);
                $('#body_content_vehicle').html('');
                $('#body_content_vehicle').append(response.data.vehicles_content);
                $('#paginate_content_vehicle').html('');
                $('#paginate_content_vehicle').append(response.data.vehicles_pagination);

                $('#driver-history').modal();
            } else {
                return showErrorFlash('Lỗi');
            }
        }
    });
}

$(document).on('click', '.driver-history', function () {
    $('#vehicle_id')
        .find('option')
        .remove();
    $('#customer_id')
        .find('option')
        .remove();
    $('#driver_id').val($(this).data('id'));
    $('#driver-history-label').html('Lịch sử của tài xế ' + $(this).data('name'));
    getDriverHistory();
});
$("#driver_history_submit").click(function () {
    getDriverHistory();
});
$("#vehicle-remove").click(function () {
    $('#vehicle_id')
        .find('option')
        .remove();
});
$("#customer-remove").click(function () {
    $('#customer_id')
        .find('option')
        .remove();
});

function handleOrderTableAction(page) {
    var driver_id = $('#driver_id').val();
    var start_date = $("#driver_start_date").val();
    var end_date = $("#driver_end_date").val();
    var vehicle_id = $("#vehicle_id").val();
    var customer_id = $("#customer_id").val();
    var per_page = $("#per_page_order").val();
    var sort_name = $("#sort_field_order").val();
    var sort_type = $("#sort_type_order").val();
    sendRequest({
        url: orderTableActionUrl,
        method: 'get',
        data: {
            driver_id: driver_id,
            start_date: start_date,
            end_date: end_date,
            vehicle_id: vehicle_id,
            customer_id: customer_id,
            page: page,
            per_page: per_page,
            sort_name: sort_name,
            sort_type: sort_type
        }
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        } else {
            if (response.data.error_code === 100) {
                $('#body_content_order').html('').append(response.data.orders_content);
                $('#paginate_content_order').html('').append(response.data.orders_pagination);
            } else {
                return showErrorFlash('Lỗi');
            }

        }
    });
}

$('#per_page_order').on('change', function () {
    handleOrderTableAction(1);
});
$(".sorting-order").click(function (e) {
    e.preventDefault();
    $('#sort_field_order').val($(this).data('name'));
    var sort_type = $('#sort_type_order').val();
    if (sort_type == 'desc' || isEmpty(sort_type)) {
        $('#sort_type_order').val('asc');
    } else {
        $('#sort_type_order').val('desc');
    }
    handleOrderTableAction(1);
});

function handleVehicleTableAction(page) {
    var driver_id = $('#driver_id').val();
    var start_date = $("#driver_start_date").val();
    var end_date = $("#driver_end_date").val();
    var vehicle_id = $("#vehicle_id").val();
    var customer_id = $("#customer_id").val();
    var per_page = $("#per_page_driver").val();
    var sort_name = $("#sort_field_driver").val();
    var sort_type = $("#sort_type_driver").val();

    sendRequest({
        url: vehicleTableActionUrl,
        method: 'get',
        data: {
            driver_id: driver_id,
            start_date: start_date,
            end_date: end_date,
            vehicle_id: vehicle_id,
            customer_id: customer_id,
            page: page,
            per_page: per_page,
            sort_name: sort_name,
            sort_type: sort_type
        }
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        } else {
            if (response.data.error_code == 100) {
                $('#body_content_vehicle').html('');
                $('#body_content_vehicle').append(response.data.vehicles_content);
                $('#paginate_content_vehicle').html('');
                $('#paginate_content_vehicle').append(response.data.vehicles_pagination);
            } else {
                return showErrorFlash('Lỗi');
            }

        }
    });
}

$('#per_page_vehicle').on('change', function () {
    handleVehicleTableAction(1);
});
$(".sorting-vehicle").click(function (e) {
    e.preventDefault();
    $('#sort_field_vehicle').val($(this).data('name'));
    var sort_type = $('#sort_type_vehicle').val();
    if (sort_type == 'desc' || isEmpty(sort_type)) {
        $('#sort_type_vehicle').val('asc');
    } else {
        $('#sort_type_vehicle').val('desc');
    }
    handleVehicleTableAction(1);
});

$(document).on('click', '.vehicle-team-detail', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    var showModal = $('#modal_show');
    showModal.data('url', $(this).attr('data-show-url'))
        .data('model', 'vehicle-team')
        .data('id', id);
    showModal.modal('show');
});