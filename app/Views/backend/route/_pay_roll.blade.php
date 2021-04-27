<div class="wrap-route" data-id="{{$entity->id}}" data-url="{{route("route.calcPayroll")}}">
    <div class="modal-header">
        <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
        <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
        </button>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">{{ trans('actions.pay_roll') }}</h4>
    </div>
    <div class="modal-body">
        @foreach($customers as $index=>$customer)
            <div class="wrap-customer" data-id="{{$customer['customer_id']}}">
                <div class="row" style="padding: 8px" >
                    <div class="col-md-6 customer-name-wrap">
                        <div class="numberCircle">{{$index + 1}}</div>
                        <div class="customer-name-label">
                            <label for="customer_full_name">{{$customer['full_name']}}</label></div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <select class="select2 select-payroll" id="payroll_id[{{$index}}]" data-customer-id="{{$customer['customer_id']}}"
                            name="payroll_id[{{$index}}]">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-bordered table-hover table-order view" style="margin: 12px">
                        <thead id="head_content">
                        <tr class="active">
                            <th scope="col" class="text-left" style="width: 150px;">
                                Số đơn hàng
                            </th>
                            <th scope="col" class="text-left"
                                style="width: 300px;">Địa điểm nhận hàng
                            </th>
                            <th scope="col" class="text-left"
                                style="width: 300px;">Địa điểm trả hàng
                            </th>
                            <th scope="col" class="text-right"
                                style="width: 150px;">Lương</th>
                        </tr>
                        </thead>
                        <tbody id="body_content">
                            @if(isset($customer['orders']) && count($customer['orders']) > 0)
                                @foreach($customer['orders'] as $order)
                                    <tr class="container" data-id={{$order['order_id']}}>
                                        <td class="text-left">
                                            {{$order['order_code']}}
                                        </td>
                                        <td class="text-left">
                                            <span class="location_destination">{{$order['location_destination'] }}</span>
                                        </td>
                                        <td class="text-left">
                                            <span class="location_arrival">{{ $order['location_arrival'] }}</span>
                                        </td>
                                        <td class="text-right">
                                            <input type="text" class="form-control text-right amount number-input" data-id={{$order['order_id']}} value="{{ numberFormat($order['amount']) }}""></input>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
       @endforeach
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-payroll" data-url={{route("route.savePayroll")}}>
            <i class="fa fa-money" style="margin-right: 8px"></i>
            {{ trans('actions.pay_roll') }}
        </button>
    </div>
    <script>
    </script>
</div>
