@if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu trên chương trình</span><span>Không thể tìm thấy dữ liệu trên chương trình</span>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle" data-name="true">{{$entity->reg_no}}</td>
            <td class="text-middle">{{$entity->tryGet('vehicleGroup')->name}}</td>
            <td class="text-right">{{numberFormat($entity->weight)}}</td>
            <td class="text-right">{{numberFormat($entity->volume)}}</td>
            <td class="text-right">
                {{$entity->length !=null ? numberFormat($entity->length) : numberFormat(0)}}
                * {{$entity->width !=null ? numberFormat($entity->width) : numberFormat(0)}}
                * {{ $entity->height !=null ? numberFormat($entity->height) : numberFormat(0)}}
            </td>
            <td class="text-middle">{{$entity->getStatus()}}</td>
            <td class="text-middle">{{$entity->getType()}}</td>
            <td class="text-middle">{{$entity->getActive()}}</td>
            <td class="text-middle">{{$entity->current_location}}</td>
            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->category_of_barrel}}</td>
            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->weight_lifting_system}}</td>
            <td class="text-middle">{{numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel)}}</td>
            <td class="text-right">{{$entity->tryGet('vehicleGeneralInfo')->register_year}}</td>
            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->brand}}</td>
            <td class="text-middle">{!! empty($entity->insUser) ? '' : $entity->insUser->username !!}</td>
            <td class="text-middle">{!! empty($entity->updUser) ? '' : $entity->updUser->username !!}</td>
        </tr>
    @endforeach
@endif