$(document).ready(function () {
    if (typeof cboSelect2 != "undefined") {
        if (typeof urlCustomer !== 'undefined') {
            cboSelect2.customer(urlCustomer, '.select-customer');

        }
    }

    if (typeof createCustomerQuickSearch != "undefined") {
        var quickSearch = createCustomerQuickSearch();
        if (typeof searchCustomerExceptIds != "undefined") {
            var config = {};
            config.exceptIds = searchCustomerExceptIds;
            quickSearch(config).init();
        }
    }
});