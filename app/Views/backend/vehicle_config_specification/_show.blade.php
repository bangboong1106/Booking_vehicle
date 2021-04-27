<ul class="list-group">
    <li class="list-group-item detail-info">
        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">

        <strong class="text-primary">{{$entity->tA('name')}}</strong>
        <br/>{{$entity->name}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('type')}}</strong>
        <br/>{!! $entity->getType()!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('active')}}</strong>
        <br/>{!! $entity->getActive()!!}
    </li>
</ul>