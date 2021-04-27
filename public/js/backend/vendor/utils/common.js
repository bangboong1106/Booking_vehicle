function showLoading(overlay) {
    if (typeof overlay !== 'undefined' && overlay !== '') {
        $(overlay).LoadingOverlay("show", {zIndex: 9999, image: baseUrl + '/css/backend/img/loader.gif'});
    } else {
        $.LoadingOverlay("show", {zIndex: 9999, image: baseUrl + '/css/backend/img/loader.gif'});
    }
}

function hideLoading(overlay) {
    if (typeof overlay !== 'undefined' && overlay !== '') {
        $(overlay).LoadingOverlay("hide");
    } else {
        $.LoadingOverlay("hide");
    }
}

function showSuccessFlash(messages) {
    var html = '<hr><div class="row"><div class="col-md-12"><ul class="col-md-12 alert alert-success">';
    if (typeof messages === 'string') {
        html += '<li><i class="fa fa-check"></i><strong>' + messages + '</strong></li>';
    } else {
        messages.forEach(function (e) {
            html += '<li><i class="fa fa-check"></i><strong>' + e + '</strong></li>';
        });
    }
    html += '</ul></div></div>';
    $('#success_msg').html(html);
}

function showErrorFlash(messages) {
    var html = '<div class="alert alert-danger"><ul>';
    if (typeof messages === 'string') {
        html += '<li>' + messages + '</li>';
    } else {
        messages.forEach(function (e) {
            html += '<li>' + e + '</li>';
        });
    }

    html + '</ul></div>';
    $('#error_msg').html(html);
    scrollToTop();
}

function clearFlash() {
    clearSuccessFlash();
    clearErrorFlash();
}

function clearErrorFlash() {
    $('#error_msg').html('');
}

function clearSuccessFlash() {
    $('#success_msg').html('');
}

function redirect(url) {
    //todo validate limit redriect by url and tab session
    return url == '' ? window.location.reload() : window.location.href = url;
}

function isUrl(url) {
    url = url.replace('https://', '').replace('http://');
    var currentUrl = getCurrentUrl();
    return currentUrl.indexOf(url) !== -1;
}

function previewFile(input) {
    if (!input.files || !input.files[0]) {
        return false;
    }
    var previewId = '#preview-file-' + $(input).attr('name');

    if (!validateFile(input)) {
        input.value = '';
        $(previewId).find('img').remove();
        $(previewId).find('input[type="hidden"]').val('');
        return false;
    }
    clearFlash();

    var reader = new FileReader();
    reader.onload = function (e) {
        var imgWrapper = $(previewId);
        var imgName = input.files[0].name !== undefined ? input.files[0].name : '';
        // create temporary img tag
        var img = $(document.createElement('img'));
        img.attr('src', e.target.result);
        img.attr('height', '250');

        // remove img exist
        imgWrapper.find('img').remove();
        // change file name upload
        imgWrapper.append(img);
        imgWrapper.closest('form').find('#file-name').empty().html(imgName);
    };
    reader.readAsDataURL(input.files[0]);

}

function validateFile(input) {
    var sizeAllow = input.getAttribute('size');
    var extAllow = input.getAttribute('ext');
    var extsAllow = extAllow.split(',');
    sizeAllow = sizeAllow.split(',');
    var minSize = parseFloat(sizeAllow[0]);
    var maxSize = parseFloat(sizeAllow[1]);

    var file = input.files[0];
    var size = file.size / 1024 / 1024;
    var extension = input.value.substr(input.value.lastIndexOf('.') + 1).toLowerCase();
    var label = input.getAttribute('data-label');
    // file type
    if (extension.length <= 0 || extsAllow.indexOf(extension) === -1) {
        var msg = validateFileMsg._g('mimes').replace(':attribute', label).replace(':values', extAllow);
        showErrorFlash(msg);
        return false;
    }
    // size
    if (size < minSize) {
        var msg = validateFileMsg._g('min').replace(':attribute', label).replace(':min', minSize);
        showErrorFlash(msg);
        return false;
    }
    if (size > maxSize) {
        var msg = validateFileMsg._g('max').replace(':attribute', label).replace(':max', maxSize);
        showErrorFlash(msg);
        return false;
    }

    return true;
}

function fillForm(val) {
    if (val === undefined) {
        val = 1;
    }
    $('form').first().find('input[type!="hidden"],select,textarea').val(val).trigger('change');
}

var GoogleMap = {
    enabledDragMarker: true,
    enableClickMarker: true,
    marker: {},
    createMapWithMarker: function (elementId, latLngWrapper, latitude, longitude) {
        // create google map
        var map = new google.maps.Map(document.getElementById(elementId), {
            zoom: 12,
            center: new google.maps.LatLng(latitude, longitude),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        // create marker
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            draggable: true
        });
        // change marker position when drag or click on map
        GoogleMap.dragMarker(latLngWrapper, marker);
        GoogleMap.clickToChangeMarker(latLngWrapper, map);
        map.setCenter(marker.position);
        marker.setMap(map);
        // save old marker
        GoogleMap.marker = marker;
    },
    dragMarker: function (element, marker) {
        if (!GoogleMap.enabledDragMarker || element.length <= 0) {
            marker.setDraggable(false);
            return;
        }
        google.maps.event.addListener(marker, 'dragend', function (event) {
            // change display latitude, longitude
            element.val(event.latLng.lat().toFixed(6) + ',' + event.latLng.lng().toFixed(6));
        });
    },
    clickToChangeMarker: function (element, map) {
        if (!GoogleMap.enableClickMarker || element.length <= 0) {
            return;
        }
        google.maps.event.addListener(map, 'click', function (event) {
            // remove old marker
            GoogleMap.removeMarker();
            // add new marker
            GoogleMap.addMarker(map, element, event.latLng);
            // change display latitude, longitude
            element.val(event.latLng.lat().toFixed(6) + ',' + event.latLng.lng().toFixed(6));
        });
    },
    addMarker: function (map, latLngWrapper, location) {
        var marker = new google.maps.Marker({
            position: location,
            draggable: true,
            map: map
        });
        GoogleMap.dragMarker(latLngWrapper, marker);
        marker.setMap(map);
        // save old marker
        GoogleMap.marker = marker;
    },
    removeMarker: function () {
        GoogleMap.marker.setMap(null);
    }
};

function scrollToTop() {
    window.scrollTo(0, 0);
}

setTimeout(displayHideNotification, 3000);

function slideMessageToTop(param) {
    if (param != null) {

        let pos = 0;
        let id = setInterval(frame, 20);

        function frame() {
            if (pos === 120) {
                clearInterval(id);
            } else {
                pos++;
                param.style.top = '-' + pos + 'px';
            }
        }
    }
}

function displayHideNotification() {
    let error = document.querySelector('#error_msg_main');
    let success = document.getElementById('success_msg_main');
    if (error != null) {
        slideMessageToTop(error);
    }

    if (success != null) {
        slideMessageToTop(success);
    }
}

// Tiện ích: Ấn enter submit form
$(function(){
    let issetForm = $('body').find('form');
    let issetSubmitBtn = $('body').find('.submit-button .submit-btn');
    let issetConfirmForm = $('body').find('#confirm_form button');

    if (issetForm.length > 0 && (issetSubmitBtn.length > 0 || issetConfirmForm.length > 0)) {
        $('body').keypress(function (e) {
            if (e.which == 13 && ! ($("input").is(":focus") || $('.form-control').is(":focus") || $('textarea').is(":focus")) ) {
                let btnSubmit = issetSubmitBtn.length > 0 ? $('.submit-btn') : $('#confirm_form button[type="submit"]');
                btnSubmit.unbind().trigger('click');
            }
        });
    }
});