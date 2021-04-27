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

    }

    $('.add-plus').on('click', function (e) {

        var locations = $('.timeline.location');

        var items = locations.find('.timeline-item:not(:last)');
        if (!items) return;
        var lastItem = $(items[items.length - 1]);

        lastItem.find('.select-location').select2('destroy');

        var id = parseInt(lastItem.find('.select-location').prop('id').match(/\d/), 10) + 1;
        var newItem = lastItem.clone();

        newItem.find('.select-location').removeAttr('data-select2-id');
        newItem.find('.select-location')
            .prop('id', 'location_' + id)
            .prop('name', 'location_' + id);

        lastItem.after(newItem);
        var options = {
            allowClear: true,
            placeholder: "Vui lòng chọn địa điểm",
            ajax: {
                url: urlLocation,
                dataType: 'json'
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
        newItem.find(".select-location").select2(options);
        newItem.find('.select-location').val('').trigger('change');
    });

    $(document).on('click', '.delete-timeline-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('.timeline-item').remove();
    });

    $('.l-create.cost').on('click', function (e) {
        var $tableBody = $('.table-cost').find("tbody"),
            $trLast = $tableBody.find("tr:last"),
            $trNew = $trLast.clone();
        $trLast.find('.select-cost').select2('destroy');
        var $trNew = $trLast.clone();
        $trNew.find('.select-cost').removeAttr('data-select2-id');
        $trNew.find('.select-cost option').removeAttr('data-select2-id');

        $trNew.find('td').each(function () {
            var el = $(this).find('.select-cost,input.input-currency');
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
        $trNew.find('.select-cost').val('').trigger('change');
        $trNew.find('.input-currency').val(0);

        var rowNumber = $('.table-cost').find('.row-number');
        rowNumber.html($('.table-cost').find("tbody tr").length);
    });

    $(document).on('click', '.delete-cost', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).parent('td').parent('tr:first').remove();
        var rowNumber = $('.table-cost').find('.row-number');
        rowNumber.html($('.table-cost').find("tbody tr").length);
    });

    $(document).on("keyup", " .input-currency", function (event) {
        var total = 0;
        var $dataRows = $('.table-cost').find("tbody tr");

        $dataRows.each(function () {
            total += parseFloat($(this).find('.input-currency').val().replace(/,/g, ""));
        });

        var result = $('.table-cost').find('.result-cost');
        result.html(total.toLocaleString("en-EN"));
    });

    //TODO: cách lấy dữ liệu địa điểm
    // $('.timeline.location').find(".select-location").each((index,item)=>console.log($(item).val()));

    //TODO: cách lấy dữ liệu bảng chi phí
    // var $dataRows = $('.table-cost').find("tbody tr");
    // var costs = [];
    // $dataRows.each(function (index, item) {
    //     costs.push(
    //         {
    //             costID: $(item).find('.select-cost').val(),
    //             amount: $(item).find('.input-currency').val()
    //         }
    //     )
    // });
    // console.log(costs);
});



