/**
 * Theme: Minton Admin Template
 * Author: Coderthemes
 * Nestable Component
 */

!function ($) {
    "use strict";

    var Nestable = function () {
    };

    Nestable.prototype.updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }

        $('#vehicle_group').toggleClass('drag_disabled drag_enabled');
    },
        //init
        Nestable.prototype.init = function () {
            // activate Nestable for list 1
            var nestable = $('#vehicle_group').nestable({
                group: 1,
                noDragClass: 'nodrag'
            }).on('change', this.updateOutput);

            // nestable.on('dragStart', function (e) {
            //     e.preventDefault();
            //     return false;
            // });
        },
        //init
        $.Nestable = new Nestable, $.Nestable.Constructor = Nestable
}(window.jQuery),

//initializing 
    function ($) {
        "use strict";
        $.Nestable.init()
    }(window.jQuery);

$(function () {
    $(document).on("change", "#partner_id", function (e) {
        e.preventDefault();
        var el = $(this);
        el.prop("disabled", true);
        setTimeout(function () {
            el.prop("disabled", false);
        }, 1500);
        var url = $(this).data("default");

        var data = {
            partner_id: $("#partner_id").val(),
        };
        sendRequest(
            {
                url: url,
                type: "POST",
                data: data,
            },
            function (response) {
                var content = response.content;
                $('#vehicle_group').html(content);
            }
        );
    });
});