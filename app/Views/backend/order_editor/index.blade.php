@extends('layouts.backend.layouts.main')
@section('content')
    <div class="flex-fill-right-menu">
        <div class="row flex-list-data-content">
            <div class="col-md-12">
                <div class="card-box list-ajax">
                    <div class="form-inline m-b-10 justify-content-between">
                        <div class="row">
                            <div class="col-md-12 text-xs-center">
                                <span><i>Hệ thống hiện tại chỉ hỗ trợ xử lý 50 đơn hàng 1 lần</i></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="btn-toolbar flex-wrap" role="group" aria-label="">
                                <div class="toolbar btn-group" role="group" aria-label="Basic example">
                                    <button id="btn-export" type="button" class="btn-export">
                                        <a class="btn upload" href="#">
                                            <i class="fa fa-download"></i> {{ trans('actions.export') }}
                                        </a>
                                    </button>
                                    <button id="btn-import" type="button" class="btn-import">
                                            <a class="btn upload" href="#">
                                                <i class="fa fa-upload"></i> {{ trans('actions.import') }}
                                            </a>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wrap-spreadsheet">
                    <div id="spreadsheet"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://bossanova.uk/jexcel/v4/jexcel.js"></script>
    <script src="https://bossanova.uk/jsuites/v3/jsuites.js"></script>
    <link rel="stylesheet" href="https://bossanova.uk/jexcel/v4/jexcel.css" type="text/css" />
    <link rel="stylesheet" href="https://bossanova.uk/jsuites/v3/jsuites.css" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.1.0/js/jquery.jdropdown.js"></script>
    <?php
    $jsFiles = [
        'vendor/jexcel/jquery-clockpicker.min',
    ]
    ?>
    {!! loadFiles($jsFiles, $area, 'css') !!}

    <?php
    $jsFiles = [
        'vendor/jexcel/jquery-clockpicker.min',
    ]
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}

    <script>
        var spreadSheetConfig = {
            url: {
                entity: {
                    vehicle: '{{ route("order-editor.vehicle")}}',
                    driver: '{{ route("order-editor.driver")}}',
                    location: '{{ route("order-editor.location")}}',
                    customer: '{{ route("order-editor.customer")}}',
                    adminUser: '{{ route("order-editor.user")}}',
                },
                customerDetail: '{{ route("order-editor.customer-detail", -1)}}',
                driverDefault: '{{ route("driver.getVehicleDriver")}}',
                columns: '{{route("order-editor.columns")}}',
                import: '{{route("order-editor.import")}}'
            },
            config: {
                supportTransportationCar: {{ json_encode(env("SUPPORT_CAR_TRANSPORTATION" , false))}}
            },
            data: JSON.parse('{!!  json_encode($items) !!}')
        };
    </script>
@endpush
