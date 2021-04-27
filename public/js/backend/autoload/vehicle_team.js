$(document).ready(function () {
    registerSelectDriver($('#partner_id').val());

    $('#partner_id').on('change', function (e) {
        registerSelectDriver(this.value);
    });
});

function registerSelectDriver(partnerId) {

    if (typeof cboSelect2 !== "undefined") {
        if (typeof driverDropdownUri !== "undefined") {
            cboSelect2.driver(driverDropdownUri, '', '', {all: '' , partner_id: partnerId});
        }
    }

    if (typeof createDriverQuickSearch != "undefined") {
        var driverQuickSearch = createDriverQuickSearch();
        if (typeof searchDriverExceptIds != "undefined") {
            var config = {};
            config.exceptIds = searchDriverExceptIds;
            config.searchElement = 'team-driver-search';
            config.searchType = 'element';
            config.partnerId = partnerId;

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
            primaryDriverConfig.partnerId = partnerId;

            driverQuickSearch(primaryDriverConfig).init();
        }
    }
}