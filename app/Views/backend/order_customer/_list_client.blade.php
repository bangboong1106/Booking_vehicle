@if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span></div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}" data-id="{{$entity->id}}">
            <td class="text-center text-middle list-action">
                <a href="#" data-toggle="tooltip" data-placement="top" title="{{trans('actions.approve')}}"
                   data-original-title="{{trans('actions.approve')}}">
                    <i class="fa fa-check"></i>
                </a>
            </td>
            <td class="text-middle" data-name="true">
                {{$entity->code}}
            </td>
            <td class="text-left text-middle">{!! $entity->name !!}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('order_date', 'd-m-Y')}}</td>
            <td class="text-middle">
                <a href="#" class="customer-detail" data-id="{!!$entity->customer_id  !!}"
                   data-show-url="{!! route("customer.show", isset($entity->customer_id) ? $entity->customer_id : 0) !!}">
                    {!! $entity->name_of_customer_id !!}
                </a>
            </td>
            <td class="text-left text-middle">{!! $entity->customer_name !!}</td>
            <td class="text-left text-middle">{!! $entity->customer_mobile_no !!}</td>
            <td class="text-left text-middle">{!! $entity->name_of_location_destination_id !!}</td>
            <td class="text-left text-middle">{!! $entity->name_of_location_arrival_id !!}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('ETD_date', 'd-m-Y').' '.$entity->getDateTime('ETD_time', 'H:i') }}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('ETA_date', 'd-m-Y').' '.$entity->getDateTime('ETA_time', 'H:i')}}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->weight)!!}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->volume)!!}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->distance)!!}</td>
        </tr>
    @endforeach
@endif