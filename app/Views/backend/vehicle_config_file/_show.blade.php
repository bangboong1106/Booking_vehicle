<ul class="list-group">
    <li class="list-group-item detail-info">
        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">

        <strong class="text-primary">{{$entity->tA('file_name')}}</strong>
        <br/>{{$entity->file_name}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('allow_extension')}}</strong>
        <br/>{!! $entity->getFileType()!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('is_show_register')}}</strong>
        <br/>{!! $entity->getOptionText($entity->is_show_register)!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('is_show_expired')}}</strong>
        <br/>{!! $entity->getOptionText($entity->is_show_expired)!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('active')}}</strong>
        <br/>{!! $entity->getActive()!!}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('note')}}</strong>
        <br/>{!! $entity->note!!}
    </li>
</ul>