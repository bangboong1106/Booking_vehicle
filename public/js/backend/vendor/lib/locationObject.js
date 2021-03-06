;(function () {
    function OneLogLocation() {
        let formContainer, mapShow, provinceContainer, districtContainer, wardContainer, addressContainer,
            input,
            provinceText = '',
            districtText = '',
            wardText = '',
            addressText = '',
            _this = this,
            map,
            markers = [],
            fullAddress,
            location,
            lat, lng;

        this.load = function(element, key, result, url, province) {
            let container = $(result),
                data = {},
                overlay = addressContainer.closest('#modal_add').length > 0 ? '#modal_add .modal-body' : '';
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
                    if (districtText != null && districtText !== '' && result === "#district_id") {
                        districtContainer.select2("trigger", "select", {
                            data: {
                                id: $("#district_id option:contains('" + districtText + "')").val(),
                                title: districtText
                            }
                        });
                    }

                    if (province && wardText != null && wardText !== '' && result === "#ward_id") {
                        $('#ward_id').select2("trigger", "select", {
                            data: {id: $("#ward_id option:contains('" + wardText + "')").val(), title: wardText}
                        });
                    }

                    if (!province) {
                        wardContainer.select2("trigger", "select", {
                            data: {id: $("#ward_id option:contains('" + wardText + "')").val(), title: wardText}
                        });
                        _this.getLocation();
                    }
                }
            });
        };

        // T??m ki???m ?????a ch???
        this._searchAddress = function () {
            formContainer.find('#address_pac').on('keyup paste', function (e) {
                if (e.type === 'keyup'){
                    if (e.keyCode === 13) {
                        _this.changeValue(this);
                    }
                }
                else {
                    if (e.type === 'change') {
                        _this.changeValue(this);
                    }
                    else {
                        if (e.type === 'patse') {
                            $(this).unbind('change').bind('change', function () {
                                _this.changeValue(this);
                            });
                        }
                    }
                }
            });

            wardContainer.change(function () {
                _this.getLocation();
            });

            addressContainer.on("change paste keyup", function () {
                let wardSelectedText = wardContainer.find('option:selected').val() === '' ? '' :
                    wardContainer.find('option:selected').text() +  ", ";
                location = addressContainer.val() + ", " + wardSelectedText
                    + districtContainer.find('option:selected').text() + ", "
                    + provinceContainer.find('option:selected').text();
                _this.binAddressToForm();
            });
        };

        this.changeValue = function(e) {
            let reg1 = /\s*([0-9.-]+)\s*,\s*([0-9.-]+)\s*/g,
                geocode = new google.maps.Geocoder,
                $this = $(e).val(),
                resultReg1 = $this.match(reg1);
            if (resultReg1 != null) {
                let latLngStr = resultReg1[0].split(',', 2),
                    latLng = new google.maps.LatLng(parseFloat(latLngStr[0]), parseFloat(latLngStr[1]));
                geocode.geocode({'latLng': latLng}, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            $(this).val(results[0].formatted_address).trigger('change');
                            _this.changeValue($('#address_pac'));
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
                    let tmp = _this.change_alias(arr[i]);

                    $("#province_id option").each(function (e) {
                        let option = $(this),
                            title = _this.change_alias(option.text());

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
                provinceText = provinceText.replace("T???nh ", "");
                provinceText = provinceText.replace("t???nh ", "");
                provinceText = provinceText.replace("Th??nh ph??? ", "");
                provinceText = provinceText.replace("Th??nh Ph??? ", "");
                provinceText = provinceText.replace("th??nh ph??? ", "");
                provinceText = provinceText.replace("Tp. ", "");
                provinceText = provinceText.replace("TP. ", "");
                provinceText = provinceText.replace("tp. ", "");
                districtText = districtText.replace("Th??nh ph??? ", "");
                districtText = districtText.replace("Th??nh Ph??? ", "");
                districtText = districtText.replace("th??nh ph??? ", "");
                districtText = districtText.replace("Tp. ", "");
                districtText = districtText.replace("TP. ", "");
                districtText = districtText.replace("tp. ", "");
                districtText = districtText.replace("Huy???n ", "");
                districtText = districtText.replace("huy???n ", "");
                districtText = districtText.replace("H. ", "");
                districtText = districtText.replace("h. ", "");
                districtText = districtText.replace("Q. ", "");
                districtText = districtText.replace("q. ", "");
                districtText = districtText.replace("Qu???n ", "");
                districtText = districtText.replace("qu???n ", "");
                districtText = districtText.replace("Th??? x?? ", "");
                districtText = districtText.replace("th??? x?? ", "");
                districtText = districtText.replace("tx. ", "");
                districtText = districtText.replace("Tx. ", "");
                districtText = districtText.replace("TX. ", "");
                wardText = wardText.replace("X?? ", "");
                wardText = wardText.replace("x?? ", "");
                wardText = wardText.replace("Ph?????ng ", "");
                wardText = wardText.replace("ph?????ng ", "");
                wardText = wardText.replace("P. ", "");
                wardText = wardText.replace("p. ", "");
                wardText = wardText.replace("Th??? tr???n ", "");
                wardText = wardText.replace("th??? tr???n ", "");
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
        };

        // G???i ?? c???a gg map
        this._init = function(input) {
            let autocomplete = new google.maps.places.Autocomplete(input);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                _this.changeValue($('#address_pac'));
                return true;
            });
        };

        this.initMap = function() {
            let latLng,
                first = false;
            if (currentLatitude !== '0') {
                latLng = new google.maps.LatLng(currentLatitude, currentLongitude);
                first = true;
            } else {
                latLng = new google.maps.LatLng(21.0031177, 105.82014079999999);
            }

            map = new google.maps.Map(document.getElementById('map_location'), {
                center: latLng,
                zoom: 15,
                gestureHandling: 'cooperative'
            });

            if (first) _this._showExistMarker(latLng);
        };

        this.getLocation = function() {
            let address = addressContainer.val() + ", " + wardContainer.find('option:selected').text() + ", "
                + districtContainer.find('option:selected').text() + ", "
                + provinceContainer.find('option:selected').text(),
                geocode = new google.maps.Geocoder();
            geocode.geocode({
                'address': address
            }, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    _this.clearMarkers();
                    // Center map on location
                    map.setCenter(results[0].geometry.location);

                    // Add marker on location
                    let marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        draggable: true,
                        anchorPoint: new google.maps.Point(0, -29)
                    });
                    markers.push(marker);
                    google.maps.event.addListener(marker, 'dragend', function (evt) {
                        lat = evt.latLng.lat();
                        lng = evt.latLng.lng();
                        _this.binAddressToForm();
                    });

                    location = $('#address').val() + ', ' + results[0].formatted_address;
                    lat = results[0].geometry.location.lat();
                    lng = results[0].geometry.location.lng();
                    _this.binAddressToForm();
                }
            });
        };

        this.clearMarkers = function() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null)
            }
            markers = [];
        };

        this.binAddressToForm = function() {
            fullAddress.val(location);
            $('#latitude').val(lat);
            $('#longitude').val(lng);
        };

        this.change_alias = function(alias) {
            if (typeof alias === 'undefined') {
                return '';
            }

            let str = alias;
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g,"a");
            str = str.replace(/??|??|???|???|???|??|???|???|???|???|???/g,"e");
            str = str.replace(/??|??|???|???|??/g,"i");
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g,"o");
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???/g,"u");
            str = str.replace(/???|??|???|???|???/g,"y");
            str = str.replace(/??/g,"d");
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "A");
            str = str.replace(/??|??|???|???|???|??|???|???|???|???|???/g, "E");
            str = str.replace(/??|??|???|???|??/g, "I");
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "O");
            str = str.replace(/??|??|???|???|??|??|???|???|???|???|???/g, "U");
            str = str.replace(/???|??|???|???|???/g, "Y");
            str = str.replace(/??/g, "D");
            str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
            str = str.replace(/ + /g," ");
            str = str.toLowerCase();
            str = str.trim();
            return str;
        };

        // Hi???n th??? map khi view ho???c confirm
        this._showMap = function (lat, lng) {
            if (mapShow.length > 0 || view) {
                let mapView,
                    latLngShow = new google.maps.LatLng(lat, lng);
                function initMapShow() {
                    mapView = new google.maps.Map(document.getElementById('map_show'), {
                        center: latLngShow,
                        zoom: 15,
                        gestureHandling: 'cooperative'
                    });

                    let marker = new google.maps.Marker({
                        position: latLngShow,
                        map: mapView
                    });
                }
                google.maps.event.addDomListener(window, 'load', initMapShow);
            }
        };

        this._showMapView = function (lat, lng) {
            let latLngShow = new google.maps.LatLng(lat, lng),
                mapView = new google.maps.Map(document.getElementById('map_show'), {
                    center: latLngShow,
                    zoom: 15,
                    gestureHandling: 'cooperative'
                });

            if (lat !== '0') {
                let marker = new google.maps.Marker({
                    position: latLngShow,
                    map: mapView
                });
            }
            google.maps.event.trigger(mapView, "resize");
        };

        // Hi???n th??? qu???n, huy???n khi ch???n t???nh, th??nh ph???
        this._showDistrict = function () {
            provinceContainer.change(function () {
                _this.load(this, 'province_id', '#district_id', urlDistrict, true);
            });
        };

        // Hi???n th??? X?? Ph?????ng khi ch???n Qu???n Huy???n
        this._showWard = function () {
            districtContainer.change(function () {
                _this.load(this, 'district_id', '#ward_id', urlWard, false);
            });
        };

        this._showLocationType = function () {
            customerContainer.change(function() {
                _this.load(this, 'customer_id', '#location_type_id', urlSelectLocationType);
            })
        }

        this._showLocationGroup = function () {
            customerContainer.change(function() {
                _this.load(this, 'customer_id', '#location_group_id', urlSelectLocationGroup);
            })
        }

        this._showExistMarker = function (latLng) {
            let marker = new google.maps.Marker({
                map: map,
                position: latLng,
                draggable: true,
                anchorPoint: new google.maps.Point(0, -29)
            });
            markers.push(marker);

            google.maps.event.addListener(marker, 'dragend', function (evt) {
                lat = evt.latLng.lat();
                lng = evt.latLng.lng();
                _this.binAddressToForm();
            });
        };

        this.init = function (modal) {
            formContainer = $('#location_model');
            mapShow = formContainer.find('#map_show');
            provinceContainer = formContainer.find('#province_id');
            districtContainer = formContainer.find('#district_id');
            wardContainer = formContainer.find('#ward_id');
            addressContainer = formContainer.find("#address");
            input = document.getElementById('address_pac');
            fullAddress = formContainer.find('#full_address');
            location = fullAddress.val();
            customerContainer = formContainer.find('#customer_id');
            this._showDistrict();
            this._showWard();
            this._searchAddress();
            this._showLocationGroup();
            this._showLocationType();
            if (input != null) {
                this._init(input);
            }

            if ($('#map_location').length > 0 && !modal) {
                google.maps.event.addDomListener(window, 'load', this.initMap);
            } else if (modal) {
                let currentLatLng,
                    currentLat = formContainer.find('#latitude').val(),
                    currentLng = formContainer.find('#longitude').val();
                currentLatLng = currentLat !== '' ? new google.maps.LatLng(currentLat, currentLng) :
                    new google.maps.LatLng(21.0031177, 105.82014079999999);

                map = new google.maps.Map(document.getElementById('map_location'), {
                    center: currentLatLng,
                    zoom: 15,
                    gestureHandling: 'cooperative'
                });
                google.maps.event.trigger(map, "resize");
                if (currentLat !== '') {
                    _this._showExistMarker(currentLatLng);
                }
            }
        }
    }

    if (typeof define === 'function' && typeof define.amd === 'object' && define.amd) {

        // AMD. Register as an anonymous module.
        define(function () {
            return OneLogLocation;
        });
    } else if (typeof module !== 'undefined' && module.exports) {
        module.exports = OneLogLocation.attach;
        module.exports.OneLogLocation = OneLogLocation;
    } else {
        window.OneLogLocation = OneLogLocation;
    }
}());