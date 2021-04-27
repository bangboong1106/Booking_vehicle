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

    createFormula();

    deleteFormula();
});

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

    var $trNew = $trLast.clone();
    $trNew.find(".select-location-group").removeAttr("data-select2-id");
    $trNew.find(".select-location-group option").removeAttr("data-select2-id");
    $trNew.find(".select-vehicle-group").removeAttr("data-select2-id");
    $trNew.find(".select-vehicle-group option").removeAttr("data-select2-id");

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
    $trLast.find(".select-vehicle-group").select2();


    var newLocationDes = $trNew.find(".select-location-group.destination");
    newLocationDes.select2(options);
    var newLocationArl = $trNew.find(".select-location-group.arrival");
    newLocationArl.select2(options);
    var vehicleGroup = $trNew.find(".select-vehicle-group");
    vehicleGroup.select2();

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
            .find(".select-vehicle-group")
            .val(entity.vehicle_group_id)
            .trigger("change");
        $trNew.find(".number-input").val(formatNumber(entity.amount));
    } else {
        newLocationDes.empty();
        newLocationDes.val("").trigger("change");
        newLocationArl.empty();
        newLocationArl.val("").trigger("change");

        $trNew.find(".select-vehicle-group").val("").trigger("change");

        $trNew.find(".number-input").val(0);
    }

    var rowNumber = $(".table-formula").find(".row-number");
    rowNumber.html($(".table-formula").find("tbody tr").length);
}

