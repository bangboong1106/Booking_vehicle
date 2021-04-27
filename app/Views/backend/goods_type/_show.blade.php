<ul class="list-group">
    <li class="list-group-item detail-info">

        @if (isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{ isset($showAdvance) ? 'first' : '' }} list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'name_of_customer_id', 'isEditable' => false, 'value' => isset($entity->customer) ? $entity->customer->full_name : "-"])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'title', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'goods_group_id', 'isEditable' => false,
            'value'=> isset($entity->goodsGroup) ? $entity->goodsGroup->name : ''])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'goods_unit_id', 'isEditable' => false,
            'value'=> isset($entity->goodsUnit) ? $entity->goodsUnit->title : ''])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'volume', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'weight', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'in_amount', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'out_amount', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            <div class="col-md-12">
                {!! MyForm::label('file', $entity->tA('file'), [], false) !!}
                <div class="preview-file">
                    @if ($entity->file_id)
                        <div>
                            <img src="{{ route('file.getImage', ['id' => $entity->file_id, 'full' => true]) }}"
                                class=" img-fluid preview-image">
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </li>
</ul>
