<script>
    jQuery(document).ready(function(){

        $("<?= $validator['selector']; ?>").each(function() {
            $(this).validate({
                errorElement: 'span',
                errorClass: 'invalid-feedback',

                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length ||
                        element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                        error.insertAfter(element.parent());
                        // else just place the validation message immediately after the input
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    let e = $(element);
                    if (e.hasClass('select2-hidden-accessible')) {
                        e.parent().find('.select2-container').removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }

                    e.closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
                },

                <?php if (isset($validator['ignore']) && is_string($validator['ignore'])): ?>

                ignore: "<?= $validator['ignore']; ?>",
                <?php endif; ?>


                unhighlight: function(element) {
                    let e = $(element);
                    if (e.val() === null || e.val() === '') {
                        return false;
                    }
                    if (e.hasClass('select2-hidden-accessible')) {
                        e.parent().find('.select2-container').removeClass('is-invalid').addClass('is-valid');
                        return false;
                    }

                    e.closest('.form-control').removeClass('is-invalid').addClass('is-valid');
                },

                success: function (element) {
                    let e = $(element);
                    e.closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
                },

                focusInvalid: true, // do not focus the last invalid input
                <?php if (config('jsvalidation.focus_on_error')): ?>
                invalidHandler: function (form, validator) {
                    if (!validator.numberOfInvalids())
                        return;
                },
                <?php endif; ?>

                rules: <?= json_encode($validator['rules']); ?>
            });
        });
    });
</script>
