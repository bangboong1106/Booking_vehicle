let requestTimeout = {},
    CHECK_REQUEST_TIMEOUT = 1;
sendRequest = function (options, callbackFunc) {
    let now = new Date(),
        loading = options.loading !== false;
    if (!checkRequest(options.url, now)) {
        return false;
    }
    let ajaxOptions = {
        error: function (response, exception) {
            let msg;
            try {
                let response = JSON.parse(response.responseText);
                switch (true) {
                    case responsex.message !== '':
                        msg = responsex.message;
                        break;
                    case exception === 'parsererror':
                        msg = 'Requested JSON parse failed.';
                        break;
                    case exception === 'timeout':
                        msg = 'Time out error.';
                        break;
                    case exception === 'abort':
                        msg = 'Ajax request aborted.';
                        break;
                    default:
                        msg = 'Uncaught Error.\n' + response.responseText;
                        break;
                }
            } catch (e) {
                msg = 'System error !';
            }
            let res = {
                data: {},
                code: response.status,
                ok: false,
                message: msg
            };
            return callbackFunc(res);
        },
        success: function (response) {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            return callbackFunc(response);
        },
        type: 'post',
        dataType: 'text',
        crossDomain: true,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': $('input[name="_token"]').val()
        },
        beforeSend: function () {
            if (loading) showLoading(options.overlay);
        },
        complete: function () {
            if (loading) hideLoading(options.overlay);
        }
    };
    $.extend(ajaxOptions, options);
    $.ajax(ajaxOptions);
};
sendRequestNotLoading = function (options, callbackFunc) {
    options.beforeSend = function () {
    };
    options.complete = function () {
    };
    sendRequest(options, callbackFunc);
};
checkRequest = function (url, time) {
    if (!requestTimeout._g(url, false)) {
        requestTimeout._s(url, time);
        return true;
    }
    let requestOldTime = requestTimeout._g(url, 0);
    if (time - requestOldTime >= CHECK_REQUEST_TIMEOUT) {
        requestTimeout._s(url, time);
        return true;
    }
    return false;
};

function example() {

    sendRequest({
        url: 'test',
        type: 'GET',
        data: {},
    }, function (response) {
        if (!response.ok) {
            return showErrorFlash(response.message);
        }
        console.log(response.data);
    });
}