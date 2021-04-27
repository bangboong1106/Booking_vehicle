@foreach($vehicle_list as $entity)
    <tr>
        <td>{{$entity->reg_no}}</td>
        <td>{{$entity->weight}}</td>
        <td>{{$entity->volume}}</td>
        <td style="padding-top: 12px;padding-left: 10px;">{{$entity->length}} * {{$entity->width}}
            * {{$entity->height}}</td>
    </tr>
@endforeach