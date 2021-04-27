<ul class="list-group">
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('description')}}</strong>
        <br/>{{$entity->description}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('email')}}</strong>
        <br/>{!! $entity->email!!}
    </li>
    @if($entity->date_from)
        <li class="list-group-item">
            <strong class="text-primary">{{$entity->tA('date_from')}}</strong>
            <br/>{!! \Carbon\Carbon::parse($entity->date_from)->format('d-m-Y')!!}
        </li>
    @endif
    @if($entity->date_to)
        <li class="list-group-item">
            <strong class="text-primary">{{$entity->tA('date_to')}}</strong>
            <br/>{!! \Carbon\Carbon::parse($entity->date_to)->format('d-m-Y')!!}
        </li>
    @endif
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('schedule_type')}}</strong>
        <br/>{!! $entity->getScheduleType()!!}
    </li>
    @if($entity->time_to_send)
        <li class="list-group-item">
            <strong class="text-primary">{{$entity->tA('time_to_send')}}</strong>
            <br/>{{$entity->getDateTime('time_to_send','H:i')}}
        </li>
    @endif
    @if($reportTypeList)
        <li class="list-group-item">
            <strong class="text-primary">Loại báo cáo</strong>
            @foreach($reportTypeList as $item)
                <br/>{{$item}}
            @endforeach
        </li>
    @endif
</ul>