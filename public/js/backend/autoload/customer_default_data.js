$(function () {
  if (typeof comboCustomerUri !== "undefined" && typeof cboSelect2 !== "undefined") {
    cboSelect2.customer(comboCustomerUri, null);
  }

  if (typeof createLocationQuickSearch != "undefined") {
    var exceptIds = [];
    var quickSearch = createLocationQuickSearch();
    if (typeof exceptIds != "undefined") {
      var config = {};
      config.exceptIds = exceptIds;
      quickSearch(config).init();
    }
  }

  initCombobox($('.select-customer').val());
  activeCombobox();

  $('#customer_id').on("select2:select", function (e) {

    initCombobox(e.params.data.id);

    activeCombobox();

    changeCustomer();
  });

  $('#customer_id').on("select2:clear", function (e) {
    changeCustomer();
  });

  if (!is_create) {
    $('.select-customer').prop('disabled', true);

    initCombobox($('.select-customer').val());

    activeCombobox();
  }

  $("#combo_location_destination_ids").change(function () {
    var val = $(this).val();
    $("#location_destination_ids").val(val);
    $("#location_destination_id").val(val);
  });

  $("#combo_location_arrival_ids").change(function () {
    var val = $(this).val();
    $("#location_arrival_ids").val(val);
    $("#location_arrival_id").val(val);
  });

  let addCompleteModal = $("#add_complete");
  addCompleteModal.on("hide.bs.modal", function (e) {
    let entity = addCompleteModal.data("entity"),
      model = addCompleteModal.data("model"),
      button = addCompleteModal.data("button");

    switch (model) {
      case "customer":
        addCustomerComplete(entity, button);
        break;
      case "system-code-config":
        addSystemCodeConfigComplete(entity, button);
        break;
      default:
        return;
    }
  });

  function addCustomerComplete(entity, button) {
    let fullName = entity.full_name;
    let combo = button.closest(".input-group").find(".select-customer");

    combo
      .empty()
      .append(
        '<option value="' +
          entity.id +
          '" title="' +
          fullName +
          '">' +
          fullName +
          "</option>"
      )
      .val(entity.id)
      .trigger("change");
  }

  function addSystemCodeConfigComplete(entity, button) {
    let combo = button.closest(".input-group").find(".select-code-config");

    combo
      .empty()
      .append(
        '<option value="' +
          entity.id +
          '" title="' +
          entity.prefix +
          '">' +
          entity.prefix +
          "</option>"
      )
      .val(entity.id)
      .trigger("change");
  }
});

function addCompletedLoadingModel(model) {
  if (model === "customer") {
    Customer("#modal_add");
  }
}

function initCombobox(customer_id) {

  if (typeof customer_id === 'undefined') {
    customer_id = -1;
  }

  if (typeof cboSelect2 !== "undefined") {
    if (typeof urlLocation !== "undefined") {
      cboSelect2.location(urlLocation, ".select-location", false, false, 'Vui lòng chọn địa điểm', {customer_id: customer_id});
    }
    if (typeof urlCodeConfig !== "undefined") {
      cboSelect2.codeConfig(urlCodeConfig);
    }
    if (typeof comboClientUri !== "undefined") {
      cboSelect2.client(comboClientUri, ".select-client", {customer_id: customer_id});
    }
  }
}

function activeCombobox() {
  if ($('#customer_id').val() > 0) {
    $('.select-client').prop('disabled', false);
    $('.select-location').prop('disabled', false);
  }
}

function changeCustomer() {
  $('.select-client').val(null).trigger('change');
  $('.select-location').val(null).trigger('change');
}