// Khai báo object library
let oneLogGrid = new OneLogGrid(),
    locationObject = new OneLogLocation(),
    excelObject = new OneLogExcel();
$(document).ready(function () {
    $(document).on("click", ".switchery", function () {
        let input = $(this).parent().find("input");
        if (input.val() === 0) {
            input.val(1);
        } else {
            input.val(0);
        }
    });

    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
    $("#table-scroll .delete-action").tooltip({trigger: "hover"});
    $("#table-scroll .edit-has-modal").tooltip({trigger: "hover"});
    $("#table-scroll .split-action").tooltip({trigger: "hover"});

    $(".select2").select2();

    let oneLogGrid = new OneLogGrid();
    oneLogGrid.init();

    customColumnGrid();

    // tự động đóng thông báo success hoặc error
    displayHideNotification();

    // Đăng ký sự kiện xử lý khi submit form
    submitForm();

    // Đăng ký sự kiện focus vào ô textbox sẽ chọn text
    registerSelectionInput();

    // Đăng ký sự kiện focus vào control đầu tiên
    registerFocusFirstControl();

    // Ẩn hiện mật khẩu
    registerDisplayHidePassword();

    // Xử lý sự kiện Xóa
    registerDelete();

    // Xử lý sự kiện mở popup thêm mới
    registerOpenModal();

    // Xử lý nhập xuất Excel
    excelObject.init(oneLogGrid);
    registerExportSelected();

    // Bổ sung tính năng tìm kiếm
    searchAllSystem();

    //Bổ sung tính năng phóng to thu nhỏ và kéo được modal popup
    registerMaximizeAndMinimizeModal();

    // Đăng ký hiển thị danh sách đã xóa
    registerShowDeleted();

    //Đăng ký format định dang ô nhập liệu
    formatInput();

    scrollToView();

    registerPreviewImage();

    registerShowModal();

    clickNotification();

    clickReadAllNotification();

    print();

    printCustomTemplate();

    lockItem();

    deduplicateItems();
});

// Đăng ký sự kiện xử lý khi submit form
function submitForm() {
    let forms = $("form");
    forms.each((index, form) => {
        let item = $(form);
        item.on("submit", function (e) {
            e.preventDefault();
            var el = $(this);
            el.prop("disabled", true);
            setTimeout(function () {
                el.prop("disabled", false);
            }, 1500);

            if (typeof item.valid !== "function") {
                item.off("submit").submit();
            }
            if (item.valid()) {
                showLoading();
                if (item.hasClass("no-convert")) {
                    item.off("submit").submit();
                } else {
                    // $(form).attr('show-loading') === 1 ? showLoading() : null;
                    $(".number-input").each((index, item) => {
                        let val = $(item).val();
                        if (typeof val === "string") {
                            val = parseFloat(
                                $(item).val().replace(/\./g, "").replace(/,/g, ".")
                            );
                            if (Number.isNaN(val)) {
                                val = null;
                            }
                        }
                        $(item).val(val);
                    });
                    $(form).off("submit").submit();
                }
            }
        });
    });

    // /reset btn
    $("a.reset").on("click", function (e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var href = $(form).attr("action");
        return (window.location.href = href);
    });
}

// tự động đóng thông báo success hoặc error
function displayHideNotification() {
    let timeOut = setTimeout(function () {
        let successMsg = $("#success_msg"),
            errorMsg = $("#error_msg");
        successMsg.animate(
            {
                top: "-60px",
            },
            1000
        );
        errorMsg.animate(
            {
                top: "-60px",
            },
            1000
        );
    }, 4000);

    $(document).on("click", "#success_msg, #error_msg", function () {
        clearTimeout(timeOut);
        $(this).css("top", "-60px");
    });
}

// Đăng ký sự kiện focus vào ô textbox sẽ chọn text
function registerSelectionInput() {
    $("input[type=text]:not(disabled)").on("focus", function (e) {
        $(this).select();
    });
}

// Đăng ký sự kiện focus vào control đầu tiên
function registerFocusFirstControl() {
    $.fn.setCursorPosition = function (pos) {
        this.each(function (index, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd("character", pos);
                range.moveStart("character", pos);
                range.select();
            }
        });
        return this;
    };
    try {
        let input = $(
            "form:first *:input:not([type=hidden]):not([type=radio]):first"
        );
        if (input.val() !== undefined) {
            input
                .focus()
                .setCursorPosition(input.val() === null ? 0 : input.val().length);
        }
    } catch (error) {
        // console.log(error);
    }
}

// Ẩn hiện mật khẩu
function registerDisplayHidePassword() {
    $('input[type="password"]').after(
        '<i class="fa fa-eye-slash show-hide-password" style="cursor: pointer;" title="Click để hiển thị mật khẩu"></i>'
    );
    let $showHidePassword = $(".show-hide-password");
    // đặt thuộc tính position cho lớp cha của nút ẩn hiện pass là relative
    $showHidePassword.parent().css("position", "relative");

    // kiểm tra xem lớp cha của nó có label hay không để đặt khoảng cách top cho hợp lý
    if ($showHidePassword.parent().find("label").length > 0) {
        $showHidePassword.css("top", "38px");
    } else {
        $showHidePassword.css({top: "13px", "z-index": "9999"});
    }
    // click vào con mắt để hiện mật khẩu
    $showHidePassword.click(function () {
        let prev = $(this).parent().find("input"),
            // xóa thông báo lỗi
            feedback = $(this).parent().find(".invalid-feedback"),
            value = prev.val(),
            type = prev.attr("type"),
            name = prev.attr("name"),
            placeholder = prev.attr("placeholder"),
            id = prev.attr("id"),
            klass = prev.attr("class"),
            new_type = type === "password" ? "text" : "password";

        if (feedback.length > 0) {
            feedback.remove();
        }

        if (type === "password") {
            $(this).prop("title", "Click để ẩn mật khẩu");
            $(this).removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
            $(this).prop("title", "Click để hiển thị mật khẩu");
            $(this).removeClass("fa-eye").addClass("fa-eye-slash");
        }
        prev.remove();

        $(this).before(
            '<input type="' +
            new_type +
            '" value="' +
            value +
            '" placeholder="' +
            placeholder +
            '" name="' +
            name +
            '" value="' +
            value +
            '" id="' +
            id +
            '" class="' +
            klass +
            '" />'
        );
    });
}

// Xử lý sự kiện Xóa
function registerDelete() {
    //delete
    $(".delete-action").on("click", function () {
        deleteItem($(this));
    });
    // mass destroy
    $(".mass-destroy-btn").on("click", function (e) {
        e.preventDefault();
        let href = $(this).find(".btn").data("action");
        $("#mass_del_form").attr("action", href);
        pushItemToDestroy();
    });

    $("#check_all_mass_destroy").click(function () {
        $(".mass-destroy")
            .prop("checked", $(this).prop("checked"))
            .trigger("change");
    });

    function pushItemToDestroy() {
        $("#mass_destroy_id").val($(".selected_item").val());
    }

    function deleteItem(item) {
        var href = item.data("action"),
            name = item.parents("tr").children("td[data-name=true]"),
            spanDelete = $("#del-confirm .modal-body span");
        if (typeof name !== "undefined") {
            spanDelete.html("");
            spanDelete.append(
                name
                    .map(function (index, value) {
                        return $(value).clone().find('div').remove().end().text().trim();
                    })
                    .get()
                    .join("-")
            );
        }
        $("#del_form").attr("action", href);
    }

    // close
    $(".close-parent-modal").on("click", function () {
        var parent = $(this).data("parent-modal");
        $("#" + parent)
            .find(".close")
            .click();
    });
}

// Xử lý sự kiện mở popup thêm mới
function registerOpenModal() {
    let addModal = $("#modal_add"),
        addCompleteModal = $("#add_complete");
    $(document).on("click", ".quick-add, .renew-btn", function () {
        let contentContainer = addModal.find(".modal-body"),
            title = addModal.find(".modal-title"),
            button = $(this),
            url = button.data("url"),
            model = button.data("model");

        addCompleteModal.data("button", button);
        $("html").css("overflow-y", "hidden");

        sendRequest(
            {
                url: url,
                type: "GET",
                data: {
                    model: model,
                },
            },
            function (response) {
                if (!response.ok) {
                    return showErrorFlash(response.message);
                }

                contentContainer.html(response.data.content);
                title.text(response.data.title);
                addModal.modal("show");
                advanceValid(model);
            }
        );
    });
    addModal.on("hide.bs.modal", function () {
        $("html").css("overflow-y", "auto");
    });

    function advanceValid(model) {
        let form = addModal.find("form"),
            url = form.attr("action"),
            token = form.find('input[name="_token"]'),
            submitBtn = addModal.find('button[type="submit"]'),
            contentContainer = addModal.find(".modal-body"),
            title = addModal.find(".modal-title"),
            backBtn = addModal.find(".back-button");

        addModal.activeInputModal(model);
        addModal.find("form").validate({});

        backBtn.on("click", function (e) {
            e.preventDefault();
            addModal.modal("hide");
        });

        submitBtn.on("click", function (e) {
            var data = form.serializeArray();
            data.push({name: "model", value: model});

            sendRequest(
                {
                    url: url,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token.val(),
                    },
                    data: data,
                },
                function (response) {
                    if (!response.ok) {
                        return showErrorFlash(response.message);
                    }

                    contentContainer.html(response.data.content);
                    title.text(response.data.title);

                    if (
                        typeof response.data.validation !== "undefined" &&
                        response.data.validation
                    ) {
                        advanceValid(model);
                    } else {
                        completeAdd(response);
                        if (response.data.model) {
                            if (
                                typeof pagingVehicle != "undefined" &&
                                $.isFunction(pagingVehicle)
                            ) {
                                pagingVehicle(1, true);
                            }
                            if ($(".refresh-chart-button").length > 0) {
                                $(".refresh-chart-button").trigger("click");
                            }
                        }
                    }
                }
            );

            return false;
        });

        if (typeof addCompletedLoadingModel != "undefined") {
            addCompletedLoadingModel(model);
        }

        if (model === "location") {
            locationObject.init(true);
        }
    }

    $.fn.activeInputModal = function (model) {
        let addModal = this,
            datepicker = addModal.find(".datepicker"),
            datetimepicker = addModal.find(".datetimepicker"),
            select2 = addModal.find(".select2"),
            timepicker = addModal.find(".timepicker");
        if (datepicker.length > 0) {
            datepicker.each(function () {
                let pickerHorizontal = "auto";
                let pickerVertical = "auto";
                if ($(this).hasClass("fast-order-date")) {
                    pickerHorizontal = "right";
                    pickerVertical = "bottom";
                }
                $(this).datetimepicker({
                    format: "DD-MM-YYYY",
                    locale: "vi",
                    widgetPositioning: {
                        horizontal: pickerHorizontal,
                        vertical: pickerVertical,
                    },
                });
            });
        }
        if (timepicker.length > 0) {
            timepicker.datetimepicker({
                format: "HH:mm",
                locale: "vi",
            });
        }
        if (datetimepicker.length > 0) {
            datetimepicker.each(function () {
                $(this).datetimepicker({
                    locale: "vi",
                });
            });
        }
        if (select2.length > 0) {
            select2.each(function () {
                $(this).select2();
            });
        }

        // TODO: Ẩn Thêm đơn hàng nhanh
        if (addModal.find(".fast-order-form").length > 0) {
            cboFastOrder.createSelection();
            cboFastOrder.changeSelection();
        }
    };

    function completeAdd(response) {
        let title = addCompleteModal.find(".modal-title");

        addModal.modal("hide");
        title.text(response.data.title);
        addCompleteModal.data("entity", response.data.entity);
        addCompleteModal.data("model", response.data.model);

        if (response.data.model === "order") {
            addCompleteModal.addClass("renew-modal");
        }

        addCompleteModal.modal("show");
    }

    $(document).on("show.bs.modal", ".modal", function () {
        let zIndex = 1040 + 10 * $(".modal:visible").length;
        $(this).css("z-index", zIndex);
        setTimeout(function () {
            $(".modal-backdrop")
                .not(".modal-stack")
                .css("z-index", zIndex - 1)
                .addClass("modal-stack");
        }, 0);
    });
}

function registerShowModal(detail) {
    let element = detail
        ? detail
        : ".route-detail, .order-detail, .driver-detail, .vehicle-detail, .admin-detail, .quota-show, .vehicle-team-detail,.customer-detail,.customer-group-detail,.view-detail-info";
    $(document).on("click", element, function (e) {
        e.preventDefault();
        showDetailModal($(this));
    });
}

// Xử lý nhập xuất Excel
let rABS =
    typeof FileReader !== "undefined" &&
    FileReader.prototype &&
    FileReader.prototype.readAsBinaryString,
    useWorker = typeof Worker !== "undefined";

function to_json(workbook) {
    if (useWorker && workbook.SSF) XLSX.SSF.load_table(workbook.SSF);
    let result = {};
    workbook.SheetNames.forEach(function (sheetName) {
        let roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {
            raw: false,
            header: 1,
        });
        if (roa.length > 0) result[sheetName] = roa;
    });
    return result;
}

function isEmpty(str) {
    return !str || 0 === str.length;
}

// Bổ sung tính năng tìm kiếm
function searchAllSystem() {
    if (typeof urlFullSearch != "undefined") {
        $("#fullsearch").autocomplete({
            source: urlFullSearch,
            minLength: 1,
            select: function (event, ui) {
                sendRequest(
                    {
                        url: ui.item.link,
                        type: "GET",
                    },
                    function (response) {
                        if (!response.ok) {
                            return showErrorFlash(response.message);
                        }

                        if (typeof name !== "undefined") {
                            $("#detail-panel .header-detail-panel").html(
                                "Thông tin chi tiết <b>" + ui.item.value + "<b/>"
                            );
                        }
                        $("#detail-panel")
                            .data("url", ui.item.link)
                            .css("width", "100%")
                            .addClass("active");
                        $("#divDetail").html("").append(response.data.content);
                    }
                );
            },
        });
        $("#fullsearch").data("ui-autocomplete")._renderItem = function (ul, item) {
            // var type = item.id.split('_')[0];
            ul.addClass("list-group break-text");

            var $li = $('<li class="list-group-item"></li>');
            var $a = $('<a href="#"></a>');
            var icon = "";
            switch (Number(item.type)) {
                case 1:
                    icon = "fa-user-circle";
                    break;
                case 2:
                    icon = "fa-truck";
                    break;
                case 3:
                    icon = "fa-barcode";
                    break;
                case 4:
                    icon = "fa-id-card";
                    break;
                case 5:
                    icon = "fa-id-card";
                    break;
            }
            $a.html(
                '<i class="fa ' +
                icon +
                ' mr-2" aria-hidden="true"></i></a>' +
                item.value
            );
            ul.append($li.append($a));
            return $li.appendTo(ul);
        };
    }
}

// Bổ sung tính năng phóng to thu nhỏ và kéo được modal popup
function registerMaximizeAndMinimizeModal() {
    $(document).on("click", ".maximize", function (e) {
        $(this)
            .parents(".modal-dialog")
            .attr("style", "")
            .removeClass("ui-draggable ui-draggable-handle")
            .addClass("modal-fullscreen");

        $(this).siblings(".minimize").css("display", "block");
        $(this).css("display", "none");

        var importExcel = $(this)
            .parents("#import_excel")
            .find(".import-table-scroll");
        if (importExcel) {
            importExcel.css("height", "72vh");
        }
    });

    $(document).on("click", ".minimize", function (e) {
        $(this)
            .parents(".modal-dialog")
            .removeClass("modal-fullscreen")
            .addClass("ui-draggable ui-draggable-handle");

        $(this).siblings(".maximize").css("display", "block");
        $(this).css("display", "none");
        var importExcel = $(this)
            .parents("#import_excel")
            .find(".import-table-scroll");
        if (importExcel) {
            importExcel.css("height", "50vh");
        }
    });
}

// Xử lý hiển thị show modal
function showDetailModal(_element) {
    $(".popover").each(function () {
        $(this).popover("hide");
    });
    let id = _element.attr("data-id"),
        url = _element.data("show-url"),
        showModal = $("#modal_show"),
        title = showModal.find(".modal-title"),
        contentContainer = showModal.find(".modal-body"),
        backUrlKey = showModal.find("#back_url_key").val();
    if (id === "" || url === "") {
        return;
    }
    if (showModal.find("#sub_back_url_key").length > 0) {
        backUrlKey = showModal.find("#sub_back_url_key").val();
    }
    sendRequest(
        {
            url: url,
            type: "GET",
            data: {
                id: id,
                back_url_key: backUrlKey,
            },
        },
        function (response) {
            $(".popover").each(function () {
                $(this).popover("hide");
            });
            if (!response.ok) {
                return showErrorFlash(response.message);
            }
            if (response.data.deleted != null && response.data.deleted == true) {
                toastr["warning"]("Đối tượng đã bị xóa.");
            } else if (response.data.auth != null && response.data.auth == true) {
                toastr["warning"]("Bạn không có quyền xem đối tượng.");
            } else {
                contentContainer.html(response.data.content);
                title.html(response.data.title);
                showModal.modal("show");
                registerAuditing();
                registerOverwidthTitle();
            }
        }
    );

    showModal
        .on("show.bs.modal", function () {
            $("html").css("overflow-y", "hidden");
        })
        .on("hide.bs.modal", function () {
            $("html").css("overflow-y", "auto");
        });
}

// Đăng ký format định dang ô nhập liệu
function formatInput() {
    // upload image
    $(".input-image").change(function () {
        previewFile(this);
    });
    //date picker
    $(".datepicker").datetimepicker({
        format: "DD-MM-YYYY",
        locale: "vi",
        useCurrent: false,
    });
    // time picker
    $(".timepicker").datetimepicker({
        format: "HH:mm",
        locale: "vi",
    });
    // date time picker
    $(".datetimepicker").datetimepicker({
        locale: "vi",
    });
    // phone format
    $(".telephone").mask("999-999-9999");

    $(".date-input")
        .toArray()
        .forEach(function (field) {
            new Cleave(field, {
                date: true,
                delimiter: "-",
                datePattern: ["d", "m", "Y"],
            });
        });

    $(".time-input")
        .toArray()
        .forEach(function (field) {
            new Cleave(field, {
                time: true,
                timePattern: ["h", "m"],
            });
        });

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

// Hiển thị chi tiết
function showHideDetailViewLeftPanel(element) {
    if ($(element).hasClass("dvLeftPanel_close")) {
        $(element).removeClass("dvLeftPanel_close");
        $(element).addClass("dvLeftPanel_show");
    } else {
        $(element).removeClass("dvLeftPanel_show");
        $(element).addClass("dvLeftPanel_close");
    }

    if ($(element).find(".svgIcons").hasClass("fCollapseIn")) {
        $(element).find(".svgIcons").removeClass("fCollapseIn");
        $(element).find(".svgIcons").addClass("fCollapseOut");
    } else {
        $(element).find(".svgIcons").removeClass("fCollapseOut");
        $(element).find(".svgIcons").addClass("fCollapseIn");
    }
    var widthRelatedList = $(element)
        .closest(".form-info-wrap")
        .find(".width-related-list");
    var relatedList = $(element)
        .closest(".form-info-wrap")
        .find(".list-related-list");
    if (parseInt(widthRelatedList.css("margin-left")) > 0) {
        widthRelatedList.css("margin-left", 0);
        relatedList.hide();
    } else {
        widthRelatedList.css("margin-left", 200);
        relatedList.show();
    }
}

function scrollToView() {
    $(document).on("click", "a.list-info", function (e) {
        e.preventDefault();
        var dest = $(this).data("dest");
        var element = document.getElementById(dest);
        var trigger = $(this).data("trigger");
        if (element) {
            if (trigger) {
                $("#" + trigger).trigger("click");
            }
            if ($(this).closest(".modal").length > 0) {
                element.scrollIntoView();
            } else {
                element.scrollIntoView({block: "start"});
                // element.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
            }
        }
    });
}

// Đăng ký sự kiện hiển thị nhật ký chỉnh sửa
function registerAuditing() {
    let buttonAuditing = $("#showAuditing"),
        collapseAuditing = $("#collapseAuditing"),
        url = buttonAuditing.data("url");

    if (buttonAuditing.length === 0) {
        return false;
    }
    $(document).on("click", "#showAuditing", function () {
        if (collapseAuditing.hasClass("show")) {
            collapseAuditing.collapse("hide");
            return false;
        }

        sendRequest(
            {
                url: url,
                type: "GET",
            },
            function (response) {
                if (!response.ok) {
                    return showErrorFlash(response.message);
                }
                let content = response.data.content;
                collapseAuditing.find(".card-body").html("").append(content);
                collapseAuditing.collapse("show");
            }
        );
    });
}

function formatNumber(number, decimals, decPoint, thousandsSep) {
    if (Number.isNaN(number)) {
        return "0";
    }
    decimals = Math.abs(decimals) || 4;
    number = parseFloat(number);

    if (!decPoint || !thousandsSep) {
        decPoint = ",";
        thousandsSep = ".";
    }

    var roundedNumber = Math.round(Math.abs(number) * ("1e" + decimals)) + "";
    var numbersString = decimals
        ? roundedNumber.slice(0, decimals * -1) || 0
        : roundedNumber;
    var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : "";
    var formattedNumber = "";

    while (numbersString.length > 3) {
        formattedNumber = thousandsSep + numbersString.slice(-3) + formattedNumber;
        numbersString = numbersString.slice(0, -3);
    }

    if (decimals && decimalsString.length === 1) {
        while (decimalsString.length < decimals) {
            decimalsString = decimalsString + decimalsString;
        }
    }
    return (
        (number < 0 ? "-" : "") +
        numbersString +
        formattedNumber +
        (decimalsString && decimalsString != "0000"
            ? decPoint + decimalsString.replace(/0+$/, "")
            : "")
    );
}

// Đăng ký hiển thị title textbox quá dài
function registerOverwidthTitle() {
    $(".view-control.form-control").each(function () {
        if ($(this)[0].scrollWidth > $(this).innerWidth()) {
            $(this).popover({
                content: $(this).val(),
                trigger: "hover",
                placement: "top",
                container: "body",
            });
        }
    });
}

// Đăng ký hiển thị danh sách đã xóa
function registerShowDeleted() {
    $(document).on("click", "#deleted_btn", function (e) {
        e.preventDefault();
        let modal = $("#deleted_modal"),
            url = modal.find("#deleted_url").val(),
            contentContainer = modal.find("#list_deleted");
        getListDeleted(url, contentContainer, modal);
    });
}

function getListDeleted(url, contentContainer, element) {
    let data = {};
    if (element.hasClass("page-link")) {
        data._s("page", element.data("page"));
    }

    sendRequest(
        {
            url: url,
            type: "GET",
            data: data,
        },
        function (response) {
            if (!response.ok) {
                return showErrorFlash(response.message);
            }
            contentContainer.html(response.data.content);
            contentContainer.find("a.page-link").on("click", function (e) {
                e.preventDefault();
                let link = $(this);
                getListDeleted(url, contentContainer, link);
            });
            contentContainer.find("a.sorting").on("click", function (e) {
                e.preventDefault();
                let sorting = $(this),
                    link = sorting.attr("href");
                getListDeleted(link, contentContainer, sorting);
            });
            if (element.hasClass("modal")) {
                element.modal("show");
            }
        }
    );
}

// Đăng ký sự kiện hiển thị ảnh đầy đủ
function registerPreviewImage() {
    $(document).on("click", ".preview-image", function (e) {
        e.preventDefault();
        var _this = $(this),
            src = _this.attr("src"),
            preview = $("#preview-image");

        preview.find("#preview-img").attr("src", src);
        preview.modal("show");
    });
}

// Refactor lại code cho phép chỉnh sửa nhanh trên form
// ModifiedBy nlhoang 07/04/2020
function allowEditableControlOnForm(editableFormConfig) {
    let _form = {};
    var config =
        typeof editableFormConfig === "undefined" ? {} : editableFormConfig;

    var viewControlElement = config.viewControlElement || "view-control";
    var editControlElement = config.editControlElement || "edit-control";
    var editGroupControlElement =
        config.editGroupControlElement || "edit-group-control";

    var HIDDEN_CLASS = "hidden",
        EDITING_CLASS = "editing",
        EDIT_CLASS = "edit",
        ACCEPT_CLASS = "accept",
        CANCEL_CLASS = "cancel",
        DISABLED_CLASS = "disabled";

    var COMBO_CLASS = "select2",
        NUMBER_CLASS = "number-input",
        DATETIME_CLASS = "datepicker";

    var $editGroup = "." + EDIT_CLASS + "." + editControlElement,
        $acceptGroup = "." + ACCEPT_CLASS + "." + editControlElement,
        $cancelGroup = "." + CANCEL_CLASS + "." + editControlElement;

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

    $(document).on("click", $editGroup, function (e) {
        e.preventDefault();
        $("." + editGroupControlElement).removeClass("editing");
        $($editGroup).removeClass(HIDDEN_CLASS);
        $($acceptGroup).addClass(HIDDEN_CLASS);
        $($cancelGroup).addClass(HIDDEN_CLASS);

        var $viewControl = $("." + viewControlElement);
        $viewControl.removeClass(EDITING_CLASS);
        $viewControl.addClass(DISABLED_CLASS);

        var controlGroup = $(e.currentTarget).closest(
            "." + editGroupControlElement
        );
        controlGroup.popover("hide");
        var control = controlGroup.find("." + viewControlElement);
        control.removeClass("disabled");
        var val = control.val();
        var name = control.attr("name");
        _form[name] = val;
        controlGroup.find("." + viewControlElement).addClass(EDITING_CLASS);
        $viewControl.each((index, item) => {
            var pro = $(item).attr("name");
            if (_form.hasOwnProperty(pro)) {
                $(item).val(_form[pro]);
            }
        });
        if (control.hasClass(DATETIME_CLASS)) {
            control.datetimepicker({
                format: "DD-MM-YYYY",
                locale: "vi",
                widgetPositioning: {
                    horizontal: "auto",
                    vertical: "auto",
                },
            });
        }
        if (control.hasClass(NUMBER_CLASS)) {
            new Cleave(control, {
                numeral: true,
                numeralDecimalMark: ",",
                delimiter: ".",
                numeralDecimalScale: 4,
                numeralThousandsGroupStyle: "thousand",
            });
        }
        if (control.hasClass(COMBO_CLASS)) {
            control.prop("disabled", false);
        }
        controlGroup.addClass(EDITING_CLASS);
        controlGroup.find($editGroup).addClass(HIDDEN_CLASS);
        controlGroup.find($acceptGroup).removeClass(HIDDEN_CLASS);
        controlGroup.find($cancelGroup).removeClass(HIDDEN_CLASS);
    });

    $(document).on("click", $acceptGroup, function (e) {
        e.preventDefault();
        var form = $(e.currentTarget).closest(".form-info-wrap");
        var controlGroup = $(e.currentTarget).closest(
            "." + editGroupControlElement
        );
        var control = controlGroup.find("." + viewControlElement);
        if (control.length == 0) return;

        var val = control.val();
        var name = control.attr("name");
        _form[name] = val;
        var id = $(e.currentTarget).closest(".form-info-wrap").attr("data-id");

        if (control.hasClass(NUMBER_CLASS)) {
            if (typeof val === "string") {
                val = parseFloat(val.replace(/\./g, "").replace(/,/g, "."));
                if (Number.isNaN(val)) {
                    val = null;
                }
            }
        }
        var data = {
            Id: id,
            Field: name,
            Value: val,
            Entity: form.attr("data-entity"),
        };
        var url = form.attr("data-quicksave") || config.url;
        sendRequest(
            {
                url: url,
                type: "POST",
                data: data,
            },
            function (response) {
                if (!response.ok) {
                    toastr["error"](response.message);
                    return;
                }
                toastr["success"]("Cập nhật thành công");

                control.addClass(DISABLED_CLASS);
                if (control.hasClass(COMBO_CLASS)) {
                    control.prop("disabled", true);
                }

                controlGroup.find("." + viewControlElement).removeClass(EDITING_CLASS);
                controlGroup.removeClass(EDITING_CLASS);
                controlGroup.find($editGroup).removeClass(HIDDEN_CLASS);
                controlGroup.find($acceptGroup).addClass(HIDDEN_CLASS);
                controlGroup.find($cancelGroup).addClass(HIDDEN_CLASS);

                displayTooltip(control, controlGroup);

                if (typeof config.customAfterSave !== "undefined") {
                    config.customAfterSave(id, name, form);
                }
            }
        );
    });

    $(document).on("click", $cancelGroup, function (e) {
        e.preventDefault();

        var controlGroup = $(e.currentTarget).closest(
            "." + editGroupControlElement
        );
        var control = controlGroup.find("." + viewControlElement);
        var name = control.attr("name");

        control
            .val(_form[name])
            .addClass(DISABLED_CLASS)
            .removeClass(EDITING_CLASS);
        if (control.hasClass(COMBO_CLASS)) {
            control.trigger("change");
            control.prop("disabled", true);
        }

        controlGroup.removeClass(EDITING_CLASS);
        controlGroup.find($editGroup).removeClass(HIDDEN_CLASS);
        controlGroup.find($acceptGroup).addClass(HIDDEN_CLASS);
        controlGroup.find($cancelGroup).addClass(HIDDEN_CLASS);
        displayTooltip(control, controlGroup);
    });

    function displayTooltip(control, controlGroup) {
        if (control && control[0].scrollWidth > control.innerWidth()) {
            controlGroup.popover({
                content: control.val(),
                trigger: "hover",
                placement: "bottom",
                container: "body",
            });
        }
    }
}

// Đăng kí sự kiện mở notification
// ModifiedBy nlhoang 07/04/2020
function clickNotification() {
    $("#notification-link").click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('.loading-notify').show();
        var notiDropdown = $("#notification-wrap");
        var userDropdown = $("#dropdown-profile");
        if (userDropdown.hasClass('show')) {
            $('#profile-hyperlink').dropdown('hide');
        }
        if (notiDropdown.hasClass('show')) {
            $("#notification-hyperlink").dropdown("hide");
        } else {
            $("#notification-hyperlink").dropdown("show");
        }
        // $("#notification-hyperlink").dropdown("show");
        $("#show_notification").html("");
        var url = $(this).data("url");
        if (url != null) {
            $.get(url, function (data) {
                $('.loading-notify').hide();
                $("#show_notification").html(data);
            });
        }
    });
}

//Đăng ký sự kiện đánh dấu đã đọc tất cả notify
function clickReadAllNotification() {
    $(document).on("click", "#read-all-notify", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $("#notification-hyperlink").dropdown("show");
        $('.loading-notify').show();
        $.ajax({
            url: makeReadAllNotification,
            dataType: "html",
            data: {
                req: "1",
            },
            success: function (response) {
                // var notification = $("#show_notification");
                // if (notification) {
                //   console.log(response);
                //   notification.html(response);
                // }
                $('.loading-notify').hide();
                $("#show_notification").children('.notify-item').each(function (index, value) {
                    $(value).addClass('notify-item-read');
                    $('#count_notifications').html(0);
                    $('.noti-icon-badge').html(0);
                });

            },
        });
    });
}

// Tùy chỉnh cột hiển thị trong grid
function customColumnGrid() {
    $(".right-config").on("click", function (e) {
        e.preventDefault();
        if ($(this).find(".fa-angle-left").length !== 0) {
            $(".flex-list-data-content").addClass("list-data-content");
            let elm = $(".fa-angle-left");
            elm.removeClass("fa-angle-left").addClass("fa-angle-right");
            $(".filter-config-content").toggle();
        } else {
            $(".flex-list-data-content").removeClass("list-data-content");
            let elm = $(".fa-angle-right");
            elm.removeClass("fa-angle-right").addClass("fa-angle-left");
            $(".filter-config-content").toggle();
        }
    });

    $(".add-my-filter").on("click", function (e) {
        e.preventDefault();
        $(".info-my-filter").fadeIn(500);
    });

    $("#cancel-save-my-filter").on("click", function (e) {
        e.preventDefault();
        $(".info-my-filter").fadeOut(500);
    });
}

// In từ trình duyệt
// CreatedBy nlhoang 06/04/2020
function print() {
    $(document).on("click", ".print-action", function (e) {
        e.preventDefault();
        console.log("print");
        var printContents = $(".content-detail").html();
        if (!printContents) return;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    });
}

// MỞ form chọn mẫu In tùy chỉnh
// CreatedBy nlhoang 06/04/2020
function printCustomTemplate() {
    $(document).on("click", ".print-custom-action", function (e) {
        e.preventDefault();

        let btn = $(this),
            modal = $("#modal_template"),
            url = btn.data("url"),
            type = btn.data("type");
        var id = $(this).parents(".form-info-wrap").data("id");
        url = url + "?ids=" + id + "&type=" + type;
        showPrintCustom(url);
    });

    $(document).on("click", "#print_template_selected", function (e) {
        e.preventDefault();
        let ids = $(".selected_item").val(),
            btn = $(this),
            url = btn.data("url"),
            type = btn.data("type");
        url = url + "?ids=" + ids + "&type=" + type;
        showPrintCustom(url);
    });

    function showPrintCustom(url) {
        sendRequest(
            {
                url: url,
                type: "GET",
            },
            function (response) {
                let modal = $("#modal_template");
                let data = response.data;
                modal.find(".modal-content").html(data.content);
                modal.modal("show");
            }
        );
    }
}

// Hàm tạo đối tượng Dropzone
// CreatedBy nlhoang 06/04/2020
function createDropzone() {
    var dropzoneOneLog = function (config) {
        var addActionDownload = function (dropzone, previewElement, file, url) {
            previewElement.append('<div class="dz-action row mt-2"></div>');
            previewElement
                .find(".dz-remove")
                .appendTo(previewElement.find(".dz-action"));
            previewElement.find(".dz-remove").addClass("col-6 fa fa-remove");
            file._dzDowload = dropzone.createElement(
                '<a class="dz-download col-6 fa fa-download" href="javascript:undefined;"></a>'
            );
            file._dzDowload.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                window.location.href = url;
            });
            previewElement.find(".dz-action").prepend(file._dzDowload);
        };

        var activePreview = function (element) {
            element.on("click", function (e) {
                e.preventDefault();
                var _this = $(this),
                    src = _this.data("image"),
                    preview = $("#preview-modal");

                preview.find("#preview").attr("src", src);
                preview.modal("show");
            });
        };

        var deletePreview = function (element) {
            $(".dz-remove").on("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                var container = $(this).closest(".dz-preview");
                container.remove();
            });
        };

        var contructor = function () {
            Dropzone.autoDiscover = false;
            var element = config.element || ".dropzone";
            var downloadUrl = config.downloadUrl;
            var removeUrl = config.removeUrl;
            var publicUrl = config.publicUrl;
            var existingFiles = config.existingFiles;
            $(element).each(function () {
                var _this = $(this),
                    configID = _this.data("id"),
                    type = Number(_this.data("file_type"));

                var file_type = ".jpeg,.jpg,.png,.gif,.xls,.xlsx,.doc,.docx,.pdf";
                if (typeof config.extension === "undefined") {
                    switch (type) {
                        case 1:
                            file_type = ".jpeg,.jpg,.png,.gif";
                            break;
                        case 2:
                            file_type = ".xls,.xlsx";
                            break;
                        case 3:
                            file_type = ".doc,.docx";
                            break;
                        case 4:
                            file_type = ".pdf";
                            break;
                        default:
                            file_type = ".jpeg,.jpg,.png,.gif,.xls,.xlsx,.doc,.docx,.pdf";
                            break;
                    }
                } else {
                    file_type = config.extension;
                }
                _this.dropzone({
                    url: config.uploadUrl,
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    autoProcessQueue: true,
                    uploadMultiple: false,
                    parallelUploads: 5,
                    maxFilesize: config.maxFilesize || 5,
                    maxFiles: config.maxFiles || 10,
                    acceptedFiles: file_type,
                    dictFileTooBig:
                        "Dung lượng của tệp phải <= " + (config.maxFilesize || 5) + "MB",
                    dictMaxFilesExceeded:
                        "Số lượng tệp tối đa <= " + (config.maxFiles || 10) + " tệp",
                    dictInvalidFileType: "Tệp sai định dạng cho phép",
                    addRemoveLinks: true,
                    init: function () {
                        var prevFile;
                        this.on("maxfilesexceeded", function (file) {
                            this.removeAllFiles();
                            this.addFile(file);
                        });
                        this.on("error", function (file, message) {
                            alert(message);
                            this.removeFile(file);
                        });
                        this.on("success", function (file, response) {
                            var previewElement = $(file.previewElement);

                            if (typeof config.customSuccessUpload !== "undefined") {
                                config.customSuccessUpload(configID, response);
                            }

                            console.log(previewElement);
                            previewElement.attr("data-image", publicUrl + response.path);
                            previewElement.find(".dz-remove").attr("data-id", response.id);

                            //Add action download
                            addActionDownload(
                                Dropzone,
                                previewElement,
                                file,
                                downloadUrl.replace("-1", response.id)
                            );

                            activePreview(previewElement);
                        });

                        if (existingFiles.length > 0) {
                            var mockFiles = [];
                            if (typeof config.customFilterFile !== "undefined") {
                                mockFiles = config.customFilterFile(configID, existingFiles);
                            } else {
                                mockFiles = existingFiles;
                            }

                            mockFiles.forEach((mockFile) => {
                                mockFile.status = Dropzone.ADDED;
                                mockFile.accepted = true;

                                this.files.push(mockFile);
                                this.emit("addedfile", mockFile);
                                this.emit("thumbnail", mockFile, mockFile.url);
                                this.emit("complete", mockFile);

                                var previewElement = $(mockFile.previewElement);
                                previewElement.attr("data-image", mockFile.full_url);
                                previewElement.find(".dz-remove").attr("data-id", mockFile.id);

                                //Add action download
                                addActionDownload(
                                    Dropzone,
                                    previewElement,
                                    mockFile,
                                    mockFile.urlDownload
                                );

                                activePreview(previewElement);
                            });
                        }
                        this._updateMaxFilesReachedClass();
                    },
                    removedfile: function (file) {
                        var container = $(file.previewElement),
                            id = container.find(".dz-remove").data("id"),
                            url = removeUrl.replace("-1", id);

                        if (typeof id !== "undefined") {
                            var fileIDs = $("#file_id");
                            if (typeof config.customRemovedUpload !== "undefined") {
                                fileIDs = config.customRemovedUpload(configID);
                            }
                            var values = fileIDs.val().split(";");
                            var stringValue = "";
                            for (var i = 0; i < values.length; i++) {
                                if (values[i] != id) {
                                    stringValue += values[i] + ";";
                                }
                            }
                            if (!isEmpty(stringValue)) {
                                stringValue = stringValue.slice(0, stringValue.length - 1);
                            }
                            fileIDs.val(stringValue);
                            console.log("success");
                        }

                        var _ref;
                        if (file.previewElement) {
                            if ((_ref = file.previewElement) != null) {
                                _ref.parentNode.removeChild(file.previewElement);
                            }
                        }
                        return this._updateMaxFilesReachedClass();
                    },
                    hiddenInputContainer: "body",
                    clickable: true,
                });
            });
        };

        var _init = function () {
            var $this = this;
            contructor();
            activePreview($(".dz-preview"));
            deletePreview();
        };

        return {
            init: _init,
        };
    };
    return dropzoneOneLog;
}

function registerExportSelected() {
    if ($("#export_selected").length) {
        $("#export_selected").on("click", function (e) {
            e.preventDefault();
            let ids = $(".selected_item").val(),
                btn = $(this),
                url = btn.data("url");
            url = url + "?ids=" + ids + "&update=1";
            window.open(url);
        });
    }
}

// Khoá đơn hàng
//CreatedBy nlhoang 29/09/2020
function lockItem() {
    var options = {
        format: "DD/MM/YYYY",
        startDate: moment().startOf("month"),
        endDate: moment().endOf("month"),
        dateLimit: {
            days: 31,
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            "Hôm nay": [moment(), moment()],
            "Hôm qua": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "7 ngày trước": [moment().subtract(6, "days"), moment()],
            "30 ngày trước": [moment().subtract(29, "days"), moment()],
            "Tuần này": [moment().startOf("isoWeek"), moment().endOf("isoWeek")],
            "Tuần trước": [
                moment().subtract(7, "days").startOf("isoWeek"),
                moment().subtract(7, "days").endOf("isoWeek"),
            ],
            "Tháng này": [moment().startOf("month"), moment().endOf("month")],
            "Tháng trước": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
        },
        opens: "left",
        drops: "down",
        buttonClasses: ["btn", "btn-sm"],
        applyClass: "btn-success",
        cancelClass: "btn-secondary",
        separator: " to ",
        locale: {
            applyLabel: "Chọn",
            cancelLabel: "Hủy",
            fromLabel: "Từ",
            toLabel: "đến",
            customRangeLabel: "Tùy chọn",
            daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            monthNames: [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5 ",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12",
            ],
            firstDay: 1,
        },
    };
    var $modal = $("#modal_lock");
    var func = function (modal, url, type) {
        modal.data("type", type);
        modal.data("url", url);

        var title = type == "lock" ? "Khoá sổ" : "Mở khoá sổ";
        modal.find(".modal-title").text(title);
        if (type == "lock") {
            modal.find(".description").show();
        } else {
            modal.find(".description").hide();
        }
        modal.find("#span-lock").text(title);
        var untype = type === "lock" ? "unlock" : "lock";
        modal
            .find("#i-lock")
            .removeClass("fa-" + untype)
            .addClass("fa-" + type);
        modal.modal("show");
    };

    if ($("#btn_lock").length) {
        $("#btn_lock").on("click", function (e) {
            e.preventDefault();
            var url = $(this).data("url");
            $("#range-date-lock").daterangepicker(options, function (
                start,
                end,
                label
            ) {
                $("#range-date-lock span").html(
                    start.locale("vi").format("D MMMM, YYYY") +
                    " - " +
                    end.locale("vi").format("D MMMM, YYYY")
                );
            });

            func($modal, url, "lock");
        });
    }

    if ($("#btn_unlock").length) {
        $("#btn_unlock").on("click", function (e) {
            e.preventDefault();
            var url = $(this).data("url");
            $("#range-date-lock").daterangepicker(options, function (
                start,
                end,
                label
            ) {
                $("#range-date-lock span").html(
                    start.locale("vi").format("D MMMM, YYYY") +
                    " - " +
                    end.locale("vi").format("D MMMM, YYYY")
                );
            });
            func($modal, url, "unlock");
        });
    }

    if ($("#btn-lock-items").length) {
        $("#btn-lock-items").on("click", function (e) {
            e.preventDefault();
            var url = $modal.data("url");
            var type = $modal.data("type");
            var data = {
                fromDate: $("#range-date-lock")
                    .data("daterangepicker")
                    .startDate.format("YYYY-MM-DD"),
                toDate: $("#range-date-lock")
                    .data("daterangepicker")
                    .endDate.format("YYYY-MM-DD"),
                type: $("#day-condition-lock").val(),
            };
            sendRequest(
                {
                    url: url,
                    type: "POST",
                    data: data,
                },
                function (response) {
                    if (response.errorCode != 0) {
                        toastr["error"](response.message);
                        return;
                    }
                    $modal.modal("hide");
                    if (type == "lock") {
                        toastr["success"]("Khoá sổ thành công");
                    } else {
                        toastr["success"]("Mở khoá sổ thành công");
                    }
                }
            );
        });
    }
}

// Gộp trùng
//CreatedBy nlhoang 30/09/2020
function deduplicateItems() {
    var $modal = $("#modal_deduplicate");

    if ($("#btn-deduplicate").length) {
        $("#btn-deduplicate").on("click", function (e) {
            e.preventDefault();
            var url = $(this).data("url");
            let ids = $(".selected_item").val();
            url = url + "?ids=" + ids;
            sendRequest(
                {
                    url: url,
                    type: "GET",
                },
                function (response) {
                    if (response.errorCode != 0) {
                        console.error(response.errorMessage);
                        toastr["error"]("Có lỗi xảy ra khi gộp");
                        return;
                    }

                    $modal.find(".body").html(response.content);
                    $modal.modal("show");
                }
            );
        });
    }

    if ($("#btn-process-deduplicate").length) {
        $("#btn-process-deduplicate").on("click", function (e) {
            e.preventDefault();
            var el = $(this);
            el.prop("disabled", true);
            setTimeout(function () {
                el.prop("disabled", false);
            }, 500);
            var destinationIDs = [];
            var sourceID = null;
            var checkedInputs = $modal.find(".body input[name=radios]:checked");
            if (checkedInputs.length == 0) {
                toastr["warning"]("Vui lòng chọn 1 bản ghi cần giữ lại.");
                return;
            }
            sourceID = $(checkedInputs[0]).val();
            var uncheckedInputs = $modal.find(
                ".body input[name=radios]:not(:checked)"
            );
            uncheckedInputs.each((a, b) => {
                destinationIDs.push($(b).val());
            });

            var url = $(this).data("url");
            var data = {
                sourceID: sourceID,
                destinationIDs: destinationIDs,
            };
            sendRequest(
                {
                    url: url,
                    type: "POST",
                    data: data,
                },
                function (response) {
                    if (response.errorCode != 0) {
                        console.error(response.errorMessage);
                        toastr["error"]("Có lỗi xảy ra khi gộp trùng dữ liệu");
                        return;
                    }
                    $modal.modal("hide");
                    toastr["success"]("Gộp trùng thành công");
                    $(".unselected-all-btn").trigger("click");
                    oneLogGrid._ajaxSearch($(".list-ajax"));
                }
            );
        });
    }
}
