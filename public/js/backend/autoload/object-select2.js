var cboSelect2 = (function () {
    this.selectLocation = function (uri, selector, isTag, multiple, placeholder, data) {
        let locationSelector = selector || ".select-location",
            locationObject =
                selector instanceof jQuery ? selector : $(locationSelector),
            plhd = "";

        if (locationObject.length === 0) {
            return;
        }

        if ($("body").find('.select2#customer_id').length > 0) {
            plhd = placeholder ? placeholder : "Vui lòng chọn chủ hàng trước";
        } else {
            plhd = "Vui lòng chọn địa điểm"
        }

        let onlyFilter = locationObject.hasClass('select2-only-filter');

        locationObject.select2({
            allowClear: true,
            placeholder: plhd,
            ajax: {
                url: uri,
                dataType: "json",
                delay: 200,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        c_id: data ? data.customer_id : -1,
                    };
                },
                cache: true,
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.text,
                                title: item.title,
                            };
                        }),
                        pagination: {
                            more: data.pagination,
                        },
                    };
                },
            },
            templateResult: function (data, container) {
                container.className += " needsclick";
                return data.text;
            },
            templateSelection: function (data) {
                if (data.id === "") {
                    // adjust for custom placeholder values
                    return plhd;
                }
                return data.title;
            },
            escapeMarkup: function (m) {
                return m;
            },
            language: "vi",
            multiple: multiple || false,
            maximumSelectionSize: 1,
            tags: isTag || false,
            createTag: function (params) {
                if (onlyFilter) {
                    return null;
                }

                return {
                    id: "id" + params.term,
                    text: params.term,
                    title: params.term,
                    newOption: true,
                };
            },
        });

        locationObject.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectDriver = function (uri, driverElement, ids, data) {
        let driverSelector = driverElement || ".select-driver",
            selectDriver = $(driverSelector);
        var url = uri;
        if (selectDriver.length > 0) {
            selectDriver.select2({
                allowClear: true,
                ajax: {
                    url: url,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            ids: ids,
                            partner_id: data ? data.partner_id : null,
                            all: data ? data.all : null,
                            vehicle_id: (data && data.vehicle_id) ? data.vehicle_id : null,
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data,
                            pagination: {
                                more: params.page * 10 < data.total,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn tài xế",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">Họ tên</div>' +
                            '<div class="col-md-6 text-ellipsis">Số điện thoại</div>' +
                            "</div>"
                        );
                    }
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.mobile_no +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn tài xế";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });

            selectDriver.on("select2:clear", function (e) {
                let select = $(e.target);
                if (select.hasClass("capital-driver")) {
                    select.val("0");
                }
            });
        }
        selectDriver.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectVehicle = function (uri, selector, data, selectorDriver) {
        var vehicleSelector = selector || ".select-vehicle",
            selectVehicle = $(vehicleSelector);
        var selectorDriver = $(selectorDriver) || $(".select-driver");
        if (selectVehicle.length > 0) {
            selectVehicle
                .select2({
                    allowClear: true,
                    ajax: {
                        url: uri,
                        dataType: "json",
                        delay: 200,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page,
                                partner_id: data ? data.partner_id : null,
                                all: data ? data.all : null,
                            };
                        },
                        processResults: function (data, params) {
                            data.page = data.page || 1;
                            return {
                                results: data.data,
                                pagination: {
                                    more: params.page * 10 < data.total,
                                },
                            };
                        },
                    },
                    placeholder: "Vui lòng chọn xe",
                    minimumInputLength: 0,
                    templateResult: function (repo, container) {
                        if (repo.loading) {
                            return $(
                                '<div class="row">' +
                                '<div class="col-md-4 text-ellipsis">Số xe</div>' +
                                '<div class="col-md-8 text-ellipsis">Vị trí</div>' +
                                "</div>"
                            );
                        }
                        container.className += " needsclick";
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-4 text-ellipsis"><b>' + repo.title + '</b><br/>' +
                            (repo.volume == null ? "" : formatNumber(repo.volume) + " (m3)" + "<br/>") +
                            (repo.weight == null ? "" : formatNumber(repo.weight) + " (kg)") +
                            "</div>" +
                            '<div class="col-md-8 text-ellipsis">' +
                            (repo.current_location == null ? "" : repo.current_location) +
                            "</div>" +
                            "</div>"
                        );
                    },
                    templateSelection: function (repo) {
                        if (repo.id === "") {
                            // adjust for custom placeholder values
                            return "Vui lòng chọn xe";
                        }
                        return repo.title;
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                })
                .on("select2:select", function (e) {
                    if (typeof urlVehicleDriver !== "undefined") {
                        var data = e.params.data,
                            id = data.id,
                            params = {};

                        params._s("vehicle_id", id);
                        sendRequest(
                            {
                                url: urlVehicleDriver,
                                type: "GET",
                                data: params,
                            },
                            function (response) {
                                if (!response.ok) {
                                    return showErrorFlash(response.message);
                                } else {
                                    var driver = response.data.driver;
                                    if (typeof driver.id === "undefined") {
                                        return false;
                                    }

                                    var newOption =
                                        '<option value="' +
                                        driver.id +
                                        '" selected="selected" ' +
                                        'title="' +
                                        driver.full_name +
                                        '">' +
                                        driver.full_name +
                                        "</option>",
                                        primaryDriverSelect =
                                            $(".select-driver").length > 0
                                                ? selectorDriver
                                                : $("#primary_driver_id"),
                                        secondaryDriverSelect = $("#secondary_driver_id");

                                    if (primaryDriverSelect && secondaryDriverSelect) {
                                        primaryDriverSelect.empty();
                                        secondaryDriverSelect.empty();
                                        primaryDriverSelect.append(newOption).trigger("change");
                                        primaryDriverSelect.val(driver.id).trigger("change");
                                    }
                                }
                            }
                        );
                    }
                });
        }
        selectVehicle.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectCodeConfig = function (uri, selector) {
        let el = selector || ".select-code-config";
        let selectCodeConfig = $(el);
        if (selectCodeConfig.length > 0) {
            selectCodeConfig
                .select2({
                    allowClear: true,
                    ajax: {
                        url: uri,
                        dataType: "json",
                        delay: 200,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page,
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.data,
                                pagination: {
                                    more: params.page * 10 < data.total,
                                },
                            };
                        },
                    },
                    placeholder: "Vui lòng chọn dạng mã",
                    minimumInputLength: 0,
                    templateResult: function (repo, container) {
                        if (repo.loading) {
                            return $(
                                '<div class="row">' +
                                '<div class="col-md-6 text-ellipsis">Dạng mã</div>' +
                                '<div class="col-md- text-ellipsis">Hiển thị</div>' +
                                "</div>"
                            );
                        }
                        container.className += " needsclick";

                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">' +
                            repo.title +
                            "</div>" +
                            '<div class="col-md-6 text-ellipsis">' +
                            repo.preview +
                            "</div>" +
                            "</div>"
                        );
                    },
                    templateSelection: function (repo) {
                        if (repo.id === "") {
                            // adjust for custom placeholder values
                            return "Vui lòng chọn dạng mã";
                        }
                        return repo.title;
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                })
                .on("select2:select", function (e) {
                    let data = e.params.data,
                        id = data.id,
                        params = {id: id};
                    sendRequest(
                        {
                            url: urlCode,
                            type: "GET",
                            data: params,
                        },
                        function (response) {
                            if (!response.ok) {
                                return showErrorFlash(response.message);
                            } else {
                                let code = response.data.code;
                                if (selector === ".fast-order-code-config") {
                                    $("#fast_order_order_code").val(code);
                                } else {
                                    $("#order_code").val(code);
                                    //$('#order_no').val(code);
                                }
                            }
                        }
                    );
                });
        }
        selectCodeConfig.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectOrder = function (uri, data) {
        let selectOrder = $(".select-order");
        if (selectOrder.length > 0) {
            selectOrder.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            route_id: data ? data.routeId : null,
                            vehicle_id: data ? data.vehicleId : null,
                            driver_id: data ? data.driverId : null,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    customer_name: item.customer_name,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn đơn hàng",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-12 text-ellipsis">Mã hệ thống</div>' +
                            '<div class="col-md-12 text-ellipsis small-text">Khách hàng</div>' +
                            "</div>"
                        );
                    }
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-12 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-12 text-ellipsis small-text">' +
                        (repo.customer_name != null ? repo.customer_name : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn đơn hàng";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }
        selectOrder.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectOrderCustomer = function (uri, data) {
        let selectOrder = $(".select-order-customer");
        if (selectOrder.length > 0) {
            selectOrder.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            order_no: data ? data.order_no : null,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    customer_name: item.customer_name,
                                    order_no: item.order_no,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn đơn hàng",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-4 text-ellipsis">Mã hệ thống</div>' +
                            '<div class="col-md-4 text-ellipsis">Số đơn hàng</div>' +
                            '<div class="col-md-4 text-ellipsis">Khách hàng</div>' +
                            "</div>"
                        );
                    }
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-4 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        repo.order_no +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        (repo.customer_name != null ? repo.customer_name : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn đơn hàng";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }
        selectOrder.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectRoute = function (uri, data) {
        let selectRoute = $(".select-route");
        $(".select-route").select2({
            allowClear: true,
            ajax: {
                url: uri,
                dataType: "json",
                delay: 200,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page,
                        vehicle_id: data ? data.vehicleId : null,
                        driver_id: data ? data.driverId : null,
                    };
                },
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.text,
                                title: item.title,
                                route_code: item.route_code,
                                final_cost: item.final_cost,
                            };
                        }),
                        pagination: {
                            more: data.pagination,
                        },
                    };
                },
            },
            placeholder: "Vui lòng chọn chuyến xe",
            minimumInputLength: 0,
            templateResult: function (repo) {
                if (repo.loading) {
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-4 text-ellipsis">Mã chuyến</div>' +
                        '<div class="col-md-6 text-ellipsis">Tên chuyến</div>' +
                        '<div class="col-md-2 text-ellipsis">Tổng chi phí (VND)</div>' +
                        "</div>"
                    );
                }
                return $(
                    '<div class="row">' +
                    '<div class="col-md-4 text-ellipsis">' +
                    repo.route_code +
                    "</div>" +
                    '<div class="col-md-6 text-ellipsis">' +
                    repo.title +
                    "</div>" +
                    '<div class="col-md-2 text-ellipsis">' +
                    (repo.final_cost != null ? formatNumber(repo.final_cost) : "0") +
                    "</div>" +
                    "</div>"
                );
            },
            templateSelection: function (repo) {
                if (repo.id === "") {
                    // adjust for custom placeholder values
                    return "Vui lòng chọn chuyến xe";
                }
                return repo.title;
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        });
        selectRoute.each(function (index, el) {
            $(el).data("select2").$container.find("*").addClass("needsclick");
        });
    };

    this.selectQuota = function (uri, vehicle_id) {
        $(".select-quota").select2({
            allowClear: true,
            ajax: {
                url: uri,
                dataType: "json",
                delay: 200,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page,
                        vehicle_id: vehicle_id,
                    };
                },
                processResults: function (data) {
                    data.page = data.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.id,
                                text: item.text,
                                title: item.title,
                                routes: item.routes,
                                total_cost: item.total_cost,
                            };
                        }),
                        pagination: {
                            more: data.pagination,
                        },
                    };
                },
            },
            placeholder: "Vui lòng chọn bảng định mức chi phí",
            minimumInputLength: 0,
            templateResult: function (repo) {
                if (repo.loading) {
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-4 text-ellipsis">Tên</div>' +
                        '<div class="col-md-6 text-ellipsis">Lộ trình</div>' +
                        '<div class="col-md-2 text-ellipsis">Chi phí (VND)</div>' +
                        "</div>"
                    );
                }
                return $(
                    '<div class="row">' +
                    '<div class="col-md-4 text-ellipsis">' +
                    repo.title +
                    "</div>" +
                    '<div class="col-md-6 text-ellipsis">' +
                    repo.routes.replace("-", " - ") +
                    "</div>" +
                    '<div class="col-md-2 text-ellipsis">' +
                    (repo.total_cost != null ? formatNumber(repo.total_cost) : "0") +
                    "</div>" +
                    "</div>"
                );
            },
            templateSelection: function (repo) {
                if (repo.id === "") {
                    // adjust for custom placeholder values
                    return "Vui lòng chọn bảng định mức chi phí";
                }
                return repo.title;
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        });
    };

    this.selectCustomer = function (uri, selector) {
        let selectCustomer = selector ? $(selector) : $(".select-customer");
        if (selectCustomer.length > 0) {
            let onlyFilter = selectCustomer.hasClass('select2-only-filter');
            selectCustomer.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    mobile_no: item.mobile_no,
                                    delegate: item.delegate,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn khách hàng",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-8 text-ellipsis">Họ tên</div>' +
                            '<div class="col-md-4 text-ellipsis">Số điện thoại</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-8 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        (repo.mobile_no ? repo.mobile_no : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn chủ hàng";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        mobile_no: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectCustomerGroup = function (uri, selector) {
        let selectCustomerGroup = selector
            ? $(selector)
            : $(".select-customer-group");
        if (selectCustomerGroup.length > 0) {
            selectCustomerGroup.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    code: item.code,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn nhóm khách hàng",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-4 text-ellipsis">Mã</div>' +
                            '<div class="col-md-8 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-4 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-8 text-ellipsis">' +
                        (repo.title ? repo.title : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn nhóm khách hàng";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "") {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        code: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectVehicleTeam = function (uri) {
        let selectVehicleTeam = $(".select-vehicle-team");
        if (selectVehicleTeam.length > 0) {
            selectVehicleTeam.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    code: item.code,
                                    capital_driver: item.capital_driver,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn đội tài xế",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-4 text-ellipsis">Mã đội</div>' +
                            '<div class="col-md-4 text-ellipsis">Tên đội</div>' +
                            '<div class="col-md-4 text-ellipsis">Đội trưởng</div>' +
                            "</div>"
                        );
                    }
                    return $(
                        '<div class="row">' +
                        '<div class="col-md-4 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        (repo.capital_driver != null ? repo.capital_driver : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        return "Vui lòng chọn đội tài xế";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }
    };

    this.selectPricePolicy = function (uri, selector) {
        let select2 = selector ? $(selector) : $(".select-price-policy");
        if (select2.length > 0) {
            select2.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.title,
                                    title: item.title,
                                    name: item.name,
                                    description: item.description,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn báo giá",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-3 text-ellipsis">Mã báo giá</div>' +
                            '<div class="col-md-3 text-ellipsis">Tên báo giá</div>' +
                            '<div class="col-md-6 text-ellipsis">Mô tả</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-3 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-3 text-ellipsis">' +
                        repo.name +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.description ? repo.description : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn báo giá";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "") {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        description: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectLocationGroup = function (uri, selector) {
        let selectLocationGroup = selector
            ? $(selector)
            : $(".select-location-group");
        if (selectLocationGroup.length > 0) {
            let onlyFilter = selectLocationGroup.hasClass('select2-only-filter');
            selectLocationGroup.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    code: item.code,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn nhóm địa điểm",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">Mã</div>' +
                            '<div class="col-md-6 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.title ? repo.title : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn nhóm địa điểm";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        code: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectPayroll = function (uri, selector) {
        let select2 = selector ? $(selector) : $(".select-payroll");
        if (select2.length > 0) {
            select2.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.title,
                                    title: item.title,
                                    name: item.name,
                                    description: item.description,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn bảng tính lương",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-3 text-ellipsis">Mã bảng tính lương</div>' +
                            '<div class="col-md-3 text-ellipsis">Tên bảng tính lương</div>' +
                            '<div class="col-md-6 text-ellipsis">Mô tả</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-3 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-3 text-ellipsis">' +
                        repo.name +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.description ? repo.description : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn bảng tính lương";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "") {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        description: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectDestroy = function (el) {
        var element = $(el);
        if (element.length > 0) {
            element.select2("destroy");
        }
    };

    this.selectLocationType = function (uri, selector) {
        let selectLocationType = selector
            ? $(selector)
            : $(".select-location-type");
        if (selectLocationType.length > 0) {
            let onlyFilter = selectLocationType.hasClass('select2-only-filter');
            selectLocationType.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    code: item.code,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn loại địa điểm",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">Mã</div>' +
                            '<div class="col-md-6 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.title ? repo.title : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn loại địa điểm";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        code: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectGoodsUnit = function (uri, selector) {
        let selectGoodsUnit = selector
            ? $(selector)
            : $(".select-goods-unit");
        if (selectGoodsUnit.length > 0) {
            let onlyFilter = selectGoodsUnit.hasClass('select2-only-filter');
            selectGoodsUnit.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    title: item.title,
                                    code: item.code,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn đơn vị hàng hoá",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">Mã</div>' +
                            '<div class="col-md-6 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.title ? repo.title : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn đơn vị hàng hoá";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        code: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectPartner = function (uri, selector) {
        let selectPartner = selector
            ? $(selector)
            : $(".select-partner");
        if (selectPartner.length > 0) {
            let onlyFilter = selectPartner.hasClass('select2-only-filter');
            selectPartner.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.items.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    full_name: item.full_name,
                                    code: item.code,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn đối tác",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-6 text-ellipsis">Mã</div>' +
                            '<div class="col-md-6 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-6 text-ellipsis">' +
                        repo.code +
                        "</div>" +
                        '<div class="col-md-6 text-ellipsis">' +
                        (repo.full_name ? repo.full_name : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn đối tác";
                    }
                    return repo.full_name;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        code: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    this.selectVehicleGroup = function (uri, selector, data) {
        let selectVehicleGroup = selector
            ? $(selector)
            : $(".select-vehicle-group");
        if (selectVehicleGroup.length > 0) {
            let onlyFilter = selectVehicleGroup.hasClass('select2-only-filter');
            selectVehicleGroup.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            partner_id: data ? data.partner_id : null,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.data.map(function (item) {
                                return {
                                    id: item.id,
                                    title: item.name,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn chủng loại xe",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-12 text-ellipsis">Tên</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-12 text-ellipsis">' +
                        (repo.title ? repo.title : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn chủng loại xe";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true
            });
        }
    };

    this.selectClient = function (uri, selector, data) {
        let selectCustomer = selector ? $(selector) : $(".select-client");
        if (selectCustomer.length > 0) {
            let onlyFilter = selectCustomer.hasClass('select2-only-filter');
            selectCustomer.select2({
                allowClear: true,
                ajax: {
                    url: uri,
                    dataType: "json",
                    delay: 200,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page,
                            customer_id: data ? data.customer_id : -1,
                        };
                    },
                    processResults: function (data) {
                        data.page = data.page || 1;
                        return {
                            results: data.data.map(function (item) {
                                return {
                                    id: item.id,
                                    title: item.title,
                                    mobile_no: item.mobile_no,
                                };
                            }),
                            pagination: {
                                more: data.pagination,
                            },
                        };
                    },
                },
                placeholder: "Vui lòng chọn khách hàng",
                minimumInputLength: 0,
                templateResult: function (repo) {
                    if (repo.loading) {
                        return $(
                            '<div class="row">' +
                            '<div class="col-md-8 text-ellipsis">Họ tên</div>' +
                            '<div class="col-md-4 text-ellipsis">Số điện thoại</div>' +
                            "</div>"
                        );
                    }

                    return $(
                        '<div class="row">' +
                        '<div class="col-md-8 text-ellipsis">' +
                        repo.title +
                        "</div>" +
                        '<div class="col-md-4 text-ellipsis">' +
                        (repo.mobile_no ? repo.mobile_no : "") +
                        "</div>" +
                        "</div>"
                    );
                },
                templateSelection: function (repo) {
                    if (repo.id === "") {
                        // adjust for custom placeholder values
                        return "Vui lòng chọn khách hàng";
                    }
                    return repo.title;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: "vi",
                maximumSelectionSize: 1,
                tags: true,
                createTag: function (params) {
                    let term = $.trim(params.term);
                    if (term === "" || onlyFilter) {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        title: term,
                        mobile_no: "",
                        newTag: true, // add additional parameters
                    };
                },
            });
        }
    };

    return {
        location: selectLocation,
        driver: selectDriver,
        vehicle: selectVehicle,
        codeConfig: selectCodeConfig,
        order: selectOrder,
        routes: selectRoute,
        quotas: selectQuota,
        customer: selectCustomer,
        destroy: selectDestroy,
        vehicleTeam: selectVehicleTeam,
        orderCustomer: selectOrderCustomer,
        customerGroup: selectCustomerGroup,
        pricePolicy: selectPricePolicy,
        locationGroup: selectLocationGroup,
        payroll: selectPayroll,
        locationType: selectLocationType,
        goodsUnit: selectGoodsUnit,
        partner: selectPartner,
        vehicleGroup: selectVehicleGroup,
        client: selectClient,
    };
})();
