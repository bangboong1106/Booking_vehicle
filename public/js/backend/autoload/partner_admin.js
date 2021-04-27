$(function () {
    if (typeof url !== 'undefined') {
        Dropzone.options.avatar = {
            url: url,
            headers: {
                'X-CSRF-TOKEN': token
            },
            autoProcessQueue: true,
            uploadMultiple: false,
            parallelUploads: 5,
            maxFilesize: 5,
            maxFiles: 1,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            dictFileTooBig: 'Image is bigger than 5MB',
            addRemoveLinks: true,
            init: function () {
                var prevFile;

                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("success", function (file, response) {
                    var previewElement = $(file.previewElement);
                    $('#avatar_id').val(response.id);
                    previewElement.attr('data-image', publicUrl + response.path);
                    previewElement.find('.dz-remove').attr('data-id', response.id);

                    //Add action download
                    addActionDownload(Dropzone, previewElement, file, urlDownload.replace('999', response.id));

                    activePreview(previewElement);
                });

                if (existingFiles.length > 0) {
                    mockFile = existingFiles[0];
                    mockFile.status = Dropzone.ADDED;
                    mockFile.accepted = true;

                    this.files.push(mockFile);
                    this.emit('addedfile', mockFile);
                    this.emit('thumbnail', mockFile, mockFile.url);
                    this.emit('complete', mockFile);

                    var previewElement = $(mockFile.previewElement);
                    previewElement.attr('data-image', mockFile.full_url);

                    //Add action download
                    addActionDownload(Dropzone, previewElement, mockFile, mockFile.urlDownload);

                    activePreview(previewElement);
                }
                this._updateMaxFilesReachedClass();
            },
            removedfile: function (file) {
                var container = $(file.previewElement),
                    id = container.find('.dz-remove').data('id'),
                    url = removeUrl.replace('999', id);

                if (typeof id !== 'undefined') {
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': token
                        }
                    });
                } else {
                    $('#avatar_id').val(0);
                }

                var _ref;
                if (file.previewElement) {
                    if ((_ref = file.previewElement) != null) {
                        _ref.parentNode.removeChild(file.previewElement);
                    }
                }
                return this._updateMaxFilesReachedClass();
            },
            //previewsContainer: ".previewsContainer",
            hiddenInputContainer: "body",
            clickable: true
        };
    }

    function addActionDownload(dropzone, previewElement, file, url) {
        previewElement.append("<div class=\"dz-action row mt-2\"></div>");
        previewElement.find('.dz-remove').appendTo(previewElement.find('.dz-action'));
        previewElement.find('.dz-remove').addClass('col-6 fa fa-remove');
        file._dzDowload = dropzone.createElement("<a class=\"dz-download col-6 fa fa-download\" href=\"javascript:undefined;\"></a>");
        file._dzDowload.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = url;
        });
        previewElement.find('.dz-action').prepend(file._dzDowload);
    }

    function activePreview(element) {
        element.on('click', function (e) {
            e.preventDefault();
            var _this = $(this),
                src = _this.data('image'),
                preview = $('#preview-modal');

            preview.find('#preview').attr('src', src);
            preview.modal('show');
        });
    }

    activePreview($('.dz-preview'));

    $('.dz-remove').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var container = $(this).closest('.dz-preview');
        container.remove();
    });

    let switchBtn = $('#switchery_is_active');
    switchBtn.on('change', function () {
        if (switchBtn.is(":checked") || switchBtn.length === 0) {
            $("#form_is_active").val("1");
        } else {
            $("#form_is_active").val("0");
        }
    });
});