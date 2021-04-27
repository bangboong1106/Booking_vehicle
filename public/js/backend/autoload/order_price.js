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
  registerUpdatePriceOfOrder();
});

// Cập nhật cước phí vận chuyển của đơn hàng
// CreatedBy nlhoang 04/08/2020
function registerUpdatePriceOfOrder() {
  $(document).on("click", "#btn-update-price", function () {
    var url = $(this).data("url");
    var data = $(".selected_item").val();
    sendRequest(
      {
        url: url,
        type: "POST",
        data: {
          data: data,
        },
      },
      function (response) {
        if (response.errorCode == -1) {
          toastr["error"](response.errorMessage);
        } else {
          toastr["success"]("Cập nhật giá đơn hàng thành công");
        }
      }
    );
  });
}
