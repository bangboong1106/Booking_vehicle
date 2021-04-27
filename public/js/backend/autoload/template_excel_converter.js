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

  $("#switchery_is_use_convert_sheet").on("change", function () {
    var checked = $(this).is(":checked");
    if (checked || $(this).length == 0) {
      $("#is_use_convert_sheet").val("1");
    } else {
      $("#is_use_convert_sheet").val("0");
    }});
});
