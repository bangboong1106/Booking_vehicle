$(function () {
    registerUpdateDocuments();
});


// Cập nhật trạng thái chứng từ trên form Chi tiết
// CreatedBy nlhoang 13/02/2020
function registerUpdateDocuments() {
    $(document).on('click', '#btn-update-documents', function () {
            var id = $(this).parents('.form-info-wrap').attr('data-id');
            showLoading();
            sendRequestNotLoading({
                url: updateDocumentsUri,
                type: 'POST',
                data: {
                    ids: id
                }
            }, function (response) {
                if (!response.ok) {
                    toastr["error"](response.message);
                } else {
                    toastr["success"]('Cập nhật trạng thái thành công');
                    $('#btn-update-documents').css('display', 'none');
                    $('#status_collected_documents').html('Đã thu đủ');
                    hideLoading();
                    oneLogGrid._ajaxSearch($('.list-ajax'), null, false);
                }
            });
        }
    );
}