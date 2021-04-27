$(function () {
    let provinceContainer = $('#province_select'),
        districtContainer = $('#district_select'),
        wardContainer = $('#ward_select'),
        addressContainer = $("#address");
    provinceContainer.change(function () {
        load(this, 'province_id', '#district_select', urlDistrict, true);
        checkValue(this);
    });
    districtContainer.change(function () {
        load(this, 'district_id', '#ward_select', urlWard, false);
        checkValue(this);
    });

    wardContainer.on('change', function () {
        checkValue(this);
    });

    function load(element, key, result, url, province) {
        let container = $(result),
            data = {},
            overlay = addressContainer.closest('#map_modal').length > 0 ? '#map_modal .modal-body' : '';
        data[key] = $(element).val();
        sendRequest({
            url: url,
            type: 'GET',
            data: data,
            overlay: overlay
        }, function (response) {
            if (!response.ok) {
                return showErrorFlash(response.message);
            } else {
                container.html(response.data.content);
                if (districtText != null && districtText !== '' && container.selector === "#district_select") {
                    districtContainer.select2("trigger", "select", {
                        data: {
                            id: $("#district_select option:contains('" + districtText + "')").val(),
                            title: districtText
                        }
                    });
                }

                if (province && wardText != null && wardText !== '' && container.selector === "#ward_select") {
                    $('#ward_select').select2("trigger", "select", {
                        data: {id: $("#ward_select option:contains('" + wardText + "')").val(), title: wardText}
                    });
                }
                if (!province) {
                    $('#ward_select').select2("trigger", "select", {
                        data: {id: $("#ward_select option:contains('" + wardText + "')").val(), title: wardText}
                    });
                    getLocation();
                }

                if (key === 'district_id' && container.find('option').length === 1) {
                    container.closest('.form-group').find('.select2-container').removeClass('is-invalid');
                    container.closest('.form-group').find('.invalid-feedback').addClass('d-none');
                }
            }
        });
    }

    let provinceText = '',
        districtText = '',
        wardText = '',
        addressText = '';
    /**
     * Tự gán giá trị cho địa chỉ
     */
    $('#address_entered').on('keyup paste', function (e) {
        if (e.type === 'keyup'){
            if (e.keyCode === 13) {
                changeValue(this);
            }
        }
        else {
            if (e.type === 'change') {
                changeValue(this);
            }
            else {
                if (e.type === 'paste') {
                    $(this).unbind('change').bind('change', function () {
                        changeValue(this);
                    });
                }
            }
        }

    }).on('click', init);

    function changeValue(e) {
        let reg1 = /\s*([0-9.-]+)\s*,\s*([0-9.-]+)\s*/g,
            geocode = new google.maps.Geocoder;
        let $this = $(e).val();
        let resultReg1 = $this.match(reg1);
        if (resultReg1 != null) {
            var latlngStr = resultReg1[0].split(',', 2);
            var latlng = new google.maps.LatLng(parseFloat(latlngStr[0]), parseFloat(latlngStr[1]));
            geocode.geocode({'latLng': latlng}, function (results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        $(this).val(results[0].formatted_address).trigger('change');
                        changeValue($('#address_entered'));
                    }
                }
            });
            resultReg1 = null;
        } else {
            let arr = $this.split(', '),
                lengthArr = arr.length,
                provinceId = null;
            addressText = '';
            provinceText = '';
            districtText = '';
            wardText = '';
            for (let i = lengthArr; i >= 0; i--) {
                let tmp = change_alias(arr[i]);
                $("#province_select option").each(function (e) {
                    let option = $(this),
                        title = change_alias(option.text());

                    if (title === tmp) {
                        provinceId = option.val();
                        return false;
                    }
                });

                if (provinceId) {
                    provinceText = arr[i];
                    if (i - 1 >= 0) {
                        districtText = arr[i - 1];
                    }
                    if (i - 2 >= 0) {
                        wardText = arr[i - 2];
                    }
                    if (i - 3 >= 0) {
                        for (let j = 0; j < i - 2; j++) {
                            if (j > 0) {
                                addressText += ', ';
                            }
                            addressText += arr[j];
                        }
                    }
                    break;
                }
                if (i === 0 && provinceText === '') {
                    addressText = $this;
                }
            }
            provinceText = provinceText.replace("Tỉnh ", "");
            provinceText = provinceText.replace("tỉnh ", "");
            provinceText = provinceText.replace("Thành phố ", "");
            provinceText = provinceText.replace("Thành Phố ", "");
            provinceText = provinceText.replace("thành phố ", "");
            provinceText = provinceText.replace("Tp. ", "");
            provinceText = provinceText.replace("TP. ", "");
            provinceText = provinceText.replace("tp. ", "");
            districtText = districtText.replace("Thành phố ", "");
            districtText = districtText.replace("Thành Phố ", "");
            districtText = districtText.replace("thành phố ", "");
            districtText = districtText.replace("Tp. ", "");
            districtText = districtText.replace("TP. ", "");
            districtText = districtText.replace("tp. ", "");
            districtText = districtText.replace("Huyện ", "");
            districtText = districtText.replace("huyện ", "");
            districtText = districtText.replace("H. ", "");
            districtText = districtText.replace("h. ", "");
            districtText = districtText.replace("Q. ", "");
            districtText = districtText.replace("q. ", "");
            districtText = districtText.replace("Quận ", "");
            districtText = districtText.replace("quận ", "");
            districtText = districtText.replace("Thị xã ", "");
            districtText = districtText.replace("thị xã ", "");
            districtText = districtText.replace("tx. ", "");
            districtText = districtText.replace("Tx. ", "");
            districtText = districtText.replace("TX. ", "");
            wardText = wardText.replace("Xã ", "");
            wardText = wardText.replace("xã ", "");
            wardText = wardText.replace("Phường ", "");
            wardText = wardText.replace("phường ", "");
            wardText = wardText.replace("P. ", "");
            wardText = wardText.replace("p. ", "");
            wardText = wardText.replace("Thị trấn ", "");
            wardText = wardText.replace("thị trấn ", "");
            wardText = wardText.replace("tt. ", "");
            wardText = wardText.replace("Tt. ", "");
            wardText = wardText.replace("TT. ", "");

            if (provinceText != null && provinceText !== '') {
                provinceContainer.select2("trigger", "select", {
                    data: {id: provinceId, title: provinceText}
                });
            }
            $('#address').val(addressText);
        }
    }

    // Gợi ý của gg map
    var input = document.getElementById('address_entered');

    function init() {
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            changeValue($('#address_entered'));
            return true;
        });
    }

    wardContainer.change(function () {
        getLocation();
    });
    addressContainer.on("change paste keyup", function () {
        location = addressContainer.val() + ", " + wardContainer.find('option:selected').text() + ", "
            + districtContainer.find('option:selected').text() + ", "
            + provinceContainer.find('option:selected').text();
        binAddressToForm();
    });
    $("#location_submit").on('click', function () {
        let result = binDataToForm();

        if (!result) {
            return false;
        }
    });

    let map;
    let markers = [];
    let location, lat, lng;

    function initMap() {
        let latlng;
        if (currentLatitude !== '0') {
            latlng = new google.maps.LatLng(currentLatitude, currentLongitude);
        } else {
            latlng = new google.maps.LatLng(21.0031177, 105.82014079999999);
        }

        map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 15
        });
        getLocation(true);
    }

    if ($('#map').length > 0) {
        if(typeof google != undefined){
            google.maps.event.addDomListener(window, 'load', initMap);
        }
    }

    function getLocation(first) {
        var address = addressContainer.val() + ", " + wardContainer.find('option:selected').text() + ", "
            + districtContainer.find('option:selected').text() + ", "
            + provinceContainer.find('option:selected').text();

        if (first) {
            var latlngShow = new google.maps.LatLng(currentLatitude, currentLongitude);
            var marker = new google.maps.Marker({
                map: map,
                position: latlngShow,
                draggable: true,
                anchorPoint: new google.maps.Point(0, -29)
            });
            markers.push(marker);

            google.maps.event.addListener(marker, 'dragend', function (evt) {
                lat = evt.latLng.lat();
                lng = evt.latLng.lng();
                binAddressToForm();
            });

            location = address;
        } else {
            let geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function (results, status) {

                if (status === google.maps.GeocoderStatus.OK) {
                    clearMarkers();
                    // Center map on location
                    map.setCenter(results[0].geometry.location);

                    // Add marker on location
                    var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        draggable: true,
                        anchorPoint: new google.maps.Point(0, -29)
                    });
                    markers.push(marker);
                    google.maps.event.addListener(marker, 'dragend', function (evt) {
                        lat = evt.latLng.lat();
                        lng = evt.latLng.lng();
                        binAddressToForm();
                    });

                    location = results[0].formatted_address;
                    lat = results[0].geometry.location.lat();
                    lng = results[0].geometry.location.lng();
                    binAddressToForm();
                }
            });
        }
    }

    function clearMarkers() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null)
        }
        markers = [];
    }

    function createAddress() {
        return location;
    }

    function binAddressToForm() {
        $('#location').val(createAddress());
    }

    function binDataToForm() {
        let address_customer = createAddress(),
            province_select = provinceContainer.val(),
            district_select = districtContainer.val(),
            ward_select = wardContainer.val(),
            address = addressContainer.val(),
            bind = false,
            selectProvince = provinceContainer.closest('div').find('.select2'),
            selectDistrict = districtContainer.closest('div').find('.select2'),
            selectWard = wardContainer.closest('div').find('.select2');

        if (province_select === '') {
            selectProvince.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').removeClass('d-none');
            bind = true;
        }

        if (district_select === '') {
            selectDistrict.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').removeClass('d-none');
            bind = true;
        }

        if ((ward_select === '' && selectDistrict.find('option').length === 1) || (ward_select === '' && selectWard.find('option').length > 1)) {
            selectWard.addClass('is-invalid').closest('.form-group').find('.invalid-feedback').removeClass('d-none');
            bind = true;
        }

        if (bind) {
            return false;
        }

        //bind location.js
        $('#current_location').val(address_customer);
        $('#latitude').val(lat);
        $('#longitude').val(lng);
        $('#province_id').val(province_select);
        $('#district_id').val(district_select);
        $('#ward_id').val(ward_select);
        $('#address-hidden').val(address);

        //bind order.js
        let addModal = $('#modal_add'),
            customerAddress = $('#customer_address');
        customerAddress.val(address_customer);
        addModal.find('.address-input').val(address_customer);
        addModal.find('.latitude').val(lat);
        addModal.find('.longitude').val(lng);
        addModal.find('.province_id').val(province_select);
        addModal.find('.district_id').val(district_select);
        addModal.find('.ward_id').val(ward_select);

        // bind customer.js
        if (customerAddress.length > 0) {
            customerAddress.closest('.group-address').removeClass('not-address');
        }

        addModal.find('.address-hidden').val(address);
        return true;
    }

    function isEmpty(value) {
        return typeof value === 'string' && !value.trim() || typeof value === 'undefined' || value === null;
    }

    function change_alias(alias) {
        if (typeof alias === 'undefined') {
            return '';
        }
        
        var str = alias;
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
        str = str.replace(/đ/g,"d");
        str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
        str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
        str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
        str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
        str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
        str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
        str = str.replace(/Đ/g, "D");
        str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
        str = str.replace(/ + /g," ");
        str = str.toLowerCase();
        str = str.trim();
        return str;
    }

    function checkValue(e) {
        let _this = $(e);
        if (_this.val() !== '') {
            _this.closest('.form-group').find('.select2-container').removeClass('is-invalid');
            _this.closest('.form-group').find('.invalid-feedback').addClass('d-none');
        }
    }

    $(document).on('click', '#clear_location', function () {
        clearDataToForm();
    });

    function clearDataToForm() {
        $('#address_entered').val('');
        $('#address').val('');
        $('#location').val('');
        provinceContainer.val('').select2({data: [{id: '', text: ''}]});

        //clear location.js
        $('#latitude').val("");
        $('#longitude').val("");
        $('#current_location').val('');

        //clear order.js
        let addModal = $('#modal_add');
        addModal.find('.address-input').val("");
        addModal.find('.latitude').val("");
        addModal.find('.longitude').val("");
        addModal.find('.province_id').val("");
        addModal.find('.district_id').val("");
        addModal.find('.ward_id').val("");

        let customerAddress = $('#customer_address');
        if (customerAddress.length > 0) {
            customerAddress.val('');
            customerAddress.closest('.group-address').addClass('not-address');
            districtContainer.val('').select2({data: [{id: '', text: ''}]});
            wardContainer.val('').select2({data: [{id: '', text: ''}]});

            $('.province_id').val("");
            $('.district_id').val("");
            $('.ward_id').val("");
        }

        clearMarkers();
        map.setCenter(new google.maps.LatLng(21.0031177, 105.82014079999999));
    }
});