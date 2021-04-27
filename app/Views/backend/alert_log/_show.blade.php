<ul class="list-group">
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('name')}}</strong>
        <br/>{{$entity->name}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('title')}}</strong>
        <br/>{!! $entity->title!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('alert_type')}}</strong>
        <br/>{!! $entity->getAlertType()!!}
    </li>
    @if($entity->date_to_send)
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('date_to_send')}}</strong>
        <br/>{{ $entity->date_to_send}} {{$entity->getDateTime('time_to_send','H:i')}}
    </li>
    @endif
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('content')}}</strong>
        <br/>{!! $entity->content!!}
    </li>
</ul>