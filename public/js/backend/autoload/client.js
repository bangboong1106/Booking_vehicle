var Customer = function (parent) {
    if (parent == null ||
        typeof parent == "undefined"
    ) {
        parent = "";
    }

    function changeView() {
        var type = $(parent + " input[name='type']:checked").val();
        $(parent + " #customer_type").val(type);
        $(parent + " #type").val(type);
        if (type == 1) {
            $(parent + " .corporate").show();
            $(parent + " .individual").hide();
        } else {
            $(parent + " .individual").show();
            $(parent + " .corporate").hide();
        }
    }

    changeView();
    // event radio change type customer
    $(document).on('click', parent + " input[name='type']", function () {
        changeView();
    });
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
