var Customer = function (parent) {
    if (parent == null ||
        typeof parent == "undefined"
    ) {
        parent = "";
    }
};
function detailCallback() {
    let modalContainer = $('#detail-panel #customer_model'),
        latShow = modalContainer.find('#latShow').val(),
        lngShow = modalContainer.find('#lngShow').val();

    if (latShow === '' || lngShow === '' || typeof latShow === 'undefined') {
        return false;
    }

    locationObject._showMapView(latShow, lngShow);
}
