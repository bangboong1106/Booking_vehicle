let me = this;
toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: false,
    progressBar: false,
    positionClass: "toast-top-center",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};
$(function () {
    if (typeof cboSelect2 !== "undefined") {
        if (typeof comboVehiclesUri !== "undefined") {
            cboSelect2.vehicle(comboVehiclesUri);
        }
        if (typeof comboVehicleTeamUri !== "undefined") {
            cboSelect2.vehicleTeam(comboVehicleTeamUri);
        }
        if (typeof comboPartnerUri !== 'undefined' && $('.select-partner').length > 0) {
            cboSelect2.partner(comboPartnerUri);
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

    $("#btnApply").on("click", function () {
        var data = createRequestData();

        sendRequest(
            {
                url: reportUri,
                type: "POST",
                data: data.data,
            },
            function (response) {
                try {
                    if (!response) return;
                    var results = generateReportTable(response.data);
                    var start = $("#reportrange")
                        .data("daterangepicker")
                        .startDate.format("DD-MM-YYYY");
                    var end = $("#reportrange")
                        .data("daterangepicker")
                        .endDate.format("DD-MM-YYYY");
                    $(".row.title span.parameter").html(` (t??? ${start} ?????n ${end})`);

                    $(".card-box.result").css("display", "block");

                    if (results.length != 0) {
                        Tool.serverData = results;

                        var template = generateReportTemplate(
                            results,
                            data.clientData,
                            response.summary
                        );
                        $(".report-content").html(template);
                        $(".report-content").html(template);
                        $(".report-content").removeClass("hide");
                        $(".empty-box").addClass("hide");
                    } else {
                        $(".empty-box").removeClass("hide");
                        $(".report-content").addClass("hide");
                    }
                } catch (ex) {
                    console.log(ex);
                    toastr["error"](
                        "C?? l???i x???y ra khi l???y th??ng tin. Vui l??ng th??? l???i sau"
                    );
                }
            }
        );
    });

    $("#btnDefault").on("click", function () {
        $("#vehicle_team_ids").empty().trigger("change");
        $("#vehicle_ids").empty().trigger("change");
        $("#reportrange span").html(
            moment().startOf("month").format("D MMMM, YYYY") +
            " - " +
            moment().endOf("month").format("D MMMM, YYYY")
        );
        $("#reportrange")
            .data("daterangepicker")
            .setStartDate(moment().startOf("month"));
        $("#reportrange")
            .data("daterangepicker")
            .setEndDate(moment().endOf("month"));
    });

    $("input[name='displayType']").change(function () {
        var displayType = $('.display-type').find('input[type=radio]:checked').val();
        if (displayType == 1) {
            $('#report-title').html("B??o c??o n??ng su???t xe")
        } else
            $('#report-title').html("Ch???t l?????ng d???ch v??? xe")
    });
});

function createRequestData() {
    var data = {};
    data.VehicleTeamIDs = $("#vehicle_team_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",");
    data.VehicleIDs = $("#vehicle_ids")
        .select2("data")
        .map((p) => p.id)
        .join(",");
    data.FromDate = $("#reportrange")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
    data.ToDate = $("#reportrange")
        .data("daterangepicker")
        .endDate.format("YYYY-MM-DD");
    data.DayCondition = $("#dayCondition").val();

    if ($('#partner_id').length > 0) {
        data.PartnerIDs = $("#partner_id")
        .select2("data")
        .map((p) => p.id)
        .join(",");
    }

    data.DisplayType = $('.display-type').find('input[type=radio]:checked').val();
    return {
        data: data,
        clientData: data,
    };
}

// T???o d??? li???u b??o c??o
// CreatedBy nlhoang 13/07/2020
function generateReportTable(response) {
    return response;
}

var columnPerformances = [
    "Bi???n s???",
    "T??i x???",
    "T???ng km",
    "S??? km trung b??nh",
    "T???ng s??? ????n",
    "T???ng s??? chuy???n",
    "Doanh thu",
    "T???ng chi ph??",
    "T???ng hoa h???ng",
    "T???ng COD",
    "L???i nhu???n",
    "T??? su???t l???i nhu???n (L???i nhu???n/Doanh thu)",
];
var columnQualitys = [
    "Bi???n s???",
    "T??i x???",
    "T???ng s??? ????n",
    "T???ng s??? chuy???n",
    "S??? chuy???n trung b??nh/ng??y",
    "S??? ????n ????ng gi???",
    "S??? ????n mu???n gi???",
    "T??? tr???ng (????ng gi???/T???ng s??? ????n ho??n th??nh)",
];

function generateReportTemplate(results, data, summary) {
    var displayType = $('.display-type').find('input[type=radio]:checked').val();

    var table = '<table class="table table-bordered">';
    var headerTemplate = `<thead>`;

    this.columns = columnPerformances;
    if (displayType == 2) {
        this.columns = columnQualitys;
    }

    this.columns.map((column) => {
        headerTemplate += `<th >${column}</th>`;
    });

    headerTemplate += "</thead>";
    var bodyTemplate = `<tbody>`;

    results.forEach((result, index) => {
        bodyTemplate += `<tr class="parent" data-index="${index}">`;

        bodyTemplate += `<td ><span>${
            result.reg_no == null ? "" : result.reg_no
            }</span></td> `;
        bodyTemplate += `<td><span>${
            result.driver_names == null ? "" : result.driver_names
            }</span></td> `;

        if (displayType == 2) {
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_order == null ? 0 : result.total_order
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_route == null ? 0 : result.total_route
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_route_average_per_day == null ? 0 : result.total_route_average_per_day
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${
                result.total_order_on_time == null ? "" : result.total_order_on_time
                }</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_order_late == null ? 0 : result.total_order_late
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.ratio_order == null ? 0 : result.ratio_order
            )}</span></td> `;
        } else {
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.distance == null ? 0 : result.distance
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.distance_average_per_day == null ? 0 : result.distance_average_per_day
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_order == null ? 0 : result.total_order
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${
                result.total_route == null ? "" : result.total_route
                }</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_amount == null ? 0 : result.total_amount
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_cost == null ? 0 : result.total_cost
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_commission == null ? 0 : result.total_commission
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.total_cod == null ? 0 : result.total_cod
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.revenue == null ? 0 : result.revenue
            )}</span></td> `;
            bodyTemplate += `<td class="text-right"><span>${formatNumber(
                result.ratio_revenue == null ? 0 : result.ratio_revenue
            )}</span></td> `;
        }
        bodyTemplate += `</tr>`;
    });
    bodyTemplate += "</tbody>";

    table += headerTemplate + bodyTemplate;
    table += "</table>";
    return table;
}

function generateHeader(results, config, n) {
    var displayType = $('.display-type').find('input[type=radio]:checked').val();
    var headerTemplate = `<row>`;
    this.columns = columnPerformances;
    if (displayType == 2) {
        this.columns = columnQualitys;
    }
    this.columns.map((column) => {
        headerTemplate += n.generateCellString(column);
    });
    headerTemplate += "</row>";
    return headerTemplate;
}

function generateRows(results, config, n) {
    var displayType = $('.display-type').find('input[type=radio]:checked').val();

    var bodyTemplate = ``;
    results.forEach((result, index) => {
        var childTemplate = "<row>";
        childTemplate += n.generateCellString(
            result.reg_no == null ? "" : result.reg_no
        );
        childTemplate += n.generateCellString(
            result.driver_names == null ? "" : result.driver_names
        );
        if (displayType == 2) {
            childTemplate += n.generateCellNumber(
                result.total_order == null ? 0 : result.total_order
            );
            childTemplate += n.generateCellNumber(
                result.total_route == null ? 0 : result.total_route
            );
            childTemplate += n.generateCellNumber(
                result.total_route_average_per_day == null ? 0 : result.total_route_average_per_day
            );
            childTemplate += n.generateCellNumber(
                result.total_order_on_time == null ? 0 : result.total_order_on_time
            );
            childTemplate += n.generateCellNumber(
                result.total_order_late == null ? 0 : result.total_order_late
            );
            childTemplate += n.generateCellNumber(
                result.ratio_order == null ? 0 : result.ratio_order
            );
        } else {
            childTemplate += n.generateCellNumber(
                result.distance == null ? 0 : result.distance
            );
            childTemplate += n.generateCellNumber(
                result.distance_average_per_day == null ? 0 : result.distance_average_per_day
            );
            childTemplate += n.generateCellNumber(
                result.total_order == null ? 0 : result.total_order
            );
            childTemplate += n.generateCellNumber(
                result.total_route == null ? 0 : result.total_route
            );
            childTemplate += n.generateCellNumber(
                result.total_amount == null ? 0 : result.total_amount
            );
            childTemplate += n.generateCellNumber(
                result.total_cost == null ? 0 : result.total_cost
            );
            childTemplate += n.generateCellNumber(
                result.total_commission == null ? 0 : result.total_commission
            );
            childTemplate += n.generateCellNumber(
                result.total_cod == null ? 0 : result.total_cod
            );
            childTemplate += n.generateCellNumber(
                result.revenue == null ? 0 : result.revenue
            );
            childTemplate += n.generateCellNumber(
                result.ratio_revenue == null ? 0 : result.ratio_revenue
            );
        }

        childTemplate += "</row>";
        bodyTemplate += childTemplate;
    });
    return bodyTemplate;
}
