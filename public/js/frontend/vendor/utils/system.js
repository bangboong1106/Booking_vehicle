$(document).ready(function () {
    $('form').on('submit', function (e) {
        e.preventDefault();
        $(this).attr('show-loading') == 1 ? showLoading() : null;
        $(this).off('submit').submit();
    });
    // upload image
    $(".input-image").change(function () {
        previewFile(this);
    });
    //date picker
    $('.datepicker').datepicker({
        todayHighlight: true,
        autoclose: true,
        format: 'yyyy-mm-dd',
        language: 'vi'
    });
    // time picker
    $('.timepicker').timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: false,
        defaultTime: ''
    });
    // phone format
    $(".telephone").mask("999-999-9999");
});

//modal delete confirm
$(document).ready(function () {
    //delete
    $('.delete-action').on('click', function () {
            var href = $(this).data('action');
            $('#del_form').attr('action', href);
        }
    );
    // mass destroy
    $('.mass-destroy-btn').on('click', function () {
        var href = $(this).data('action');
        $('#mass_del_form').attr('action', href);
        pushItemToDestroy();
    });
    $("#check_all_mass_destroy").click(function () {
        $(".mass-destroy").prop('checked', $(this).prop('checked')).trigger('change');
    });
    $(".mass-destroy").on('change', function () {
        if ($(".mass-destroy:checked").length > 0) {
            $('.mass-destroy-btn').removeClass('disabled');
        } else {
            $('.mass-destroy-btn').addClass('disabled');
        }
    });

    function pushItemToDestroy() {
        var ids = [];
        $(".mass-destroy:checked").each(function () {
            ids.push($(this).val());
        });
        $('#mass_destroy_id').val(ids.join(','));
    }

    // close
    $('.close-parent-modal').on('click', function () {
        var parent = $(this).data('parent-modal');
        $('#' + parent).find('.close').click();
    });
});
//  sorting
$('.sorting').on('click', function (e) {
    e.preventDefault();
    var url = $(this).data('url');
    if (!url) {
        return false;
    }
    return window.location.href = url;
});

// /reset btn

$('a.reset').on('click', function (e) {
    e.preventDefault();
    var form = $(this).closest('form');
    var href = $(form).attr('action');
    return window.location.href = href;
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})