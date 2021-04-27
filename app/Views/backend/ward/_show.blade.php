<ul class="list-group">
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('title')}}</strong>
        <br/>{{$entity->title}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('type')}}</strong>
        <br/>{!! $entity->getType() !!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('province')}}</strong>
        <br/>{!! $provinceObj->title !!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('district')}}</strong>
        <br/>{!! $districtObj->title !!}
    </li>
</ul>