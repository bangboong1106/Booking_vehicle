$(function () {
    $('#check-all').on('click', function (e) {
        e.preventDefault();
        $('.checkbox.checkbox-circle input').prop('checked', true).removeAttr("disabled");
    });
    $('#uncheck-all').on('click', function (e) {
        e.preventDefault();
        $('.checkbox.checkbox-circle input').prop('checked', false);
        $('#form-table tbody tr td:nth-child(3)').nextAll('td').find('input').prop('checked', false).attr("disabled", true);
    });
    $('#form-table tbody tr td:nth-child(3) input').on('change', function () {
        let viewCheck = $(this),
            tdContainer = viewCheck.closest('td');
        if(this.checked) {
            tdContainer.nextAll('td').find('input').removeAttr("disabled");
        } else {
            tdContainer.nextAll('td').find('input').prop('checked', false).attr("disabled", true);
        }
    }).each(function () {
        if (!this.checked) {
            let viewCheck = $(this),
                tdContainer = viewCheck.closest('td');
            tdContainer.nextAll('td').find('input').prop('checked', false).attr("disabled", true);
        }
    });



    $('.accordion-body').on('hide.bs.collapse', function () {

        $('#btn-toggle-collapse').addClass('collapsed');

        let total = $('.accordion-toggle').length;

        let accordionCollapsed = ++$('.accordion-toggle.panel-title.collapsed').length;

        if (accordionCollapsed == total) {
            $('#btn-toggle-collapse').addClass('collapsed');
        }
    });

    $('.accordion-body').on('show.bs.collapse', function () {

        let total = $('.accordion-toggle').length;

        let accordionCollapsed = $('.accordion-toggle.panel-title.collapsed').length;

        if (accordionCollapsed == total) {
            $('#btn-toggle-collapse').addClass('collapsed');
        }

        if (accordionCollapsed == 0) {
            $('#btn-toggle-collapse').removeClass('collapsed');
        }

    });

    $('#btn-toggle-collapse').on('click', function () {
        let t = $(this);

        if (t.hasClass('collapsed')) {
            $('.accordion-body').collapse('show');
        } else {
            $('.accordion-body').collapse('hide');
        }
    });
});