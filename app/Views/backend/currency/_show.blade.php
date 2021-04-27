<script>
    var editableFormConfig = {};
    allowEditableControlOnForm(editableFormConfig);
</script>
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
            @include('layouts.backend.elements.detail_to_edit',['property' => 'currency_code', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'currency_name', 'isEditable' => false])
        </div>
    </li>
</ul>