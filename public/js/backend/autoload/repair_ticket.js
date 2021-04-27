$(function () {
  registerCombo();

  registerSelect();

  registerCalculateAmount();
});

function registerCombo() {
  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlDriver !== "undefined") {
      cboSelect2.driver(urlDriver);
    }
    if (typeof urlVehicle !== "undefined") {
      cboSelect2.vehicle(urlVehicle);
    }
  }

  if (typeof createDriverQuickSearch != "undefined") {
    var quickSearch = createDriverQuickSearch();

    var driverConfig = {};
    quickSearch(driverConfig).init();
  }

  if (typeof createVehicleQuickSearch != "undefined") {
    var quickSearch = createVehicleQuickSearch();
    var config = {};
    quickSearch(config).init();
  }
}

// Lưu thông tin xe và tài xế vào hiddenfield
// CreatedBy nlhoang 26/08/2020
function registerSelect() {
  $(".select2").change(function () {
    var data = $(this).select2("data");

    var $name = $(this)
      .parents(".select2-bootstrap-prepend")
      .find("input[type=hidden]");

    $name.val(data[0] ? data[0].title : "");
  });
}

// Tính thành tiền khi thay đổi số lượng hoặc đơn giá
// CreatedBy nlhoang 26/08/2020
function registerCalculateAmount() {
  $(document).on(
    "keyup",
    "input[data-field=quantity],input[data-field=price]",
    debounce(function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $tr = $(this).parents("tr");
      var $quantity = convertFormatNumber(
        $tr.find("input[data-field=quantity]").val()
      );
      var $price = convertFormatNumber(
        $tr.find("input[data-field=price]").val()
      );
      var $amount = $tr.find("input[data-field=amount]");
      $amount.val(formatNumber($quantity * $price));

      let tableGoods = $("#table-amount"),
        amountItems = tableGoods
          .find("tbody tr input[data-field=amount]")
          .toArray(),
        totalAmount = amountItems
          .map((p) => convertFormatNumber($(p).val()))
          .reduce((accumulator, currentValue) => accumulator + currentValue, 0);
      $("#amount").val(formatNumber(totalAmount));
    })
  );
}

// Format định số
// CreatedBy nlhoang 26/08/2020
function convertFormatNumber(val) {
  var result = val;
  if (typeof val === "string") {
    result = parseFloat(val.replace(/\./g, "").replace(/,/g, "."));
    if (Number.isNaN(result)) {
      result = 0;
    }
  }
  return result;
}
