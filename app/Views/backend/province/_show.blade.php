<ul class="list-group">
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('province_id')}}</strong>
        <br/>{{$entity->province_id}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('title')}}</strong>
        <br/>{{$entity->title}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('type')}}</strong>
        <br/>{!! $entity->getType() !!}
    </li>
    {{--<li class="list-group-item">--}}
        {{--<strong class="text-primary">{{$entity->tA('avatar')}}</strong>--}}
        {{--<br/>{!! $entity->getTmpFile('avatar')!!}--}}
    {{--</li>--}}
</ul>