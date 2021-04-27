$(function () {
  if (typeof uploadUrl != "undefined") {
    var config = {};
    config.uploadUrl = uploadUrl;
    config.downloadUrl = downloadUrl;
    config.removeUrl = removeUrl;
    config.publicUrl = publicUrl;
    config.extension = ".png,.jpg,.gift,.bmp";
    config.existingFiles = existingFiles;
    config.maxFiles = 1;
    config.customSuccessUpload = function (configID, response) {
      var fileIDs = $("#file_id");
      fileIDs.val(response.id);
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
