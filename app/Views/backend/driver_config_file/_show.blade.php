<ul class="list-group">
    <li class="list-group-item detail-info">
        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('file_name')}}</strong>--}}
        {{--        <br/>{{$entity->file_name}}--}}
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'file_name', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('allow_extension')}}</strong>--}}
        {{--        <br/>{!! $entity->getFileType()!!}--}}
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'allow_extension', 'isEditable' => false, 'value'=>$entity->getFileType()])
        </div>
    </li>
    <li class="list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('is_show_register')}}</strong>--}}
        {{--        <br/>{!! $entity->getOptionText($entity->is_show_register)!!}--}}
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'is_show_register', 'isEditable' => false, 'value'=>$entity->getOptionText($entity->is_show_register)])
        </div>
    </li>
    <li class="list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('is_show_expired')}}</strong>--}}
        {{--        <br/>{!! $entity->getOptionText($entity->is_show_expired)!!}--}}
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'is_show_expired', 'isEditable' => false, 'value'=>$entity->getOptionText($entity->is_show_expired)])
        </div>
    </li>
    <li class="list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('active')}}</strong>--}}
        {{--        <br/>{!! $entity->getActive()!!}--}}

        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'active', 'isEditable' => false, 'value'=>$entity->getActive()])
        </div>
    </li>
    <li class="list-group-item">


        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'isEditable' => false])
        </div>
    </li>
</ul>