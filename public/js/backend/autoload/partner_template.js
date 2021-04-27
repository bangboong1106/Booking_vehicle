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

  $("#type").on("change", function () {
    var type = $(this).val();
    $('#select-goods').val('').trigger('change');
    $('#select-cost').val('').trigger('change');
    $("#list_item").val('');

    if (type == 3 || type == 7) {
      $("#wrap-is-print-empty-cost").show();
      $(".select-cost").show();
    } else {
      $("#wrap-is-print-empty-cost").hide();
      $(".select-cost").hide();
    }

    if (type == 1) {
      $(".select-goods").show();
    } else {
      $(".select-goods").hide();
    }
  });

  $("#select-goods,#select-cost").on("change", function () {
    $("#list_item").val($(this).val());
  });

  $("#switchery_is_print_empty_cost").on("change", function () {
    var checked = $(this).is(":checked");
    if (checked || $(this).length == 0) {
      $("#form_is_print_empty_cost").val("1");
    } else {
      $("#form_is_print_empty_cost").val("0");
    }

    $("#switchery_is_print_empty_goods").on("change", function () {
        var checked = $(this).is(":checked");
        if (checked || $(this).length == 0) {
            $("#form_is_print_empty_goods").val("1");
        } else {
            $("#form_is_print_empty_goods").val("0");
        }
    });

  });
});
