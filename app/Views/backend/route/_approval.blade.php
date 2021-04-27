<div class="modal-header">
    <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
    <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
    </button>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">{{ trans('actions.approve') }}</h4>
</div>
<style>
    .group-suggest {
        border: 1px solid #d6d6d6;
        border-radius: 4px;
        -moz-background-clip: padding;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        padding: 8px;
        background-color: #f2f2f2;
        min-width: 780px;
        margin-bottom: 16px;
    }

    .group-suggest .suggest {
        color: #212121;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .group-suggest .main-suggest {
        padding-left: 16px;
    }
</style>
<div class="modal-body">
    {{--    @if($fuelCost != 0)--}}
    <div class="crm-flex crm-flex-row crm-justify-content-between group-suggest">
        <div class="crm-flex crm-flex-column">
            <div class="suggest">Gợi ý:</div>
            <div class="main-suggest">
                <div class="ng-tns-c34-30">
                    Chi phí nhiên liệu dự kiến = Số km định mức * Đơn giá dầu * Định mức dầu của xe
                </div>
                <div class="ng-tns-c34-30">
                    Dự kiến: <b>{{numberFormat($fuelCost)}}</b><span> (VND)</span>
                </div>
            </div>
        </div>
    </div>
    {{--    @endif--}}
    <table class="table table-bordered table-hover table-cost view" style="min-height: 300px">
        <thead id="head_content">
        <tr class="active">
            <th scope="col" class="text-left" style="width: 300px;">
                Diễn giải
            </th>
            <th scope="col" class="text-right"
                style="width: 240px;">{{ trans('models.route.attributes.amount_admin') }}<i
                        class="fa fa-copy copy-all" data-type="admin" style="margin-left: 8px; cursor: pointer;"></i>
            </th>
            <th scope="col" class="text-right"
                style="width: 200px;">{{ trans('models.route.attributes.amount_driver') }}<i
                        class="fa fa-copy copy-all" data-type="driver" style="margin-left: 8px; cursor: pointer;"></i>
            </th>
            <th scope="col" class="text-right"
                style="width: 200px;">{{ trans('models.route.attributes.amount_approval') }}</th>
        </tr>
        </thead>
        <tbody id="body_content">
        @if(isset($listCost) && count($listCost) > 0)
            @foreach($listCost as $cost)
                <tr class="container-cost">
                    <td class="text-left">
                        {{$cost['receipt_payment_name']}}
                    </td>
                    <td class="text-right wrap-cost">
                        <span class="cost admin">{{ numberFormat($cost['amount_admin']) }}</span>
                        <a href="#"><i class="fa fa-copy" style="margin-left: 8px"></i></a>
                    </td>
                    <td class="text-right wrap-cost">
                        <span class="cost driver">{{ numberFormat($cost['amount_driver']) }}</span>
                        <a href="#"><i class="fa fa-copy" style="margin-left: 8px"></i></a>
                    </td>
                    <td>
                        <input class="form-control text-right final-cost number-input" type="text"
                               value="{{ numberFormat($cost['amount']) }}" data-id="{{$cost['id']}}">
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="empty-data">
                <td colspan="4" class="text-center">
                    <div class="empty-box">
                        <span style="left: 0">
                            <i>Không thể tìm thấy dữ liệu trên chương trình</i>
                        </span>
                    </div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
    <div class="input-group" style="display: none">
        <select class="select2" id="cost">
            <option></option>
            @if($receiptPayments)
                @foreach($receiptPayments as $receiptPayment)
                    <option value="{{explode("_", $receiptPayment)[0]}}">
                        {{explode("_", $receiptPayment)[1]}}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="add-cost-wrap">
        <button class="btn btn-default"><span>Thêm chi phí</span></button>
    </div>
    <div class="form-group row">
        <div class="col-md-12">
            {!! MyForm::label('note', 'Ghi chú', [], false) !!}
            {!! MyForm::textarea('note', $entity->approved_note,['rows'=>2]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary btn-approve"
            data-url="{{ route('route.approval', $entity->id) }}">
        <i class="fa fa-check" style="margin-right: 8px"></i>
        {{ trans('actions.approve') }}
    </button>
</div>
<script>
    $(document).on('click', '.table-cost .fa.fa-copy:not(.copy-all)', function (e) {
        var cost = $(this).closest('.wrap-cost').find('.cost').text();
        if (cost == "") {
            $(this).parents('tr').find('.insertedLastCost').html('');
        }
        $(this).closest('.container-cost').find('.final-cost').val(cost)
    });
    $(document).on('click', '.table-cost .fa.fa-copy.copy-all', function (e) {
        var type = $(this).attr('data-type');
        $('.table-cost tbody tr').each((index, item) => {
            var cost = $(item).find('.wrap-cost .cost.' + type).text();
            $(item).find('.final-cost').val(cost);
            if (cost == "") {
                $(item).find('.insertedLastCost').html('');
            }
        });

    });
</script>