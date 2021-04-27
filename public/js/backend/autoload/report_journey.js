let me = this;

$(function () {
    if (typeof cboSelect2 !== "undefined") {
        if (typeof comboVehiclesUri !== "undefined") {
            cboSelect2.vehicle(comboVehiclesUri);
        }
        if (typeof comboDriverUri !== "undefined") {
            cboSelect2.driver(comboDriverUri);
        }
        if (typeof comboVehicleTeamUri !== "undefined") {
            cboSelect2.vehicleTeam(comboVehicleTeamUri);
        }
        if (typeof comboPartnerUri !== "undefined" && $('.select-partner').length > 0) {
            cboSelect2.partner(comboPartnerUri);
        }
    }

    if (typeof createDriverQuickSearch != "undefined") {
        var exceptIds = [];
        var quickSearch = createDriverQuickSearch();
        if (typeof exceptIds != "undefined") {
            var config = {};
            config.exceptIds = exceptIds;
            quickSearch(config).init();
        }
    }
    if (typeof createVehicleQuickSearch != "undefined") {
        var exceptIds = [];
        var quickSearch = createVehicleQuickSearch();
        if (typeof exceptIds != "undefined") {
            var config = {};
            config.exceptIds = exceptIds;
            quickSearch(config).init();
        }
    }

    if (typeof createVehicleTeamQuickSearch != "undefined") {
        var exceptIds = [];
        var quickSearch = createVehicleTeamQuickSearch();
        if (typeof exceptIds != "undefined") {
            var config = {};
            config.exceptIds = exceptIds;
            quickSearch(config).init();
        }
    }

    if (typeof createCustomerQuickSearch != "undefined") {
        var exceptIds = [];
        var quickSearch = createCustomerQuickSearch();
        if (typeof exceptIds != "undefined") {
            var config = {};
            config.exceptIds = exceptIds;
            quickSearch(config).init();
        }
    }

    $('#btnApply').on('click', function () {
        var data = createRequestData();

        sendRequest({
            url: reportUri,
            type: 'POST',
            data: data.data
        }, function (response) {
            if (!response) return;
            var results = generateReportTable(response.data);
            var start = $('#reportrange').data('daterangepicker').startDate.format('DD-MM-YYYY');
            var end = $('#reportrange').data('daterangepicker').endDate.format('DD-MM-YYYY');
            $('.row.title span.parameter').html(` (từ ${start} đến ${end})`);

            $('.card-box.result').css('display', 'block');

            if (results.length != 0) {
                Tool.serverData = results;
                var template = generateReportTemplate(results, data.clientData, response.summary);
                $('.report-content').html(template);
                $('.report-content').removeClass('hide');
                $('.empty-box').addClass('hide');
                $('#total-distance').html(formatNumber(response.distance));
                $('#total-distance-with-goods').html(formatNumber(response.distanceWithGoods));
                $('#total-distance-without-goods').html(formatNumber(response.distanceWithoutGoods));

            } else {
                $('.empty-box').removeClass('hide');
                $('.report-content').addClass('hide');
                $('#total-distance').html(0);
                $('#total-distance-with-goods').html(0);
                $('#total-distance-without-goods').html(0);
            }


        });
    });

    $('#btnDefault').on('click', function () {
        $('#vehicle_team_ids').empty().trigger('change');
        $('#vehicle_ids').empty().trigger('change');
        $('#driver_ids').empty().trigger('change');
        $('#reportrange span').html(moment().startOf('month').format('D MMMM, YYYY') + ' - ' + moment().endOf('month').format('D MMMM, YYYY'));
        $('#reportrange').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#reportrange').data('daterangepicker').setEndDate(moment().endOf('month'));
    });

    $('.sync-data').on('click', function (e) {
        e.preventDefault();
        var data = {};
        data.FromDate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        data.ToDate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        sendRequest({
            url: syncUri,
            type: 'POST',
            data: data
        }, function (response) {
            if (!response) return;

            if (response.error_code != "100") {
                toastr["error"](response.message);
            } else {
                toastr["success"]('Đồng bộ dữ liệu thành công');
            }
        });
    });

});

function createRequestData() {
    var data = {};
    data.VehicleTeamIDs = $('#vehicle_team_ids').select2('data').map(p => p.id).join(',');
    data.VehicleIDs = $('#vehicle_ids').select2('data').map(p => p.id).join(',');
    data.DriverIDs = $('#driver_ids').select2('data').map(p => p.id).join(',');
    data.FromDate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
    data.ToDate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    if ($('#partner_id').length > 0) {
        data.PartnerIds = $('#partner_id').select2('data').map(p => p.id).join(',');
    }

    return {
        data: data,
        clientData: data
    };
}

// Tạo dữ liệu báo cáo
// CreatedBy nlhoang 23/05/2019
function generateReportTable(response) {
    var results = [];
    if (!response) return [];
    response.forEach(item => {
        if (results.filter(p => p.id == item.vehicle_id).length > 0) {
            var result = results.find(p => p.id === item.vehicle_id);
            if (!result.hasOwnProperty([item.date])) {
                result[item.date] = {};
                var itemDate = result[item.date];
                itemDate.distance = item.distance;
                itemDate.distance_with_goods = item.distance_with_goods;
                itemDate.distance_without_goods = item.distance_without_goods;

            }

        } else {
            var result = {};
            result.key = item.reg_no;
            result.id = item.vehicle_id;
            result.driver_name = item.driver_name;
            result[item.date] = {};
            var itemDate = result[item.date];
            itemDate.distance = item.distance;
            itemDate.distance_with_goods = item.distance_with_goods;
            itemDate.distance_without_goods = item.distance_without_goods;

            results.push(result);
        }
    });
    return results;
}

function generateReportTemplate(results, data, summary) {
    var table = '<table class="table table-bordered">';
    var headerTemplate = `<thead>`;
    var header = results[0];
    headerTemplate += `<th style="width: 180px">Số xe</th>`;
    headerTemplate += `<th>Tài xế</th>`;
    headerTemplate += `<th style = "width: 120px">Tỷ lệ có hàng</th>`;

    for (var key in header) {
        if (key != 'id' && key !== 'key' && key != 'driver_name') {
            headerTemplate += `<th style = "width: 120px" > ${key} </th>`;
        }
    }
    headerTemplate += '</thead>';
    var bodyTemplate = `<tbody>`;

    results.forEach((result, index) => {
            bodyTemplate += `<tr class="parent" data-index="${index}">`;
            bodyTemplate += `<td ><span>${result.key}</span></td> `;
            bodyTemplate += `<td ><span>${result.driver_name == null ? '' : result.driver_name}</span></td> `;
            var childTemplate = '';

            var totalWithGoods = 0;
            var total = 0;
            for (var date in result) {
                if (date != 'id' && date != 'driver_name' && date != 'key') {
                    var distance = result[date].distance ? result[date].distance : 0;
                    var distance_with_goods = result[date].distance_with_goods ? result[date].distance_with_goods : 0;
                    var distance_without_goods = result[date].distance_without_goods ? result[date].distance_without_goods : 0;

                    total += distance;
                    totalWithGoods += distance_with_goods;

                    childTemplate += `<td class="text-right">`
                        + formatNumber(distance) + `<br/>`
                        + formatNumber(distance_with_goods) + `<br/>`
                        + formatNumber(distance_without_goods) + `<br/>` +
                        `</td>`;

                }
            }

            var ratio = total != 0 ? (totalWithGoods / total) * 100 : 0;
            bodyTemplate += `<td class="text-right">` + formatNumber(ratio.toFixed(2)) + `</td> `;
            bodyTemplate += childTemplate;
            bodyTemplate += `</tr>`;
        }
    );
    if (summary) {
        var summaryTemplate = '<tfoot><tr>';

        var childSummaryTemplate = '';
        for (var index in summary) {
            childSummaryTemplate += `<td class="text-right">`
                + formatNumber(summary[index].distance ? summary[index].distance : 0) + `<br/>`
                + formatNumber(summary[index].distance_with_goods ? summary[index].distance_with_goods : 0) + `<br/>`
                + formatNumber(summary[index].distance_without_goods ? summary[index].distance_without_goods : 0) + `<br/>` + `</td>`;
        }
        summaryTemplate += `<td><b>Tổng km<br/>Tổng km có hàng<br/>Tổng km không hàng</b></td>`;
        summaryTemplate += `<td></td>`;
        summaryTemplate += `<td></td>`;
        summaryTemplate += childSummaryTemplate;
        summaryTemplate += '</tr></tfoot>';

        bodyTemplate += summaryTemplate;
    }
    bodyTemplate += "</tbody>";

    table += headerTemplate + bodyTemplate;
    table += '</table>';
    return table;
}


function generateHeader(results, config, n) {
    var headerTemplate = `<row>`;
    var header = results[0];
    headerTemplate += n.generateCellString('Số xe');
    headerTemplate += n.generateCellString('Tài xế');

    for (var key in header) {
        if (key != 'id' && key !== 'key' && key != 'driver_name') {
            headerTemplate += n.generateCellString(key);
        }
    }
    headerTemplate += '</row>';
    return headerTemplate;
}

function generateRows(results, config, n) {
    var object = config.object,
        data = config.data,
        summary = config.summary;

    var bodyTemplate = ``;
    results.forEach((result, index) => {
        var childTemplate = '<row>';
        childTemplate += n.generateCellString(result.key);
        childTemplate += n.generateCellString(result.driver_name == null ? '' : result.driver_name);
        for (var date in result) {
            if (date != 'id' && date != 'driver_name' && date != 'key') {
                childTemplate += n.generateCellString(
                    formatNumber(result[date].distance ? result[date].distance : 0) + '('
                    + '' + formatNumber(result[date].distance_with_goods ? result[date].distance_with_goods : 0) + '/'
                    + '' + formatNumber(result[date].distance_without_goods ? result[date].distance_without_goods : 0) + ')');

            }
        }
        childTemplate += '</row>';
        bodyTemplate += childTemplate;

    });
    return bodyTemplate;
}
