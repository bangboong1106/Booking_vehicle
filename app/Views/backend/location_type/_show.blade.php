<ul class="list-group">
    <li class="list-group-item detail-info">

        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'title', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'name_of_customer_id', 'isEditable' => false, 'value' => isset($entity->customer) ? $entity->customer->full_name : "-"])
        </div>
    </li>
    <li class="list-group-item">
        {{--        <strong class="text-primary">{{$entity->tA('note')}}</strong>--}}
        {{--        <br/>{!! ebr($entity->note) !!}--}}

        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'description', 'isEditable' => false])
        </div>
    </li>
</ul>