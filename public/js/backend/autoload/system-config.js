$(function () {
    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        editableFormConfig.url = url;
        editableFormConfig.viewControlElement = 'setting-view-control';
        editableFormConfig.editControlElement = 'setting-edit-control';
        editableFormConfig.editGroupControlElement = 'edit-group-control';
        allowEditableControlOnForm(editableFormConfig);
    }

    $('.select2').prop('disabled', true);
});