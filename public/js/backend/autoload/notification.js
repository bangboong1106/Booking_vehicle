$(function () {
  loadMore();
  $("#notify-load-more").click(function (e) {
    e.preventDefault();
    loadMore();
  });

  registerUpdateReadNotify();
  registerVehicleNotifyDetail();
  registerUpdateDocuments();
});

function loadMore() {
  var page = $("#notify-page").val();
  sendRequest(
    {
      url: urlLoadMoreNotify,
      type: "POST",
      data: {
        page: page,
      },
    },
    function (response) {
      if (!response.ok) {
        return showErrorFlash(response.message);
      } else {
        if (response.data.content != "") {
          $("#notify-list").append(response.data.content);
          page = Number(page) + 1;
          $("#notify-page").val(page);
          $("#notify-load-more").css("display", "block");
        } else {
          $("#notify-load-more").css("display", "none");
        }
      }
    }
  );
}

function registerUpdateReadNotify() {
  $(document).on("click", ".notify-item", function (e) {
    e.preventDefault();
    var element = $(this).children("div");
    var readStatus = $(this).data("read-status");
    if (readStatus == 0) {
      var id = $(this).data("notify-id");
      sendRequestNotLoading(
        {
          url: urlUpdateStatusNotify,
          type: "POST",
          data: {
            id: id,
          },
        },
        function (response) {
          if (response.errorCode != 0) {
            return showErrorFlash(response.errorMessage);
          } else {
            element
              .removeClass("notification-item")
              .addClass("notification-item-read");
            $(".noti-icon-badge").html(response.countUnread);
          }
        }
      );
    }
  });
}

function registerVehicleNotifyDetail() {
  $(document).on("click", ".vehicle-notify-item", function (e) {
    e.preventDefault();
    var actionIds = $(this).data("id");
    var actionScreen = $(this).data("action-screen");
    sendRequest(
      {
        url: urlVehicleNotifyDetail,
        type: "POST",
        data: {
          actionIds: actionIds,
          actionScreen: actionScreen,
        },
      },
      function (response) {
        if (!response.ok) {
          return showErrorFlash(response.message);
        } else {
          if (response.data.content != "") {
            $("#modal-vehicle-notify-content").html("");
            $("#modal-vehicle-notify-content").append(response.data.content);
            $("#modal_vehicle_notify_detail").modal("show");
          }
        }
      }
    );
  });
}

// Cập nhật trạng thái chứng từ trên form Chi tiết
// CreatedBy nlhoang 14/02/2020
function registerUpdateDocuments() {
  $(document).on("click", "#btn-update-documents", function () {
    var id = $(this).parents(".form-info-wrap").attr("data-id");
    var now = moment();
    showLoading();
    sendRequestNotLoading(
      {
        url: urlUpdateDocument,
        type: "POST",
        data: {
          ids: id,
        },
      },
      function (response) {
        if (!response.ok) {
          toastr["error"](response.message);
        } else {
          toastr["success"]("Cập nhật trạng thái thành công");
          $("#btn-update-documents").css("display", "none");
          $("#is_collected_documents").html(
            '<i class="fa fa-check" aria-hidden="true"></i>'
          );
          $("#status_collected_documents").text("Đã thu đủ chứng từ");
          $("#datetime_collected_documents_reality").text(
            now.format("DD/MM/YYYY HH:mm")
          );
          hideLoading();
        }
      }
    );
  });
}
