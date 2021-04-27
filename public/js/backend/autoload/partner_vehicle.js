$(function () {
    if ($('#map').length > 0) {
        new google.maps.event.addDomListener(window, 'load', initMapPage);
    }

    if (typeof cboSelect2 !== "undefined") {
        if (typeof driverDropdownUri !== "undefined") {
            cboSelect2.driver(driverDropdownUri, '', '', {all: '', partner_id: $('#partner_id').val()});
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
            fileIDs.val(fileIDs.val() == '' ? response.id : fileIDs.val() + ';' + response.id);

        };
        config.customRemovedUpload = function (configID) {
            var fileIDs = $('#' + configID + '_file_id');
            return fileIDs;
        };
        config.customFilterFile = function (configID, existingFiles) {
            var mockFiles = existingFiles.filter(item => item.vehicle_config_file_id == configID);
            return mockFiles;

        };
        var dropzoneOneLog = createDropzone();
        dropzoneOneLog(config).init();
    }

    if (typeof createDriverQuickSearch != "undefined") {
        var driverQuickSearch = createDriverQuickSearch();
        if (typeof searchDriverExceptIds != "undefined") {
            var config = {};
            config.exceptIds = searchDriverExceptIds;
            config.partnerId = $('#partner_id').val();
            driverQuickSearch(config).init();
        }
    }

});

function getVehicleHistory() {
    var vehicle_id = $('#vehicle_id').val();
    var start_date = $("#vehicle_start_date").val();
    var end_date = $("#vehicle_end_date").val();
    var driver_id = $("#driver_id").val();
    var customer_id = $("#customer_id").val();
    sendRequest({
        url: vehicleHistoryUrl,
        type: 'GET',
        data: {
            vehicle_id: vehicle_id,
            start_date: start_date,
            end_date: end_date,
            driver_id: driver_id,
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

                $('#driver_size').html(response.data.drivers_size);
                $('#body_content_driver').html('');
                $('#body_content_driver').append(response.data.drivers_content);
                $('#paginate_content_driver').html('');
                $('#paginate_content_driver').append(response.data.drivers_pagination);

                $('#vehicle-history').modal();
            } else {
                return showErrorFlash('Lỗi');
            }
        }
    });
}

$(document).on('click', '.vehicle-history', function () {
    $('#driver_id')
        .find('option')
        .remove();
    $('#customer_id')
        .find('option')
        .remove();
    $('#vehicle_id').val($(this).data('id'));
    $('#vehicle-history-label').html('Lịch sử của xe ' + $(this).data('name'));
    getVehicleHistory();
});
$("#vehicle_history_submit").click(function () {
    getVehicleHistory();
});
$("#driver-remove").click(function () {
    $('#driver_id')
        .find('option')
        .remove();
});
$("#customer-remove").click(function () {
    $('#customer_id')
        .find('option')
        .remove();
});

function handleOrderTableAction(page) {
    var vehicle_id = $('#vehicle_id').val();
    var start_date = $("#vehicle_start_date").val();
    var end_date = $("#vehicle_end_date").val();
    var driver_id = $("#driver_id").val();
    var customer_id = $("#customer_id").val();
    var per_page = $("#per_page_order").val();
    var sort_name = $("#sort_field_order").val();
    var sort_type = $("#sort_type_order").val();
    sendRequest({
        url: orderTableActionUrl,
        method: 'get',
        data: {
            vehicle_id: vehicle_id,
            start_date: start_date,
            end_date: end_date,
            driver_id: driver_id,
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
                $('#body_content_order').html('');
                $('#body_content_order').append(response.data.orders_content);
                $('#paginate_content_order').html('');
                $('#paginate_content_order').append(response.data.orders_pagination);
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

function handleDriverTableAction(page) {
    var vehicle_id = $('#vehicle_id').val();
    var start_date = $("#vehicle_start_date").val();
    var end_date = $("#vehicle_end_date").val();
    var driver_id = $("#driver_id").val();
    var customer_id = $("#customer_id").val();
    var per_page = $("#per_page_driver").val();
    var sort_name = $("#sort_field_driver").val();
    var sort_type = $("#sort_type_driver").val();

    sendRequest({
        url: driverTableActionUrl,
        method: 'get',
        data: {
            vehicle_id: vehicle_id,
            start_date: start_date,
            end_date: end_date,
            driver_id: driver_id,
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
                $('#body_content_driver').html('').append(response.data.drivers_content);
                $('#paginate_content_driver').html('').append(response.data.drivers_pagination);
            } else {
                return showErrorFlash('Lỗi');
            }

        }
    });
}

$('#per_page_driver').on('change', function () {
    handleDriverTableAction(1);
});
$(".sorting-driver").click(function (e) {
    e.preventDefault();
    $('#sort_field_driver').val($(this).data('name'));
    let sortType = $('#sort_type_driver'),
        sortTypeValue = sortType.val();
    if (sortTypeValue === 'desc' || isEmpty(sortTypeValue)) {
        sortType.val('asc');
    } else {
        sortType.val('desc');
    }
    handleDriverTableAction(1);
});

$(document).on('click', '.gps-history', function () {
    $('#vehicle_id').val($(this).data('id'));
    $('#vehicle_plate').val($(this).data('name'));
    $('#vehicle-gps-history-label').html('Lịch sử GPS của xe ' + $(this).data('name'));
    getVehicleGpsHistory();

});
$("#vehicle_gps_history_submit").click(function () {
    getVehicleGpsHistory();
});

function getVehicleGpsHistory() {
    let vehicle_id = $('#vehicle_id').val(),
        vehicle_plate = $('#vehicle_plate').val(),
        from_date = $("#gps_from_date").val(),
        from_time = $("#gps_from_time").val(),
        to_date = $("#gps_to_date").val(),
        to_time = $("#gps_to_time").val();

    sendRequest({
        url: vehicleGpsHistoryUrl,
        type: 'GET',
        data: {
            vehicle_id: vehicle_id,
            vehicle_plate: vehicle_plate,
            from_date: from_date,
            from_time: from_time,
            to_date: to_date,
            to_time: to_time
        }
    }, function (response) {
        console.log('**** response', response);
        if (!response.ok) {
            return showErrorFlash(response.message);
        } else {
            console.log('**** response.data.error_code', response.data.error_code);
            if (response.data.error_code == 100) {
                initMap(response.data.data);
                $('#vehicle-gps-history').modal();
            } else {
                return showErrorFlash('Lỗi');
            }
        }
    });
}

var map;
var markers = [];
var location, lat = 21.0464404, lng = 105.7936427;

function initMap(locations) {
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: new google.maps.LatLng(lat, lng),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow({});

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
        if (i == locations.length - 1) {
            map.setCenter(new google.maps.LatLng(locations[i][1], locations[i][2]));
        }
    }
}

function initMapPage() {
    var latlng = new google.maps.LatLng(lat, lng);
    map = new google.maps.Map(document.getElementById('map'), {
        center: latlng,
        zoom: 15
    });
    getLocation();
}

function getLocation() {
    clearMarkers();
    var latlngShow = new google.maps.LatLng(lat, lng);
    var marker = new google.maps.Marker({
        map: map,
        position: latlngShow,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29)
    });
    map.setCenter(marker.getPosition());
}

function clearMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null)
    }
    markers = [];
}

$(document).on('keyup', '.capacity', function () {
    var capacity = 1;
    $('.capacity').each(function (index, value) {
        capacity *= parseFloat($(this).val().replace(/\./g, "").replace(/,/g, '.'));
    })
    $('[name=volume]').val(formatNumber(capacity));
});