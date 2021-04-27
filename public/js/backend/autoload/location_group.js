$(function () {
    if (typeof cboSelect2 !== "undefined") {
        if (typeof urlLocation !== "undefined") {
            cboSelect2.location(urlLocation, '.select-location', false, true);
        }
    }
});