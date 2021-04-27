let map, markers = [], lat = 21.0464404, lng = 105.7936427,
    bounds;
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
$(function () {
    let selectCustomer = $('#customer_id_order_customer'),
        inputCustomerName = $('#customer_name'),
        inputCustomerMobileNo = $('#customer_mobile_no');

    selectCustomer.on("select2:select select2:clear", function (e) {
        let data = $(this).select2('data')[0];
        if (data && data.id !== '') {
            inputCustomerName.parent().parent().removeClass('hide');
            inputCustomerMobileNo.parent().parent().removeClass('hide');
            var customerName = data.delegate,
                customerMobileNo = data.mobile_no;
            inputCustomerName.val(customerName);
            inputCustomerMobileNo.val(customerMobileNo);
        } else {
            inputCustomerName.parent().parent().addClass('hide');
            inputCustomerMobileNo.parent().parent().addClass('hide');
            inputCustomerName.val('');
            inputCustomerMobileNo.val('');
        }

    });

    if (typeof cboSelect2 !== "undefined") {
        if (typeof urlLocation !== "undefined") {
            cboSelect2.location(urlLocation, '.select-location', true, false);
        }
        if (typeof comboCustomerUri !== "undefined") {
            cboSelect2.customer(comboCustomerUri, null);
        }
    }


    if (typeof createGoodsQuickSearch != "undefined") {
        var quickSearch = createGoodsQuickSearch();
        var searchGoodsExceptIds = [];
        if (typeof searchGoodsExceptIds != "undefined") {
            var config = {};
            config.searchCallback = (selectedData, datas) => {
                goodsSearchCallback(selectedData, datas);
            };
            config.exceptIds = searchGoodsExceptIds;
            quickSearch(config).init();
        }
    }

    selectCustomer.on("select2:select", function (e) {
        $("#goods_owner_id").val($(this).val());
        $('.select-location').prop('disabled', false);
        initLocationQuickSearch();
        if (is_create) {
            $('.select-location').val(null).trigger('change');
            c_id = e.params.data.id;
            let urlLocationWithId = urlLocation + '?c_id=' + c_id;
            cboSelect2.location(urlLocationWithId, $(".select-location"), true, false, 'Vui lòng chọn địa điểm');
            
            $('.delete-goods').trigger('click');
        }
    });

    if ((typeof is_create != "undefined" && typeof urlLocation != "undefined" && !is_create) || selectCustomer.val() > 0) {
        $('.select-location').prop('disabled', false);
        c_id = selectCustomer.val();
        let urlLocationWithId = urlLocation + '?c_id=' + c_id;
        cboSelect2.location(urlLocationWithId, $(".select-location"), true, false, 'Vui lòng chọn địa điểm');
        initLocationQuickSearch();
    }

    // validate lai form khi thay đổi ngày nhận hàng
    $('#ETD_date, #ETA_date').on('dp.change', function () {
        let form = $('form').validate();
        form.resetForm();
        form.form();
    });

    exportUpdateData();

    viewHistory();

    registerAddLocation();

    registerAddModal(selectCustomer, inputCustomerName, inputCustomerMobileNo);

    clickVehicleGroupInfo();

    registerShowOrderClient();

    changeVAT();

    calculateInGoods();
    deleteGoods();

    registerGetETA();

    registerUpdateRevenue();
});

function registerAddModal(selectCustomer, inputCustomerName, inputCustomerMobileNo) {
    let addCompleteModal = $('#add_complete');
    addCompleteModal.on('hide.bs.modal', function (e) {
        let entity = addCompleteModal.data('entity'),
            model = addCompleteModal.data('model'),
            button = addCompleteModal.data('button');

        switch (model) {
            case 'customer':
                addCustomerComplete(entity);
                break;
            case 'location':
                addLocationComplete(entity, button);
                break;
            default:
                return;
        }
    });

    function addCustomerComplete(entity) {
        let fullName = entity.type === '1' ? entity.delegate : entity.full_name;

        let newOption = '<option value="' + entity.id + '" selected="selected" ' +
            'data-customer="' + fullName +
            '" data-phone="' + entity.mobile_no +
            '" title="' + entity.full_name +
            '">' + entity.full_name + '</option>';
        selectCustomer.append(newOption).trigger('change');

        inputCustomerName.parent().parent().removeClass('hide');
        inputCustomerMobileNo.parent().parent().removeClass('hide');

        inputCustomerName.val(fullName);
        inputCustomerMobileNo.val(entity.mobile_no);
    }

    function addLocationComplete(entity, button) {
        let locationSelect = button.closest('.input-group').find('.select-location');

        locationSelect.empty().append('<option value="' + entity.id + '" title="' + entity.title + '">'
            + entity.title + '</option>').val(entity.id).trigger('change');

        if ($(button).closest('.location-order-destination').length > 0) {
            $('#hdfDestinationLocationId').val(entity.id);
        } else {
            $('#hdfArrivalLocationId').val(entity.id);
        }
    }
}

function viewHistory() {
    $(document).on('click', '.order-history', function () {
        $('#order-history-label').html('Lịch sử của đơn hàng ' + $(this).data('name'));
        var order_id = $(this).data('id');
        sendRequest({
            url: orderHistoryUrl,
            type: 'GET',
            data: {
                'order_id': order_id
            }
        }, function (response) {
            if (!response.ok) {
                return showErrorFlash(response.message);
            } else {
                $('#content_order_history').html('').append(response.data.content);
                $('#order-history').modal();
            }
        });
    });
}


function addCompletedLoadingModel(model) {
    if (model === 'customer') {
        Customer('#modal_add');
    }
    if (model === 'loaction') {
        Customer('#modal_add');
    }
}

// Xuất dữ liệu
// CreatedBy nlhoang
function exportUpdateData() {
    $('.parameter input[type=radio]').on('change', function () {
        if ($(this).get(0).checked) {
            if ($(this).val() == 5) {
                $('.custom-parameter').addClass('show');
                $('.custom-parameter').removeClass('hide');

            } else {
                $('.custom-parameter').removeClass('show');
                $('.custom-parameter').addClass('hide');
            }
        }
    });
}

// Format định số
// CreatedBy nlhoang 30/10/2019
function convertFormatNumber(val) {
    var result = val;
    if (typeof val === 'string') {
        result = parseFloat(val.replace(/\./g, "").replace(/,/g, '.'));
        if (Number.isNaN(result)) {
            result = 0;
        }
    }
    return result;
}

// Đăng ký sự kiện xử lý nút thêm mới địa điểm
function registerAddLocation() {
    let indexDestination = $('.location-order-destination').find('.location-item').length,
        indexArrival = $('.location-order-arrival').find('.location-item').length;

    $(document).on('click', '#arrival-plus-button, #destination-plus-button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let btn = $(this),
            cardBody = btn.parents('.card-body'),
            locationOrder = cardBody.find('.location-order'),
            locationItem = btn.closest('.add-block').find('.location-item-default').clone(),
            type, index;

        if (locationItem.hasClass('location-destination')) {
            type = 'locationDestinations';
            index = indexDestination;
            indexDestination++;
        } else {
            type = 'locationArrivals';
            index = indexArrival;
            indexArrival++;
        }
        locationItem.removeClass('location-item-default').removeClass('hide');
        locationItem.find('.form-control[data-field]').each(function () {
            let input = $(this),
                field = input.data('field');
            input.attr('name', type + '[' + index + '][' + field + ']');
        });
        locationOrder.append(locationItem);
        let selectLocation = locationItem.find('.select-location-add');
        cboSelect2.location(urlLocation, selectLocation, true);
        locationItem.find('.timepicker').datetimepicker({
            format: 'HH:mm',
            locale: 'vi'
        });
        locationItem.find('.datepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            locale: 'vi',
            useCurrent: false,
        });
    });
    $(document).on('click', '.delete-location', function (e) {
        $(this).closest('.location-item').remove();
    });
}

//Xử lý trên bảng danh sách nhóm xe
//CreatedBy nlhoang 17/04/2020
function clickVehicleGroupInfo() {
    var index = 0;
    $(document).on('click', '#btn-add-vehicle-group', function (e) {
        e.preventDefault();
        let btn = $(this)
            , $tableBody = btn.closest('.content-body').find('#table-vehicle-group-info')
            , $trDefault = $tableBody.find(".vehicle-group-info-default")
            , $trNew = $trDefault.clone();

        index++;
        $trNew.find('.vgi-item').each(function () {
            let input = $(this)
                , fieldName = input.data('field');
            input.attr('name', 'listVehicleGroup[' + index + '][' + fieldName + ']');
            input.attr('id', 'listVehicleGroup[' + index + '][' + fieldName + ']');

        });

        $trNew.find('.combo').select2();
        $trNew.find('.number-input').toArray().forEach(function (el) {
            new Cleave(el, {
                numeral: true,
                numeralDecimalMark: ',',
                delimiter: '.',
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: 'thousand'
            });
        });

        $trNew.removeClass('d-none').removeClass('vehicle-group-info-default').addClass('vehicle-group-item');
        $tableBody.append($trNew);
    });

    $(document).on('click', '#table-vehicle-group-info .fa.fa-trash', function (e) {
        $(this).closest('.vehicle-group-item').remove();
    });
}

// Đăng ký hiển thị danh sách DH khách hàng từ client
function registerShowOrderClient() {
    $(document).on('click', '#order_client_btn', function (e) {
        e.preventDefault();
        let modal = $('#order_client_modal'),
            contentContainer = modal.find('#order_client_content');
        getListOrderClient(urlOrderClient, contentContainer, modal);
    });

    $(document).on('click', '#order_client_modal .fa.fa-check', function (e) {
        e.preventDefault();
        let modal = $('#order_client_modal'),
            $tr = $(this).parents('tr'),
            id = $tr.data('id');
        sendRequest({
            url: urlOrderApprove,
            type: 'POST',
            data: {
                'id': id ? id : 0
            },
        }, function (response) {
            if (response.errorCode != 0) {
                toastr["error"](response.errorMessage);
            } else {
                toastr["success"]('Cập nhật trạng thái thành công');
                $tr.remove();
                oneLogGrid._ajaxSearch($('.list-ajax'));
            }
        });
    });
}

function getListOrderClient(url, contentContainer, element) {
    let data = {};
    if (element.hasClass('page-link')) {
        data._s('page', element.data('page'));
    }

    sendRequest({
        url: url,
        type: 'GET',
        data: data,
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        }
        contentContainer.html(response.data.content);
        contentContainer.find('a.page-link').on('click', function (e) {
            e.preventDefault();
            let link = $(this);
            getListOrderClient(url, contentContainer, link);
        });
        contentContainer.find('a.sorting').on('click', function (e) {
            e.preventDefault();
            let sorting = $(this),
                link = sorting.attr('href');
            getListOrderClient(link, contentContainer, sorting);
        });
        if (element.hasClass('modal')) {
            element.modal('show');
        }
    })
}

function changeVAT() {
    $("#switchery_vat_default").on("change", function () {
        var checked = $("#switchery_vat_default").is(":checked");
        if (checked || $("#switchery_vat_default").length == 0) {
            $("#vat").val("1");
        } else {
            $("#vat").val("0");
        }
    });
}

function calculateInGoods() {
    $(document).on(
        "keyup",
        "input[data-field=quantity],input[data-field=volume],input[data-field=weight]",
        debounce(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $tr = $(this).parents("tr");
            setTotal($tr, "volume");
            setTotal($tr, "weight");
            setTotalGoods();
        })
    );

    $(document).on(
        "change",
        "input[data-field=insured_goods]",
        debounce(function (e) {
            $(this).val($(this).prop("checked") ? 1 : 0);
        })
    );
}

function deleteGoods() {
    $(document).on("click", "table.table-goods .delete-goods", function (e) {
        e.preventDefault();
        e.stopPropagation();

        let td = $(this).parent("td").parent("tr:first");

        if (td.hasClass('show')) {
            td.remove();
            setTotalGoods();
            let length = $(".table-goods").find("tbody tr").length - 1;
            if (length === 0) {
                $(".table-goods").removeClass("show").addClass("hide");
                $("#btn-goods-search").removeClass("show").addClass("hide");
                $(".wrap-add-field").removeClass("hide").addClass("show");
            }
        }
    });
}

// Tính dữ liệu thể tích, trọng lượng
// CreatedBy nlhoang 30/10/2019
function setTotal($tr, type) {
    var unitType = $tr.find("input[data-field=" + type + "]").val();
    var quantity = $tr.find("input[data-field=quantity]").val();
    var total = convertFormatNumber(unitType) * convertFormatNumber(quantity);
    $tr.find("input[data-field=total_" + type + "]").val(formatNumber(total));
    // $tr.find('input[data-field=total_' + type + ']').html(formatNumber(total));
}

// Tính tổng thông tin thể tích, trọng lượng
// CreatedBy nlhoang 30/10/2019
function setTotalGoods() {
    let tableGoods = $(".table-goods"),
        quantities = tableGoods
            .find("tbody tr input[data-field=quantity]")
            .toArray();
    let totalQuantity = quantities
        .map((p) => convertFormatNumber($(p).val()))
        .reduce((accumulator, currentValue) => accumulator + currentValue, 0);

    $("input[name=quantity]").val(formatNumber(totalQuantity - 1));
    let weights = tableGoods
            .find("tbody tr input[data-field=total_weight]")
            .toArray(),
        totalWeight = weights
            .map((p) => convertFormatNumber($(p).val()))
            .reduce((accumulator, currentValue) => accumulator + currentValue, 0);
    $("input[name=weight]").val(formatNumber(totalWeight));

    let volumes = tableGoods
            .find("tbody tr input[data-field=total_volume]")
            .toArray(),
        totalVolume = volumes
            .map((p) => convertFormatNumber($(p).val()))
            .reduce((accumulator, currentValue) => accumulator + currentValue, 0);
    $("input[name=volume]").val(formatNumber(totalVolume));
}

// Xử lý sự kiện sau khi Chọn hàng háo từ form xuống
// CreatedBy nlhoang 29/10/2019
function goodsSearchCallback(selectData, goods) {
    if (goods.length > 0) {
        $(".table-goods").removeClass("hide");
        $("#btn-goods-search").removeClass("hide");
        $(".wrap-add-field").addClass("hide");
    }
    let tableGoods = $(".table-goods"),
        tableBody = tableGoods.find("tbody"),
        trLast = tableBody.find("tr:first"),
        length = tableGoods.find("tbody tr").length - 1;
    if (length > 0) {
        var name = $($(".table-goods").find("tbody tr")[length])
            .find("input[type=hidden]")
            .attr("name");
        var matches = name.match(/\[([0-9]+)\]/);
        if (null != matches) {
            length = parseInt(matches[1], 10) + 1;
        }
    } else {
        length = length + 1;
    }

    $.each(goods, (index, item) => {
        let trNew = trLast.clone();

        trNew.find("td .form-control[data-field]").each(function (idx, el) {
            let field = $(el).attr("data-field") || null;
            $(el).attr("name", "goods[" + (index + length) + "][" + field + "]");
            if (field === "goods_type" || field === "goods_unit" || field === "goods_type_id") {
                $(el).val(item[field]);
            } else if (item[field]) {
                $(el).val(formatNumber(item[field]));
            }
        });
        trNew.addClass("show").removeClass("hide");
        tableGoods.find("tbody").append(trNew);
    });
    setTotalGoods();

    $(".number-input")
        .toArray()
        .forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralDecimalMark: ",",
                delimiter: ".",
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: "thousand",
            });
        });
}

function initLocationQuickSearch() {
    if (typeof createLocationQuickSearch != "undefined") {
        var quickSearch = createLocationQuickSearch();
        if (typeof searchLocationUrl != "undefined") {
            var config = {};
            config.customerId = $('#customer_id_order_customer').val();

            quickSearch(config).init();
            $('.location-search').removeClass('bg-disable-combo-box');
        }
    }
}

function registerGetETA() {
    $(document).on('change', '.select-location, #ETD_date, #ETD_time', function (e) {
        e.preventDefault();
        var etd = $('#ETD_date').val() + " " + $('#ETD_time').val();
        var location_destination_id = $('#location_destination_id').val();
        var location_arrival_id = $('#location_arrival_id').val();

        $('#distance').val(0);

        if (etd && location_destination_id && location_arrival_id) {
            sendRequest({
                url: calcETAUri,
                type: 'POST',
                data: {
                    'etd': etd,
                    'location_destination_id': location_destination_id,
                    'location_arrival_id': location_arrival_id
                },
            }, function (response) {
                if (response.errorCode == 0) {
                    var data = response.data;
                    $('#ETA_date').val(data.eta_date);
                    $('#ETA_time').val(data.eta_time);
                    $('#distance').val(data.distance.toString().replace(".", ","));
                }
            });
        }

    });
}

function registerUpdateRevenue() {
    $(document).on("click", "#btn_confirm_update_revenue", function (e) {
        e.preventDefault();
        let ids = $(".selected_item").val(),
            btn = $(this),
            url = btn.data("url"),
            type = btn.data("type");
        url = url + "?ids=" + ids + "&type=" + type;
        showUpdateReveuneModal(url);
    });

    $(document).on("click", "#btn-mass-update-revenue", function (e) {
        e.preventDefault();
        let btn = $(this),
            url = btn.data("url"),
            modal = $("#modal_update_revenue");
        var data = [];
        modal.find("#body_content .amount").each(function (e, item) {
            let input = $(this),
                id = input.data("id"),
                value = input.val().replace(/\./g, "").replace(/,/g, ".");
            data.push({
                id: id,
                value: value,
            });
        });
        sendRequest(
            {
                url: url,
                data: {
                    data: data,
                },
                type: "POST",
            },
            function (response) {
                toastr["success"]("Cập nhật doanh thu thành công");
                modal.modal("hide");
                $(".unselected-all-btn").trigger("click");
                oneLogGrid._ajaxSearch($(".list-ajax"), null, false);
            }
        );
    });

    function showUpdateReveuneModal(url) {
        sendRequest(
            {
                url: url,
                type: "GET",
            },
            function (response) {
                let modal = $("#modal_update_revenue");
                if (!response.ok) {
                    toastr["error"]("Có lỗi xảy ra");
                    return;
                }
                let data = response.data;
                modal.find(".modal-content").html(data.content);
                modal
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
                modal.modal("show");
            }
        );
    }
}