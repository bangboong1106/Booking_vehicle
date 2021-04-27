<script>
    var resizefunc = [],
        baseUrl = '{{route('home')}}',
        backendUrl = '{{route('dashboard.index')}}',
        publicUrl = '{{public_url('')}}';

    <!-- FCM Push Notification -->
    const urlUpdateNotification = '{{route('notification-log.urlUpdateNotification')}}',
        updateTokenFcm = '{{route('notification-log.updateTokenFcm')}}',
        displayNotification = '{{route('notification-log.displayNotification')}}',
        makeReadAllNotification = '{{route('notification-log.makeReadAllNotification')}}';
    <!-- End FCM Push Notification -->

    const urlDistrict = '{{route('ward.getDistrict')}}',
        urlWard = '{{route('ward.getWard')}}',
        urlSelectLocationType = '{{route('location-type.select-location-type')}}',
        urlSelectLocationGroup = '{{route('location-group.select-location-group')}}';
</script>
<?php
if (env('USE_WEBPACK_BACKEND', false)) {
    $jsFiles = [
        'app',
    ];
} else {
    $jsFiles = [
        'vendor/popper.min',
        'vendor/jquery-ui.min',
        'vendor/bootstrap.min',
        'vendor/utils/loadingoverlay.min',
        'vendor/utils/loadingoverlay_progress.min',
        'vendor/bootstrap-datetimepicker.min',
        'vendor/daterangepicker',
        'vendor/utils/locales/vi',
        'vendor/utils/min',
        'vendor/utils/common',
        'vendor/utils/xhr',
        'vendor/jquery.maskedinput.min',
        'vendor/detect',
//        'vendor/wow.min',
        'vendor/jquery.slimscroll',
        'vendor/jquery.cookie',
//        'vendor/parsley.min',
        'vendor/dropzone',
        'vendor/jsvalidation/js/jsvalidation.min',
        'vendor/select2.min',
        'vendor/vi-select2.min',
        'vendor/jquery.bootstrap-touchspin.min',
        'vendor/switchery.min',
        'vendor/utils/jquery.app',
        'vendor/utils/jquery.core',
        'vendor/utils/grid',
        'vendor/lib/locationObject',
        'vendor/lib/excelObject',
        'vendor/utils/system',

//        'vendor/jquery.hotkeys',
        'vendor/cleave.min',
        'vendor/cleave-phone.vn',

        'vendor/firebase',
        'vendor/utils/notification',
        'vendor/toastr.min',

        'autoload/object-select2',
        'autoload/customer',
        // TODO: Ẩn Thêm đơn hàng nhanh
        'autoload/fast-order',
    ];
}
?>

{!! loadFiles($jsFiles, isset($area) ? $area : 'backend', 'js') !!}
@php
    $validation = empty($validator) ? '' : $validator;
@endphp
{!! $validation !!}