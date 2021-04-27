/**
 * Theme: Minton Admin Template
 * Author: Coderthemes
 * Nestable Component
 */

!(function ($) {
  "use strict";

  var Nestable = function () {};

  (Nestable.prototype.updateOutput = function (e) {
    var list = e.length ? e : $(e.target),
      output = list.data("output");
    sendRequestNotLoading(
      {
        url: saveOrderUrl,
        type: "POST",
        data: {
          list: list.nestable("serialize"),
          type: $(this).data("type"),
        },
      },
      function (response) {}
    );
  }),
    //init
    (Nestable.prototype.init = function () {
      // activate Nestable for list 1
      $("#receipt_list")
        .nestable({
          group: 1,
          maxDepth: 1,
          // noDragClass: 'nodrag'
        })
        .on("change", this.updateOutput);

      $("#payment_list")
        .nestable({
          group: 1,
          maxDepth: 1,
          // noDragClass: 'nodrag'
        })
        .on("change", this.updateOutput);
    }),
    //init
    ($.Nestable = new Nestable()),
    ($.Nestable.Constructor = Nestable);
})(window.jQuery),
  //initializing
  (function ($) {
    "use strict";

    try {
      $.Nestable.init();
    } catch (e) {
      console.log(e);
    }

    $(".dd a").on("mousedown", function (event) {
      event.preventDefault();
      return false;
    });

    $(document).on("click", ".delete-cost", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var count = $("#body_content").find("tr").length;
      if (count <= 1) {
        generateCostItem(void 0);
      }
      $(this).parent("td").parent("tr:first").remove();
    });

    $(document).on("click", "#btn-plus", function (e) {
      e.preventDefault();
      e.stopPropagation();
      generateCostItem(void 0);
    });

    $("#switchery_is_display_driver").on("change", function () {
      var checked = $(this).is(":checked");
      if (checked || $(this).length == 0) {
        $("#is_display_driver").val("1");
      } else {
        $("#is_display_driver").val("0");
      }
    });

    function generateCostItem() {
      var $tableBody = $(".table-cost").find("tbody"),
        $trLast = $tableBody.find("tr:last");

      var $trNew = $trLast.clone();

      $trNew.find("td").each(function () {
        var el = $(this)
          .find(".mapping")
          .each(function (idx, element) {
            var id = $(element).attr("id") || null;
            if (id) {
              var index = "";
              id.replace(/\[(.+?)\]/g, function ($0, $1) {
                index = Number($1) + 1;
              });
              var name = "amount_list[" + index + "]";
              $(element).attr("id", name);
              $(element).attr("name", name);
              $(element).val(0);
            }
          });
      });
      $trLast.after($trNew);
    }
  })(window.jQuery);
