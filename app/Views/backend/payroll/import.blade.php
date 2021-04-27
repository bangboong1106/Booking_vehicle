@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped import-table">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.code') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.name') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.order_no') }}</th>
            <th style="width: 200px">{{ trans('models.order_customer.attributes.order_date') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.order') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.customer_id') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.customer_name') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.customer_mobile_no') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.location_destination') }}</th>
            <th style="width: 100px">{{ trans('models.order_customer.attributes.ETD_time') }}</th>
            <th style="width: 200px">{{ trans('models.order_customer.attributes.ETD_date') }}</th>
            <th style="width: 250px">{{ trans('models.order_customer.attributes.location_arrival') }}</th>
            <th style="width: 100px">{{ trans('models.order_customer.attributes.ETA_time') }}</th>
            <th style="width: 200px">{{ trans('models.order_customer.attributes.ETA_date') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.distance') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.volume') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.weight') }}</th>
            <th style="width: 200px">{{ trans('models.order_customer.attributes.vehicle_group_id') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.vehicle_number') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.route_number') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.amount') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.commission_type') }}</th>
            <th style="width: 150px">{{ trans('models.order_customer.attributes.commission_value') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            @foreach($entity['vehicle_groups'] as $index =>$vehicle_groups)
                <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                    @if($index==0)
                        <td>{!! $entity['importable'] ? empty($entity['warning']) ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-warning">'.implode('<br>',$entity['warning']).'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                        <td class="text-center">{{$entity['row']}}</td>
                        <td class="text-center">{{$entity['code']}}</td>
                        <td class="text-center">{{$entity['name']}}</td>
                        <td class="text-center">{{$entity['order_no']}}</td>
                        <td class="text-center">{{$entity['order_date']}}</td>
                        <td class="text-center">{{$entity['order_codes']}}</td>
                        <td class="text-center">{{$entity['customer_text']}}</td>
                        <td class="text-center">{{$entity['customer_name']}}</td>
                        <td class="text-center">{{$entity['customer_mobile_no']}}</td>
                        <td class="text-center">{{$entity['location_destination_id']}}</td>
                        <td class="text-center">{{$entity['ETD_time']}}</td>
                        <td class="text-center">{{$entity['ETD_date']}}</td>
                        <td class="text-center">{{$entity['location_arrival_id']}}</td>
                        <td class="text-center">{{$entity['ETA_time']}}</td>
                        <td class="text-center">{{$entity['ETA_date']}}</td>
                        <td class="text-center">{{$entity['distance']}}</td>
                        <td class="text-center">{{$entity['volume']}}</td>
                        <td class="text-center">{{$entity['weight']}}</td>

                    @else
                        @for($i = 1; $i <= 19; $i++)
                            <td></td>
                        @endfor
                    @endif

                    <td class="text-center">{{$vehicle_groups['vehicle_group_id']}}</td>
                    <td class="text-center">{{$vehicle_groups['vehicle_number']}}</td>

                    @if($index==0)
                        <td class="text-center">{{$entity['route_number']}}</td>
                        <td class="text-center">{{$entity['amount']}}</td>
                        <td class="text-center">{{$entity['commission_type_text']}}</td>
                        <td class="text-center">{{$entity['commission_value']}}</td>
                    @else
                        @for($i = 1; $i <= 4; $i++)
                            <td></td>
                        @endfor
                    @endif

                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>