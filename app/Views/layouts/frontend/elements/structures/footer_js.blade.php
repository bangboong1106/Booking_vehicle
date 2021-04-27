<script src="{{ public_url('css/frontend/ceta/script_main_js.js') }}" type="text/javascript"></script>
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/6238532.js"></script>

<script>
    $(function() {
        $(".more-info").on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: ($('#content-home').offset().top - 200)
            }, 1000);
        });
    });
</script>
