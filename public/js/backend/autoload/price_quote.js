let map,
    markers = [],
    lat = 21.0464404,
    lng = 105.7936427,
    bounds;
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
        if (typeof comboLocationGroupUri !== "undefined") {
            cboSelect2.locationGroup(comboLocationGroupUri, null);
        }
    }

    changeIsDefault();

    changeIsApplyAll();

    changeType();

    changeOperator();

    createFormula();

    deleteFormula();

    createPointCharge();

    deletePointCharge();

    changeIsDistance();

    setDateRange();
});

function changeType() {
    $("#type").change(function () {
        var text = $("#type option:selected").text();
        $(".group_type").text(text);
        var val = $(this).val();

        $(".condition-group .condition").hide();
        var $condition = $(".condition-group .formula_" + val);
        $condition.show();
        $(".select-operator").val("equal").trigger("change");

        if (val == 1) {
            $(".condition-vehicle-group").show();
            $(".select-vehicle-group").prop("disabled", false);
            $(".select-operator").prop("disabled", true);
            $(".condition-goods-type").hide();
        } else if (val == 4) {
            $(".condition-vehicle-group").hide();
            $(".select-operator").prop("disabled", true);
            $(".condition-goods-type").show();
            $(".select-goods-type").prop("disabled", false);
        } else {
            $(".condition-vehicle-group").hide();
            $(".select-operator").prop("disabled", false);
            $(".condition-goods-type").hide();
        }
    });
}

function changeIsDefault() {
    $("#switchery_price_quote_default").on("change", function () {
        var checked = $("#switchery_price_quote_default").is(":checked");
        if (checked || $("#switchery_price_quote_default").length == 0) {
            $("#isDefault").val("1");
        } else {
            $("#isDefault").val("0");
        }
    });
}

function changeIsApplyAll() {
    $("#switchery_customer_apply_all").on("change", function () {
        var checked = $("#switchery_customer_apply_all").is(":checked");
        if (checked || $("#switchery_customer_apply_all").length == 0) {
            $("#isApplyAll").val("1");
            $(".listCustomerGroup").hide();
        } else {
            $("#isApplyAll").val("0");
            $(".listCustomerGroup").show();
        }
    });
}

function changeIsDistance() {
    $("#switchery_distance").on("change", function () {
        var checked = $("#switchery_distance").is(":checked");
        if (checked || $("#switchery_distance").length == 0) {
            $("#isDistance").val("1");
            $(".select-location-group").prop("disabled", true);
            $(".is-distance").hide();
        } else {
            $("#isDistance").val("0");
            $(".select-location-group").prop("disabled", false);
            $(".is-distance").show();

        }
    });
}

function changeOperator() {
    $(document).on("change", ".select-operator", function (e) {
        var val = $(this).val();
        var type = $("#type").val();
        var element = $(this)
            .parents("tr")
            .find('input[name*="_to"].formula_' + type);

        if (val == "in") {
            element.show();
        } else {
            element.hide();
        }
    });
}

function createFormula() {
    // Thêm mới 1 dòng công thức
    $(document).on("click", "#btn-add-formula", function (e) {
        e.preventDefault();
        e.stopPropagation();
        generateFormulaItem(void 0);
    });
}

function deleteFormula() {
    // Xóa dòng bảng chi phí
    $(document).on("click", ".delete-formula", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //Neu xoa het thi add them 1 item
        var count = $(".table-formula #body_content").find("tr").length;
        if (count <= 1) {
            generateFormulaItem(void 0);
        }
        $(this).parent("td").parent("tr:first").remove();
        var rowNumber = $(".table-formula").find(".row-number");
        rowNumber.html($(".table-formula").find("tbody tr").length);
    });
}

function createPointCharge() {
    // Thêm mới 1 dòng công thức
    $(document).on("click", "#btn-add-point-charge", function (e) {
        e.preventDefault();
        e.stopPropagation();
        generatePointChargeItem(void 0);
    });
}

function deletePointCharge() {
    // Xóa dòng bảng chi phí
    $(document).on("click", ".delete-point-charge", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //Neu xoa het thi add them 1 item
        var count = $(".table-point-charge #body_content").find("tr").length;
        if (count <= 1) {
            generatePointChargeItem(void 0);
        }
        $(this).parent("td").parent("tr:first").remove();
        var rowNumber = $(".table-point-charge").find(".row-number");
        rowNumber.html($(".table-point-charge").find("tbody tr").length);
    });
}

// Xử lý thêm công thức
function generateFormulaItem(entity) {
    var $tableBody = $(".table-formula").find("tbody"),
        $trLast = $tableBody.find("tr:last");

    if ($trLast.find(".select-location-group").data("select2")) {
        $trLast.find(".select-location-group").select2("destroy");
    }
    if ($trLast.find(".select-vehicle-group").data("select2")) {
        $trLast.find(".select-vehicle-group").select2("destroy");
    }
    if ($trLast.find(".select-goods-type").data("select2")) {
        $trLast.find(".select-goods-type").select2("destroy");
    }
    if ($trLast.find(".select-operator").data("select2")) {
        $trLast.find(".select-operator").select2("destroy");
    }

    var $trNew = $trLast.clone();
    $trNew.find(".select-location-group").removeAttr("data-select2-id");
    $trNew.find(".select-location-group option").removeAttr("data-select2-id");
    $trNew.find(".select-vehicle-group").removeAttr("data-select2-id");
    $trNew.find(".select-vehicle-group option").removeAttr("data-select2-id");
    $trNew.find(".select-goods-type").removeAttr("data-select2-id");
    $trNew.find(".select-goods-type option").removeAttr("data-select2-id");
    $trNew.find(".select-operator").removeAttr("data-select2-id");
    $trNew.find(".select-operator option").removeAttr("data-select2-id");

    $trNew.find("td").each(function () {
        var el = $(this)
            .find(".formula")
            .each(function (idx, element) {
                var id = $(element).attr("id") || null;
                if (id) {
                    var tmp = id.split("][");
                    var index = Number(tmp[0].split("[")[1]) + 1;
                    var prop = tmp[1].substring(0, tmp[1].length - 1);
                    var name = "formulas[" + index + "][" + prop + "]";
                    $(element).attr("id", name);
                    $(element).attr("name", name);
                }
            });
    });
    $trLast.after($trNew);
    var options = {
        allowClear: true,
        ajax: {
            url: comboLocationGroupUri,
            dataType: "json",
            delay: 200,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                };
            },
            processResults: function (data) {
                data.page = data.page || 1;
                return {
                    results: data.items.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            title: item.title,
                            code: item.code,
                        };
                    }),
                    pagination: {
                        more: data.pagination,
                    },
                };
            },
        },
        placeholder: "Vui lòng chọn nhóm địa điểm",
        minimumInputLength: 0,
        templateResult: function (repo) {
            if (repo.loading) {
                return $(
                    '<div class="row">' +
                    '<div class="col-md-6">Mã</div>' +
                    '<div class="col-md-6">Tên</div>' +
                    "</div>"
                );
            }

            return $(
                '<div class="row">' +
                '<div class="col-md-6">' +
                repo.code +
                "</div>" +
                '<div class="col-md-6">' +
                (repo.title ? repo.title : "") +
                "</div>" +
                "</div>"
            );
        },
        templateSelection: function (repo) {
            if (repo.id === "") {
                // adjust for custom placeholder values
                return "Vui lòng chọn nhóm địa điểm";
            }
            return repo.title;
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        language: "vi",
        maximumSelectionSize: 1,
        tags: true,
        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === "") {
                return null;
            }
            return {
                id: term,
                text: term,
                title: term,
                code: "",
                newTag: true, // add additional parameters
            };
        },
    };

    $trLast.find(".select-location-group").select2(options);
    var isHide = $trLast.find(".select-vehicle-group").css("display") === "none";

    $trLast.find(".select-vehicle-group").select2();
    if (isHide) {
        $trLast.find(".select-vehicle-group").next(".select2-container").hide();
    } else {
        $trLast.find(".select-vehicle-group").next(".select2-container").show();
    }

    var isHideGoodsType = $trLast.find(".select-goods-type").css("display") === "none";
    $trLast.find(".select-goods-type").select2();
    if (isHideGoodsType) {
        $trLast.find(".select-goods-type").next(".select2-container").hide();
    } else {
        $trLast.find(".select-goods-type").next(".select2-container").show();
    }

    $trLast.find(".select-operator").select2();


    var newLocationDes = $trNew.find(".select-location-group.destination");
    newLocationDes.select2(options);
    var newLocationArl = $trNew.find(".select-location-group.arrival");
    newLocationArl.select2(options);

    var vehicleGroup = $trNew.find(".select-vehicle-group");
    vehicleGroup.select2();
    if (isHide) {
        vehicleGroup.next(".select2-container").hide();
    } else {
        vehicleGroup.next(".select2-container").show();
    }

    var goodsType = $trNew.find(".select-goods-type");
    goodsType.select2();
    if (isHideGoodsType) {
        goodsType.next(".select2-container").hide();
    } else {
        goodsType.next(".select2-container").show();
    }

    var operator = $trNew.find(".select-operator");
    operator.select2();

    $trNew
        .find(".number-input")
        .toArray()
        .forEach(function (el) {
            new Cleave(el, {
                numeral: true,
                numeralDecimalMark: ",",
                delimiter: ".",
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: "thousand",
            });
        });
    if (entity) {
        $(newLocationDes).data("select2").dataAdapter.select({
            id: entity.location_group_destination_id,
            text: entity.location_group_destination_title,
            title: entity.location_group_destination_title,
            code: entity.location_group_destination_code,
        });
        $(newLocationDes).on("change", function (e) {
            if (e.currentTarget.value == "") {
                $(e.currentTarget).select2("clear");
            }
        });
        $(newLocationArl).data("select2").dataAdapter.select({
            id: entity.location_group_arrival_id,
            text: entity.location_group_arrival_title,
            title: entity.location_group_arrival_title,
            code: entity.location_group_arrival_code,
        });
        $(newLocationArl).on("change", function (e) {
            if (e.currentTarget.value == "") {
                $(e.currentTarget).select2("clear");
            }
        });

        $trNew
            .find(".select-goods-type")
            .val(entity.goods_type_id)
            .trigger("change");
        $trNew
            .find(".select-vehicle-group")
            .val(entity.vehicle_group_id)
            .trigger("change");
        $trNew.find(".number-input").val(formatNumber(entity.amount));
    } else {
        newLocationDes.empty();
        newLocationDes.val("").trigger("change");
        newLocationArl.empty();
        newLocationArl.val("").trigger("change");

        $trNew.find(".select-goods-type").val("").trigger("change");
        $trNew.find(".select-vehicle-group").val("").trigger("change");
        $trNew.find(".select-operator").val("equal").trigger("change");

        $trNew.find(".number-input").val(0);
    }

    var rowNumber = $(".table-formula").find(".row-number");
    rowNumber.html($(".table-formula").find("tbody tr").length);
}

// Xử lý thêm phí rớt điểm
function generatePointChargeItem(entity) {
    var $tableBody = $(".table-point-charge").find("tbody"),
        $trLast = $tableBody.find("tr:last");

    if ($trLast.find(".select-vehicle-group").data("select2")) {
        $trLast.find(".select-vehicle-group").select2("destroy");
    }
    if ($trLast.find(".select-goods-type").data("select2")) {
        $trLast.find(".select-goods-type").select2("destroy");
    }
    if ($trLast.find(".select-operator").data("select2")) {
        $trLast.find(".select-operator").select2("destroy");
    }
    var $trNew = $trLast.clone();
    $trNew.find(".select-vehicle-group").removeAttr("data-select2-id");
    $trNew.find(".select-vehicle-group option").removeAttr("data-select2-id");
    $trNew.find(".select-goods-type").removeAttr("data-select2-id");
    $trNew.find(".select-goods-type option").removeAttr("data-select2-id");
    $trNew.find(".select-operator").removeAttr("data-select2-id");
    $trNew.find(".select-operator option").removeAttr("data-select2-id");

    $trNew.find("td").each(function () {
        var el = $(this)
            .find(".formula")
            .each(function (idx, element) {
                var id = $(element).attr("id") || null;
                if (id) {
                    var tmp = id.split("][");
                    var index = Number(tmp[0].split("[")[1]) + 1;
                    var prop = tmp[1].substring(0, tmp[1].length - 1);
                    var name = "pointCharges[" + index + "][" + prop + "]";
                    $(element).attr("id", name);
                    $(element).attr("name", name);
                }
            });
    });
    $trLast.after($trNew);

    var isHide = $trLast.find(".select-vehicle-group").css("display") === "none";
    $trLast.find(".select-vehicle-group").select2();
    if (isHide) {
        $trLast.find(".select-vehicle-group").next(".select2-container").hide();
    } else {
        $trLast.find(".select-vehicle-group").next(".select2-container").show();
    }

    var isHideGoodsType = $trLast.find(".select-goods-type").css("display") === "none";
    $trLast.find(".select-goods-type").select2();
    if (isHideGoodsType) {
        $trLast.find(".select-goods-type").next(".select2-container").hide();
    } else {
        $trLast.find(".select-goods-type").next(".select2-container").show();
    }

    $trLast.find(".select-operator").select2();


    var vehicleGroup = $trNew.find(".select-vehicle-group");
    vehicleGroup.select2();
    if (isHide) {
        vehicleGroup.next(".select2-container").hide();
    } else {
        vehicleGroup.next(".select2-container").show();
    }

    var goodsType = $trNew.find(".select-goods-type");
    goodsType.select2();
    if (isHideGoodsType) {
        goodsType.next(".select2-container").hide();
    } else {
        goodsType.next(".select2-container").show();
    }

    var operator = $trNew.find(".select-operator");

    operator.select2();
    $trNew
        .find(".number-input")
        .toArray()
        .forEach(function (el) {
            new Cleave(el, {
                numeral: true,
                numeralDecimalMark: ",",
                delimiter: ".",
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: "thousand",
            });
        });
    if (entity) {
        $trNew
            .find(".select-goods-type")
            .val(entity.goods_type_id)
            .trigger("change");
        $trNew
            .find(".select-vehicle-group")
            .val(entity.vehicle_group_id)
            .trigger("change");
        $trNew.find(".number-input").val(formatNumber(entity.amount));
    } else {
        $trNew.find(".select-goods-type").val("").trigger("change");
        $trNew.find(".select-vehicle-group").val("").trigger("change");
        $trNew.find(".select-operator").val("equal").trigger("change");

        $trNew.find(".number-input").val(0);
    }

    var rowNumber = $(".table-point-charge").find(".row-number");
    rowNumber.html($(".table-point-charge").find("tbody tr").length);
}

// Thiết lập dữ liệu tính giá tự động
// CreatedBy nlhoang 03/08/2020
function setDateRange() {

    let config = {
        format: 'DD/MM/YYYY',
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        dateLimit: {
            months: 36
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Hôm nay': [moment(), moment()],
            'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 ngày trước': [moment().subtract(6, 'days'), moment()],
            '30 ngày trước': [moment().subtract(29, 'days'), moment()],
            'Tuần này': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
            'Tuần trước': [moment().subtract(7, 'days').startOf('isoWeek'), moment().subtract(7, 'days').endOf('isoWeek')],
            'Tháng này': [moment().startOf('month'), moment().endOf('month')],
            'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'left',
        drops: 'down',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-secondary',
        separator: ' to ',
        locale: {
            applyLabel: 'Chọn',
            cancelLabel: 'Hủy',
            fromLabel: 'Từ',
            toLabel: 'đến',
            customRangeLabel: 'Tùy chọn',
            daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
            monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5 ', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            firstDay: 1
        }
    };


    var text = moment().startOf('month').locale('vi').format('D MMMM, YYYY') + ' - ' + moment().endOf('month').locale('vi').format('D MMMM, YYYY');
    $('#price-date-range span').html(text);

    if ($('#price-date-range').length > 0) {
        $('#price-date-range').daterangepicker(config, function (start, end, label) {
            $('#price-date-range span').html(start.locale('vi').format('D MMMM, YYYY') + ' - ' + end.locale('vi').format('D MMMM, YYYY'));
        });
    }

    $('#btn-price').on('click', function () {
        var url = $(this).data('url');
        var data = {};
        data.day_condition = $('#dayCondition').val();
        data.from_date = $('#price-date-range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        data.to_date = $('#price-date-range').data('daterangepicker').endDate.format('YYYY-MM-DD');
        sendRequest({
            url: url,
            type: 'POST',
            data: data
        }, function (response) {
            if (response.errorCode == -1) {
                toastr["error"](esponse.message);
            } else {
                $('#price_modal').modal('hide');
                toastr["success"]("Tính giá tự động thành công. Vui lòng kiểm tra dữ liệu trong chức năng Danh sách giá đơn hàng");
            }
        })

    });
}