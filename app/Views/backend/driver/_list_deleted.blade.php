@if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu trên chương trình</span><span>Không thể tìm thấy dữ liệu trên chương trình</span>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle" data-name="true">{{$entity->code}}</td>
            <td class="text-middle">{{$entity->tryGet('adminUser')->username}}</td>
            <td class="text-middle">{{$entity->full_name}}</td>
            <td class="text-middle">
                <a href="mailto:{{$entity->tryGet('adminUser')->email}}">{{$entity->tryGet('adminUser')->email}}</a>
            </td>
            <td class="text-middle">
                <a href="tel:{{$entity->mobile_no}}">{{$entity->mobile_no}}
                </a>
            </td>
            <td class="text-middle">{{$entity->id_no}}</td>
            <td class="text-middle">{{$entity->driver_license}}</td>
            <td class="text-middle">{{$entity->getSexText()}}</td>
            <td class="text-center">{{$entity->getDateTime('birth_date', 'd-m-Y')}}</td>
            <td class="text-middle">{{$entity->vehicleTeams->pluck('name')->implode(' ; ')}}</td>
            <td class="text-center">{{ $entity->getDateTime('work_date', 'd-m-Y')}}</td>
            <td class="text-right">{{$entity->experience_drive}}</td>
            <td class="text-right">{{$entity->experience_work}}</td>
            <td class="text-middle">{{$entity->address}}</td>
            <td class="text-middle">{{$entity->hometown}}</td>
            <td class="text-middle">{{$entity->evaluate}}</td>
            <td class="text-middle">{{$entity->rank}}</td>
            <td class="text-middle">{{$entity->work_description}}</td>
            <td class="text-middle">{{$entity->note}}</td>
            <td class="text-middle">{!! empty($entity->insUser) ? '' : $entity->insUser->username !!}</td>
            <td class="text-middle">{!! empty($entity->updUser) ? '' : $entity->updUser->username !!}</td>
        </tr>
    @endforeach
@endif