@if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span></div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle" data-name="true">{{$entity->order_code}}</td>
            <td class="text-middle">
                <span class="badge badge-{!! $entity->status == config("constant.CHO_NHAN_HANG") ? 'brown'
                     : ($entity->status == config("constant.DANG_VAN_CHUYEN") ? 'blue'
                     : ($entity->status == config("constant.HOAN_THANH") ? 'success'
                     : ($entity->status == config("constant.HUY") ? 'dark'
                     : ($entity->status == config("constant.KHOI_TAO") ? 'light'
                     : ($entity->status == config("constant.TAI_XE_XAC_NHAN") ? 'stpink'
                     : 'secondary'))))) !!}">
                        {!! array_key_exists($entity->status, $statuses) ? $statuses[$entity->status] : '' !!}
                </span>
            </td>
            <td class="text-middle">{!! $entity->order_no !!}</td>
            <td class="text-middle">{!! $entity->bill_no !!}</td>
            <td class="text-middle">{!! $entity->customer_name !!}</td>
            <td class="text-middle">
                <a href="tel:{!! $entity->customer_mobile_no !!}">{!! $entity->customer_mobile_no !!}</a>
            </td>
            <td class="text-center">{!! $entity->getDateTime('order_date', 'd-m-Y') !!}</td>
            <td class="text-right">{!!empty($entity->amount) ? '0' : numberFormat($entity->amount) !!}</td>
            <td class="text-middle">{!! empty($entity->insured_goods) ? trans('messages.no') : trans('messages.yes') !!}</td>
            <td class="text-right">{!! numberFormat($entity->quantity) !!}</td>
            <td class="text-right">{!! empty($entity->volume) ? '' : numberFormat($entity->volume) !!}</td>
            <td class="text-right">{!! empty($entity->weight) ? '' : numberFormat($entity->weight) !!}</td>
            <td class="text-center">
                {!! $entity->getDateTime('ETD_date', 'd-m-Y') !!} {!! $entity->getDateTime('ETD_time', 'H:i') !!}</td>
            <td class="text-center">
                {!! $entity->getDateTime('ETA_date_reality', 'd-m-Y') !!} {!! $entity->getDateTime('ETA_time_reality', 'H:i') !!}</td>
            <td class="text-middle">{!! empty($entity->locationDestination) ? '' : $entity->locationDestination->title !!}</td>
            <td class="text-middle">
                <a href="tel:{!! $entity->contact_mobile_no_destination !!}">{!! $entity->contact_mobile_no_destination !!}</a>
            </td>
            <td class="text-middle">{!! $entity->contact_name_destination !!}</td>
            <td class="ttext-right">{!! empty($entity->loading_destination_fee) ? '0' : numberFormat($entity->loading_destination_fee)!!}</td>
            <td class="text-center">
                {!! $entity->getDateTime('ETA_date', 'd-m-Y') !!} {!! $entity->getDateTime('ETA_time', 'H:i') !!}</td>
            <td class="text-center">
                {!! $entity->getDateTime('ETA_date_reality', 'd-m-Y') !!} {!! $entity->getDateTime('ETA_time_reality', 'H:i') !!}</td>
            <td class="text-middle">{!! empty($entity->locationArrival) ? '' : $entity->locationArrival->title !!}</td>
            <td class="text-middle">
                <a href="tel:{!! $entity->contact_mobile_no_arrival !!}">{!! $entity->contact_mobile_no_arrival !!}</a>
            </td>
            <td class="text-middle">{!! $entity->contact_name_arrival !!}</td>
            <td class="text-middle">
                {!! empty($entity->loading_arrival_fee) ? '0' : numberFormat($entity->loading_arrival_fee) !!}</td>
            <td class="text-middle">
                @if($entity->precedence == config('constant.ORDER_PRECEDENCE_SPECIAL'))
                    <span class="fa fa-star text-warning"></span>
                    <span class="fa fa-star text-warning"></span>
                    <span class="fa fa-star text-warning"></span>
                @endif
                @if($entity->precedence == config('constant.ORDER_PRECEDENCE_NORMAL'))
                    <span class="fa fa-star text-warning"></span>
                    <span class="fa fa-star text-warning"></span>
                @endif
                @if($entity->precedence == config('constant.ORDER_PRECEDENCE_LOW'))
                    <span class="fa fa-star text-warning"></span>
                @endif
            </td>
            <td class="text-middle">{!! empty($entity->insUser) ? '' : $entity->insUser->username !!}</td>
            <td class="text-middle">{!! empty($entity->updUser) ? '' : $entity->updUser->username !!}</td>
            <td class="text-center">{!! $entity->getDateTime('ins_date', 'd-m-Y') !!}</td>
            <td class="text-center">{!! $entity->getDateTime('upd_date', 'd-m-Y') !!}</td>
            <td class="text-middle">{!! e($entity->note) !!}</td>
            <td class="text-right">{!! empty($entity->extend_cost) ? '0' : numberFormat($entity->extend_cost)!!}</td>
        </tr>
    @endforeach
@endif