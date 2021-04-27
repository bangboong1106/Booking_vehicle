<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<div class="modal-header">
    <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
    <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
    </button>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">{{ trans('actions.print-template') }}</h4>
</div>
<style>
    .template-toolbar {
        align-self: center !important;
        flex-grow: 1;
        padding: 16px 16px 0 24px;
        display: flex;
        flex-direction: row !important;
    }

    .search-body {
        width: 70%;
        display: flex !important;
        flex-direction: column !important;
        height: auto;
        position: relative;
        border-radius: 3px;
        -moz-background-clip: padding;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        -ms-box-sizing: border-box;
        -o-box-sizing: border-box;
        box-sizing: border-box;
    }

    .search-wrap {
        overflow: hidden;
        width: 100%;
        display: flex !important;
    }

    #search-text {
        background-size: 16px 16px;
        padding-right: 32px !important;
        background-repeat: no-repeat !important;
        background-position: center left 8px;
        padding-left: 40px !important;
        display: block;
        width: 100%;
        min-height: 32px;
        max-height: 32px;
        padding: 6px 16px !important;
        font-size: 13px;
        line-height: 1.428571429;
        color: #212121;
        vertical-align: middle;
        background-color: #fff;
        border: 1px solid #d6d6d6;
        border-radius: 3px;
    }

    #btn-add-template {
        height: 32px;
        line-height: 20px !important;
        padding: 5px 12px;
        border-radius: 3px;
        -moz-background-clip: padding;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        border: 1px solid #1976d2;
        -ms-box-sizing: border-box;
        -o-box-sizing: border-box;
        box-sizing: border-box;
        background-color: #fff;
        color: #1976d2;
        cursor: pointer;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        background-position: center left 8px;
    }

    #btn-add-template .fa.fa-plus {
        margin-right: 8px;
    }

    .plus-wrap {
        margin-left: 12px;
    }

    #template-list {
        list-style: none;
        margin-top: 16px;
        cursor: pointer;
    }

    #template-list li {
        height: 32px;
        line-height: 20px !important;
        padding: 4px;
    }
</style>
<?php
$route = route('order.exportCustomTemplate');
switch ($type) {
    case config('constant.ORDER'):
        $route = route('order.exportCustomTemplate');
        break;
    case config('constant.DOCUMENT'):
        $route = route('document.exportCustomTemplate');
        break;
    case config('constant.ROUTE'):
        $route = route('route.exportCustomTemplate');
        break;
    case config('constant.QUOTA'):
        $route = route('quota.exportCustomTemplate');
        break;
    case config('constant.CUSTOMER'):
        $route = route('customer.exportCustomTemplate');
        break;
    case config('constant.DRIVER'):
        $route = route('driver.exportCustomTemplate');
        break;
    case config('constant.VEHICLE'):
        $route = route('vehicle.exportCustomTemplate');
        break;
    case config('constant.ORDER_CUSTOMER'):
        $route = route('order-customer.exportCustomTemplate');
        break;

}
?>
<div class="modal-body" style="padding: 0 !important;">
    <div class="window-body">
        <div class="template-toolbar">
            <div class="search-body">
                <div class="search-wrap">
                    <input id="search-text" type="text" placeholder="Tìm kiếm mẫu"/>
                </div>
            </div>

            <div class="plus-wrap">
                <button id="btn-add-template" class="btn" data-url="{{route('template.create')}}">
                    <span><i class="fa fa-plus"></i></span>
                    Thêm mẫu
                </button>
            </div>
        </div>
        <div>
            <ul id="template-list">
                @foreach($templateList as $template)
                    <li>
                        <a href="#" data-url="{{$route }}"
                           data-ids="{{$ids}}"
                           data-id="{{$template->id}}" href="#"> {{$template->title}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        @if($isParam)
            <div id="divParameter" style="display: none;
                                            background-color: #efefef;
                                            width: 100%;
                                            padding: 4px">
                <div style="padding: 8px 16px 0 24px;">
                    <div id="template-range" class="pull-right form-control">
                        <span></span>
                        <i class="pull-right glyphicon-calendar fa fa-calendar"></i>
                    </div>
                </div>
                <div style="text-align: right;">
                    <button id="download-template" class="btn btn-success" style="margin: 8px 16px;">
                        <i class="fa fa-download"></i><span style="margin-left: 16px">Tải biểu mẫu</span></button>
                </div>
            </div>
        @endif
    </div>
</div>
<script>
            @if($isParam)
    var parameter = {};
    $(document).ready(function () {
        $('#template-range span').html(
            moment().startOf('month').locale('vi').format('D MMMM, YYYY') + ' - ' +
            moment().endOf('month').locale('vi').format('D MMMM, YYYY'));
        parameter.startDate = moment().startOf('month').format('YYYY-MM-DD');
        parameter.endDate = moment().endOf('month').format('YYYY-MM-DD');

        $('#template-range').daterangepicker({
            format: 'DD/MM/YYYY',
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            dateLimit: {
                days: 31
            },
            showDropdowns: false,
            showWeekNumbers: true,
            timePicker: false,
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-success',
            cancelClass: 'btn-secondary',
            separator: ' to ',
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                applyLabel: 'Chọn',
                cancelLabel: 'Hủy',
                fromLabel: 'Từ',
                toLabel: 'đến',
                customRangeLabel: 'Tùy chọn',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5 ', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                firstDay: 1
            }
        }, function (start, end, label) {
            parameter.startDate = start.format('YYYY-MM-DD');
            parameter.endDate = end.format('YYYY-MM-DD');
            $('#template-range span').html(start.locale('vi').format('D MMMM, YYYY') + ' - ' + end.locale('vi').format('D MMMM, YYYY'));
        });
    });
    $(document).off('click', '#template-list li a');
    $(document).on('click', '#template-list li a', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var uri = $(this).data('url');
        var templateId = $(this).data('id');
        var ids = $(this).data('ids');
        if ($('#divParameter').css('display') == 'none') {
            $('#divParameter').css('display', 'block');
            $('#download-template').data('url', uri).data('id', templateId).data('ids', ids);
        } else {
            $('#divParameter').css('display', 'none');
            $('#download-template').data('url', '').data('id', '').data('ids', '');
        }
    });
    $(document).off('click', '#download-template');
    $(document).on('click', '#download-template', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var uri = $(this).data('url');
        var templateId = $(this).data('id');
        var ids = $(this).data('ids');
        var param = $.param(parameter);
        var url = uri + '?ids=' + ids + '&templateId=' + templateId + '&' + param;
        window.open(url);
    });
    @else
    $(document).off('click', '#template-list li a');
    $(document).on('click', '#template-list li a', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var uri = $(this).data('url');
        var templateId = $(this).data('id');
        var ids = $(this).data('ids');
        var url = uri + '?ids=' + ids + '&templateId=' + templateId;
        window.open(url);
    });
    @endif

    $(document).off('keyup', '#search-text');
    $(document).on('keyup', '#search-text', function (e) {
        var filter = $(this).val();
        $("#template-list li").each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show()
            }
        });
    });
    $(document).off('click', '#btn-add-template');
    $(document).on('click', '#btn-add-template', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).data('url');
        var win = window.open(url, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Please allow popups for this website');
        }
    });
</script>