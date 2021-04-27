$(function () {
    var data_origin = $('#district_id').data('origin');
    loadDistrict(data_origin);
    $("#province_id").change(function() {
        loadDistrict('0');
    });

    function loadDistrict(selected) {
        var container = $('#district_id');
        sendRequest({
            url: url,
            type: 'GET',
            data: {
                'province_id' : $("#province_id").val()
            }
        }, function (response) {
            if (!response.ok) {
                return showErrorFlash(response.message);
            }
            container.html(response.data.content);
            if (0 < parseInt(selected)) {
                $("#district_id").val(selected);
            }
        });
    };
});

