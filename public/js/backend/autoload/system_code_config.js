$(function () {
  $("#switchery_is_generate_time").on("change", function () {
    var checked = $(this).is(":checked");
    if (checked || $(this).length == 0) {
      $("#is_generate_time").val("1");
    } else {
      $("#is_generate_time").val("0");
    }
  });
});
