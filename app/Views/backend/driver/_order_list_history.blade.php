@foreach($order_list as $entity)
    <tr>
        <td>{{$entity->order_code}}</td>
        <td>{!! $entity->order_no !!}</td>
        <td>{!! $entity->getDateTime('ETD_date', 'd-m-Y') !!} {!! $entity->getDateTime('ETD_time', 'H:i') !!}</td>
        <td>{!! $entity->getDateTime('ETA_date', 'd-m-Y') !!} {!! $entity->getDateTime('ETA_time', 'H:i') !!}</td>
        <td>{!! empty($entity->locationDestination) ? '' : $entity->locationDestination->title !!}</td>
        <td>{!! empty($entity->locationArrival) ? '' : $entity->locationArrival->title !!}</td>
        <td>{!! $entity->customer_name !!}</td>
        <td style="padding-top: 12px;padding-left: 10px;">{!! array_key_exists($entity->status, $statuses) ? $statuses[$entity->status] : '' !!}</td>
    </tr>
@endforeach