$(function () {
    let contactTable;
    let selectContact;
    let modal = $('#contact_modal');

    $(document).on('click', '.contact-search', function () {
        let self = $(this);
        selectContact = self;

        modal.modal();

        var type = self.attr('data-type');
        if (type == null) {
            type = 'single';
        }

        if (!$.fn.dataTable.isDataTable(contactTable)) {
            contactTable = $('#table_contacts').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                language: {
                    search: "Tìm kiếm :",
                    lengthMenu: "Hiển thị _MENU_ bản ghi",
                    info: "Hiển thị _START_ - _END_ trong _TOTAL_ kết quả",
                    infoEmpty: "Không có dữ liệu",
                    loadingRecords: "Đang tải...",
                    zeroRecords: "Không có dữ liệu",
                    sInfoFiltered: "(được lọc từ _MAX_ mục)",
                    select: {
                        rows: {
                            _: "Đã chọn %d dòng",
                            0: "",
                            1: "Đã chọn 1 dòng"
                        }
                    },
                    emptyTable: "Không có dữ liệu trong bảng",
                    paginate: {
                        first: "Đầu",
                        previous: "Trước",
                        next: "Tiếp",
                        last: "Cuối"
                    },
                    processing: '<div class="loader"><img src="' + publicUrl + '/css/backend/img/loader.gif"></div>'
                },
                ajax: contactUri,
                select: type,
                columns: [
                    {data: 'id'},
                    {data: 'contact_name'},
                    {data: 'phone_number'},
                    {data: 'email'},
                    {data: 'full_address'}
                    //{data: 'active'}
                ],
                'order': [[0, 'asc']],
                "createdRow": function (row, data, index) {
                    if ($.inArray(data['id'], self.closest(".select2-bootstrap-prepend").find(".select-contact").val()) > -1) {
                        contactTable.rows(index).select();
                    }
                },
            });
        } else {
            if (typeof contactTable.columns().checkboxes === 'undefined') {
                return;
            }
            contactTable.columns().checkboxes.deselectAll();
            contactTable.ajax.reload();
        }
    });

    modal.on('show.bs.modal', function () {
        $('html').css('overflow-y', 'hidden');
    }).on('hide.bs.modal', function () {
        $('html').css('overflow-y', 'auto');
    });

    $(document).on('dblclick', '#table_contacts_wrapper tr', function (e) {
        addValueSelected(e);
    });

    $('#btn-contact').on('click', function (e) {
        addValueSelected(e);
    });

    function addValueSelected(e) {
        e.preventDefault();
        var contacts = [];

        var rows_selected = contactTable.rows({selected: true}).data();
        // var rows_selected = contactTable.column(0, { search: 'applied' }).checkboxes.selected();
        $.each(rows_selected, function (index, item) {
            contacts.push({
                id: item.id,
                full_address: item.full_address,
                phone_number: item.phone_number,
                email: item.email,
                contact_name: item.contact_name,
                location_id: item.location_id,
                location_title: item.location_title
            });
        });

        contactSearchCallback(selectContact, contacts);
        modal.modal('hide');
    }

    function addLocationComplete(entity, button) {
        var locationSelect = button.closest('.input-group').find('.select-location');

        locationSelect.empty().append('<option value="' + entity.id + '" title="' + entity.title + '">' + entity.title + '</option>').val(entity.id).trigger('change');
    }
});

function contactSearchCallback(self, contacts) {
    let typeContact = self.attr("type-contact"),
        phoneNumber = $('#contact_mobile_no_' + typeContact),
        contactName = $('#contact_name_' + typeContact),
        email = $('#contact_email_' + typeContact),
        selectedAddress = self.closest('.card-body').find('.location-order').first().find('.select-location'),
        saveContact = $('#auto-create-template_' + typeContact).first();

    contacts.forEach(function (item) {
        phoneNumber.val(item.phone_number);
        contactName.val(item.contact_name);

        email.val(item.email);
        if (item.location_id !== null)
            selectedAddress.select2("trigger", "select", {
                data: {id: item.location_id, title: item.location_title}
            });
        if (saveContact.val() !== '0') {
            saveContact.parent().find("span.switchery").trigger('click');
            saveContact.val(0);
        }
    });

    self = null;
}
