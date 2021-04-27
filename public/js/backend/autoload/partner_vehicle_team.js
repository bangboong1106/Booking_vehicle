$(document).ready(function () {
    if (typeof cboSelect2 !== "undefined") {
        if (typeof driverDropdownUri !== "undefined") {
            cboSelect2.driver(driverDropdownUri, '', '', {all: 1, partner_id: $('#partner_id').val()});
        }
    }

    if (typeof createDriverQuickSearch != "undefined") {
        var driverQuickSearch = createDriverQuickSearch();
        if (typeof searchDriverExceptIds != "undefined") {
            var config = {};
            config.exceptIds = searchDriverExceptIds;
            config.searchElement = 'team-driver-search';
            config.searchType = 'element';
            config.partnerId = $('#partner_id').val();
            driverQuickSearch(config).init();
        }
        if (typeof primaryDriverExceptIds != "undefined") {
            var primaryDriverConfig = {};
            primaryDriverConfig.exceptIds = primaryDriverExceptIds;
            primaryDriverConfig.searchElement = 'primary-driver-search-wrap';
            primaryDriverConfig.searchType = 'element';
            primaryDriverConfig.tableElement = 'table_primary_drivers';
            primaryDriverConfig.modalElement = 'primary_driver_modal';
            primaryDriverConfig.buttonElement = 'btn-primary-driver';
            primaryDriverConfig.partnerId = $('#partner_id').val();

            driverQuickSearch(primaryDriverConfig).init();
        }
    }
});