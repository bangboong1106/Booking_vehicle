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
      var fileIDs = $("#path");
      fileIDs.val(response.path);
    };
    config.customRemovedUpload = function (configID) {
      return $("#path");
    };
    var dropzoneOneLog = createDropzone();
    dropzoneOneLog(config).init();
  }
  $("#btn-convert").click(function (e) {
    e.preventDefault();
    var el = $(this);
    el.prop("disabled", true);
    setTimeout(function () {
      el.prop("disabled", false);
    }, 1500);

    if($("#template_id").val() == ""){
      toastr["error"]("Vui lòng chọn mẫu file chuyển đổi");
      return;
    }
    if($("#path").val() == ""){
      toastr["error"]("Vui lòng chọn file chuyển đổi");
      return;
    }

    var url = $(this).data("url");
    var data = {
      template_id: $("#template_id").val(),
      path: $("#path").val(),
    };
    var params = Object.keys(data)
      .reduce(function (a, k) {
        a.push(k + "=" + encodeURIComponent(data[k]));
        return a;
      }, [])
      .join("&");
    url = url + "?" + params;

    showLoading();
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.responseType = "blob";
    xhr.onload = function () {
      hideLoading();
      var filename = "";
      var disposition = xhr.getResponseHeader("Content-Disposition");
      if (disposition && disposition.indexOf("attachment") !== -1) {
        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        var matches = filenameRegex.exec(disposition);
        if (matches != null && matches[1]) {
          filename = matches[1].replace(/['"]/g, "");
        }
      }

      $("#path").val('');
      toastr["success"]("Chuyển đổi dữ liệu thành công");
      saveAs(xhr.response, filename);
    };
    xhr.send();
  });
});
