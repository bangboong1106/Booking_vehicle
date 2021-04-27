<ul class="list-group">
    <li class="list-group-item detail-info">

        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.list_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">

        <strong class="text-primary">{{$entity->tA('type')}}</strong>
        <br/>{{$entity->getSystemCodeTypeText()}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('prefix')}}</strong>
        <br/>{{$entity->prefix}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('is_generate_time')}}</strong>
        <br/>
        <span>
            @if($entity->is_generate_time == "1")
                <i class="fa fa-check" aria-hidden="true"></i>
            @else
                <i class="fa"></i>
            @endif
        </span>
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('time_format')}}</strong>
        <br/>{{$entity->time_format}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('suffix_length')}}</strong>
        <br/>{{$entity->suffix_length}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('preview')}}</strong>
        <br/>{{$entity->prefix.sprintf('%0' . $entity->suffix_length . 'd', 1)}}
    </li>
</ul>