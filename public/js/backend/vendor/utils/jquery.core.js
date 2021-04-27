/**
* Theme: Minton Admin Template
* Author: Coderthemes
* Module/App: Core js
*/

//portlets
!function($) {
    "use strict";

}(window.jQuery),

/**
 * Notifications
 */
function($) {
    "use strict";
}(window.jQuery),

/**
 * Components
 */
function($) {
    "use strict";

    var Components = function() {};

    //initializing tooltip
    Components.prototype.initTooltipPlugin = function() {
        $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip()
    },

    //initializing popover
    Components.prototype.initPopoverPlugin = function() {
        $.fn.popover && $('[data-toggle="popover"]').popover()
    },

    /* -------------
     * Form related controls
     */
    //switch
    Components.prototype.initSwitchery = function() {
        $('[data-plugin="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });
    },



    //initilizing
    Components.prototype.init = function() {
        var $this = this;
        this.initTooltipPlugin(),
        this.initPopoverPlugin(),
            // this.initNiceScrollPlugin(),
            // this.initCustomModalPlugin(),
            // this.initRangeSlider(),
            this.initSwitchery()
        // this.initMultiSelect(),
        // this.initPeityCharts(),
        //creating portles
        // $.Portlet.init();
    },

    $.Components = new Components, $.Components.Constructor = Components

}(window.jQuery),
    //initializing main application module
function($) {
    "use strict";
    $.Components.init();
}(window.jQuery);




