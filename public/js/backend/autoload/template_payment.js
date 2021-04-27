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
    if (typeof uploadUrl != "undefined") {
        var config = {};
        config.uploadUrl = uploadUrl;
        config.downloadUrl = downloadUrl;
        config.removeUrl = removeUrl;
        config.publicUrl = publicUrl;
        config.extension = ".xls,.xlsx,.doc,.docx";
        config.existingFiles = existingFiles;
        config.maxFiles = 1;
        config.customSuccessUpload = function (configID, response) {
            var fileIDs = $("#file_id");
            fileIDs.val(
                fileIDs.val() == "" ? response.id : fileIDs.val() + ";" + response.id
            );
        };
        config.customRemovedUpload = function (configID) {
            return $("#file_id");
        };
        var dropzoneOneLog = createDropzone();
        dropzoneOneLog(config).init();
    }

    $(document).on(
        "click",
        ".download-item",
        debounce(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $a = $(this);
            var url = $a.data("url");
            var type = $a.data("type");
            window.location.href = url.replace(-1, type);
        })
    );


});


// Xử lý thêm ánh xạ
function generateMappingItem(entity) {
    var $tableBody = $(".table-mapping").find("tbody"),
        $trLast = $tableBody.find("tr:last");

    if ($trLast.find(".select-receipt-payment").data("select2")) {
        $trLast.find(".select-receipt-payment").select2("destroy");
    }
    var $trNew = $trLast.clone();
    $trNew.find(".select-receipt-payment").removeAttr("data-select2-id");
    $trNew.find(".select-receipt-payment option").removeAttr("data-select2-id");

    $trNew.find("td").each(function () {
        var el = $(this)
            .find(".mapping")
            .each(function (idx, element) {
                var id = $(element).attr("id") || null;
                if (id) {
                    var tmp = id.split("][");
                    var index = Number(tmp[0].split("[")[1]) + 1;
                    var prop = tmp[1].substring(0, tmp[1].length - 1);
                    var name = "templatePaymentMappingList[" + index + "][" + prop + "]";
                    $(element).attr("id", name);
                    $(element).attr("name", name);
                }
            });
    });
    $trLast.after($trNew);

    $trLast.find(".select-receipt-payment").select2();
    var receiptPayment = $trNew.find(".select-receipt-payment");
    receiptPayment.select2();

    if (entity) {
        $trNew
            .find(".select-receipt-payment")
            .val(entity.receipt_payment_id)
            .trigger("change");
        $trNew.find(".input-uppercase").val(entity.column_index);
    } else {

        $trNew.find(".select-receipt-payment").val("").trigger("change");

        $trNew.find(".input-uppercase").val("");
    }

}
