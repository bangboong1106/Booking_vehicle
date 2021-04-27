locationObject.init();

let container = $('#location_model'),
    latShow = container.find('#latShow').val(),
    lngShow = container.find('#lngShow').val();
if (typeof latShow !== 'undefined') {
    locationObject._showMap(latShow, lngShow);
}

function detailCallback() {
    let modalContainer = $('#detail-panel #location_model'),
        latShow = modalContainer.find('#latShow').val(),
        lngShow = modalContainer.find('#lngShow').val();

    if (latShow === '' || lngShow === '' || typeof latShow === 'undefined') {
        return false;
    }

    locationObject._showMapView(latShow, lngShow);
}

$(function () {
    $('input[name=location_type]').click(function (e) {
        $('#type').val($(this).val());
    });
});