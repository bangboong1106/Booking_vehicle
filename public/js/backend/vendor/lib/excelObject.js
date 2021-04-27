;(function () {
    function OneLogExcel() {
        let importSuccess = false,
            importJs = false,
            importModal = $('#import_excel'),
            exportModal = $('#export_excel'),
            exportMessage = exportModal.find('#export_message'),
            dragContent = $('.box.has-advanced-upload');

        this.init = function (oneLogGrid) {
            Dropzone.prototype.defaultOptions.dictDefaultMessage = 'Kéo tệp vào đây để tải lên';
            Dropzone.prototype.defaultOptions.dictCancelUpload = 'Hủy';
            Dropzone.prototype.defaultOptions.dictUploadCanceled = 'Đã hủy';
            Dropzone.prototype.defaultOptions.dictRemoveFile = '';
            Dropzone.options.import = {
                url: 'upload.php',
                autoProcessQueue: false,
                init: function () {
                    let myDropzone = this;

                    // for Dropzone to process the queue (instead of default form behavior):
                    $("button[type=submit]").on("click", function (e) {
                        // Make sure that the form isn't actually being sent.
                        e.preventDefault();
                        e.stopPropagation();
                        myDropzone.processQueue();
                    });

                    //send all the form data along with the files:
                    this.on("sendingmultiple", function (data, xhr, formData) {
                        formData.append("firstname", jQuery("#firstname").val());
                        formData.append("lastname", jQuery("#lastname").val());
                    });
                }
            };
            this.initImportModal(oneLogGrid);
            this.initButton();
            this.initDragContent();
        }

        this.initImportModal = function (oneLogGrid) {
            importModal.each(function () {
                let modal = $(this),
                    form = modal.find('form'),
                    importBtn = modal.find('#import-button'),
                    nextBtn = modal.find('#import-button-next'),
                    backBtn = modal.find('#import-button-back'),
                    closeBtn = modal.find('.close-import-modal'),
                    input = modal.find('#import-excel'),
                    fileInput = input[0],
                    container = form.find('.result-import'),
                    url = form.attr('action'),
                    label = $('.box__input').find('label'),
                    checkDataStep = modal.find('.wizard-step.check-data'),
                    contentOne = modal.find('.wizard-content-1'),
                    contentTwo = modal.find('.wizard-content-2'),
                    contentThree = modal.find('.wizard-content-3'),
                    progressLine = modal.find('.wizard-progress-line'),
                    importDone = modal.find('.import-done'),
                    model = nextBtn.data('model'), fileUpload;

                if (modal.hasClass('order-modal')) return;

                modal.on('show.bs.modal', function () {
                    backBtn.addClass("d-none");
                    nextBtn.removeClass("d-none");
                    importBtn.addClass("d-none");
                    contentOne.addClass("active");
                    checkDataStep.removeClass("active");
                    contentTwo.removeClass("active");
                    progressLine.css("width", "33%");
                    $(".import-type-excel #create").prop('checked','checked');
                    $(".import-type-excel #update").prop('checked','');
                    input.val('');
                    modal.find('.box.has-advanced-upload .text-success').html('<strong style="color: blue">Chọn tệp</strong><span class="box__dragndrop" style="color: black"> hoặc kéo thả tệp vào vùng này</span>.');

                    if (importJs) return;
                    nextBtn.prop('disabled', true);
                    $.getScript(baseUrl + '/js/backend/vendor/xlsx.full.min.js', function(){
                        nextBtn.prop('disabled', false);
                    });
                    importJs = true;
                });

                nextBtn.on('click', function (e) {
                    let formData = new FormData();
                    e.preventDefault();
                    var el = $(this);
                    el.prop('disabled', true);
                    setTimeout(function(){el.prop('disabled', false); }, 1500);

                    if (!fileInput.files[0]) {
                        return false;
                    }

                    $("#filter_fail").closest(".result-import").removeClass("filter-fail");

                    let file = fileInput.files[0],
                        reader = new FileReader(),
                        ext = file.name.split('.').pop(),
                        result = [];
                    fileUpload = file;
                    reader.onload = function (e) {
                        showLoading();
                        let data = e.target.result, wb,
                            readType = {type: rABS ? 'binary' : 'base64'};
                        try {
                            wb = XLSX.read(data, readType);
                            let res = to_json(wb),
                                sheet = res[Object.keys(res)[0]];
                            let i = typeof headerRow !== 'undefined' ? headerRow : 10;
                            let rowCost = typeof headerCost !== 'undefined' ? headerCost : -1;
                            for (i; i < sheet.length; i++) {
                                let row = sheet[i];
                                if (i === rowCost) {
                                    result.push(row);
                                    continue;
                                }
                                if (isEmpty(row[0]) && isEmpty(row[1]) && isEmpty(row[2]) && isEmpty(row[3])) {
                                    continue;
                                }
                                result.push(row);
                            }

                            if (model === 'quota') {
                                //Danh sach chi phi BDM
                                let sheetCost = res[Object.keys(res)[1]],
                                    j = 1, listCost = [];
                                for (j; j < sheetCost.length; j++) {
                                    let rowCost = sheetCost[j];
                                    listCost.push(rowCost);
                                }
                                result.push(listCost);

                                //Cap nhat vao chuyen ko
                                if ($('#update_route').length > 0 && $('#update_route').is(":checked")) {
                                    result.push({'update_route': true});
                                }

                            }

                            formData.append('data', JSON.stringify(result));

                            let type = $('input[name=import-excel-type]:checked', form).val();
                            formData.append(type, '1');

                            sendRequest({
                                url: url,
                                type: 'POST',
                                data: formData,
                                async: true,
                                processData: false,
                                contentType: false,
                                beforeSend: function () {
                                },
                            }, function (response) {
                                if (!response.ok) {
                                    return showErrorFlash(response.message);
                                } else {
                                    container.html(response.data.content);
                                    label.html(response.data.label);
                                    input.val('');
                                    backBtn.removeClass('d-none');
                                    nextBtn.addClass('d-none');
                                    importBtn.removeClass('d-none');

                                    checkDataStep.addClass('active');
                                    contentOne.removeClass('active');
                                    contentTwo.addClass('active');
                                    progressLine.css('width', '66%');

                                    let switchBtn = container.find('.switchery');
                                    if (switchBtn.length > 0) {
                                        new Switchery(switchBtn[0]);
                                    }
                                }
                            })
                        } catch (e) {
                            console.log(e);
                        }
                    };
                    if (ext === 'xlsx' || ext === 'xls') {
                        if (rABS) reader.readAsBinaryString(file);
                        else reader.readAsArrayBuffer(file);
                    }
                });

                closeBtn.on('click', function (e) {
                    e.preventDefault();
                    modal.find(".wizard-content").removeClass("active");
                    contentOne.addClass("active");
                    checkDataStep.removeClass("active");
                    importDone.removeClass("active");
                    progressLine.css("width", "33.33%");
                    label.html("");

                    if (!importBtn.hasClass("d-none")) importBtn.addClass("d-none");
                    if (!backBtn.hasClass("d-none")) backBtn.addClass("d-none");
                    if (nextBtn.hasClass("d-none")) nextBtn.removeClass("d-none");
                });

                backBtn.on('click', function (e) {
                    e.preventDefault();
                    importBtn.addClass('d-none');
                    nextBtn.removeClass('d-none');
                    backBtn.addClass('d-none');

                    if($('#create').length)
                        $('#create').prop('checked',true);

                    modal.find('.wizard-content').removeClass('active');
                    contentOne.addClass('active');
                    checkDataStep.removeClass('active');
                    importDone.removeClass('active');
                    progressLine.css('width', '33.33%');
                });

                importBtn.on('click', function (e) {
                    e.preventDefault();
                    var el = $(this);
                    el.prop('disabled', true);
                    setTimeout(function(){el.prop('disabled', false); }, 1500);

                    let formDataImport = new FormData();
                    formDataImport.append('import_file', '1');
                    if (typeof customImportFormData === 'function') {
                        customImportFormData(formDataImport);
                    }
                    formDataImport.append('file', fileUpload);

                    sendRequest({
                        url: url,
                        type: 'POST',
                        data: formDataImport,
                        async: true,
                        processData: false,
                        contentType: false
                    }, function (response) {
                        if (!response.ok) {
                            return showErrorFlash(response.message);
                        } else {
                            label.html(response.data.label);
                            contentThree.html(response.data.content);
                            input.val('');

                            progressLine.css('width', '100%');
                            if (!backBtn.hasClass('d-none')) backBtn.addClass('d-none');
                            if (!importBtn.hasClass('d-none')) importBtn.addClass('d-none');
                            if (!nextBtn.hasClass('d-none')) nextBtn.addClass('d-none');
                            contentOne.removeClass('active');
                            contentTwo.removeClass('active');
                            contentThree.addClass('active');
                            importDone.addClass('active');
                            importSuccess = true;
                        }
                    })
                });

                $(document).on('change', '#filter_fail', function () {
                    let input = $(this),
                        checked = input.is(':checked'),
                        importResult = input.closest('.result-import');
                    if (checked) {
                        !importResult.hasClass('filter-fail') ? importResult.addClass('filter-fail') : '';
                    } else {
                        importResult.removeClass('filter-fail');
                    }
                })
            });

            importModal.on('hidden.bs.modal', function (e) {
                if (importSuccess) {
                    oneLogGrid._ajaxSearch($('.list-ajax'));
                    importSuccess = false;
                    $('.wizard-step').removeClass('active');
                    $('.wizard-step.import-wizard').addClass('active');
                    $('.wizard-progress-line').css('width', '33.33%');
                    $('.wizard-content-1').addClass('active');
                    $('.wizard-content-3').removeClass('active');
                }
            });
        }

        this.initButton = function () {
            $(document).on('click', '#confirm_export_button', function (e) {
                e.preventDefault();
                exportMessage.html("");
                let url = $(this).data('url');
                sendRequest({
                    url: url,
                    type: 'GET'
                }, function (response) {
                    if (!response.ok) {
                        return showErrorFlash(response.message);
                    } else {
                        exportMessage.html(response.data.message);
                        exportModal.modal('show');
                    }
                })
            });

            $(document).one('click', '#export-button', function (e) {
                e.preventDefault();
                let button = $(this),
                    url = button.data('url'),
                    modal = button.closest('#export_excel');

                window.open(url);
                modal.modal('hide');
            });
        }

        this.initDragContent = function () {
            if (dragContent.length > 0) {
                var label = dragContent.find('label'),
                    input = dragContent.find('.box__file'),
                    droppedFiles = false,
                    fileTypes = ['xls', 'xlsx'],
                    showFiles = function (files) {
                        if (files && files[0]) {
                            let extension = files[0].name.split('.').pop().toLowerCase(),
                                isSuccess = fileTypes.indexOf(extension) > -1;

                            if (!isSuccess) {
                                label.removeClass().addClass('text-danger').text(label.data('error'));
                                input.val('');
                                return;
                            }
                        }

                        label.text(files.length > 1 ? (input.attr('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name)
                            .removeClass().addClass('text-success');
                    };

                input.on('change', function (e) {
                    showFiles(e.target.files);
                });
                dragContent.on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }).on('dragover dragenter', function () {
                    dragContent.addClass('is-dragover');
                }).on('dragleave dragend drop', function () {
                    dragContent.removeClass('is-dragover');
                }).on('drop', function (e) {
                    droppedFiles = e.originalEvent.dataTransfer.files;
                    showFiles(droppedFiles);
                    input.prop('files', droppedFiles);
                });
            }
        }
    }

    if (typeof define === 'function' && typeof define.amd === 'object' && define.amd) {
        // AMD. Register as an anonymous module.
        define(function () {
            return OneLogExcel;
        });
    } else if (typeof module !== 'undefined' && module.exports) {
        module.exports = OneLogExcel.attach;
        module.exports.OneLogExcel = OneLogExcel;
    } else {
        window.OneLogExcel = OneLogExcel;
    }
}());
