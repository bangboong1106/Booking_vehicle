@foreach($driver_list as $entity)
    <tr>
        <td>{{$entity->full_name}}</td>
        <td>{{$entity->mobile_no}}</td>
        <td style="padding-top: 12px;padding-left: 10px;">{{$entity->id_no}}</td>
    </tr>
@endforeach