@if(!$entities->total())
    <div class="empty-box"><span>{{trans('messages.no_data_found')}}</span></div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle" data-name="true">{{$entity->customer_code}}</td>
            <td class="text-middle">{{$entity->full_name}}</td>
            <td class="text-middle">{{$entity->mobile_no}}</td>
            <td class="text-middle">{{$entity->getCustomerType()}}</td>
            <td class="text-middle">{{$entity->tryGet('adminUser')->username}}</td>
            <td class="text-middle">{{$entity->tryGet('adminUser')->email}}</td>
            <td class="text-middle">{{$entity->delegate}}</td>
            <td class="text-middle">{{$entity->tax_code}}</td>
            <td class="text-center">{{$entity->getDateTime('birth_date', 'd-m-Y')}}</td>
            <td class="text-middle">{{$entity->getSexText()}}</td>
            <td class="text-middle">{{$entity->current_address}}</td>
            <td class="text-middle">{{$entity->note}}</td>
            <td class="text-middle">{!! empty($entity->insUser) ? '' : $entity->insUser->username !!}</td>
            <td class="text-middle">{!! empty($entity->updUser) ? '' : $entity->updUser->username !!}</td>
        </tr>
    @endforeach
@endif