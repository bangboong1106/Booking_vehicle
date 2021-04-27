<div class="form-group {{isset($class_form) ? $class_form : ""}}">
    {!! MyForm::label('location_type_id', $entity->tA('location_type_id'), [], false) !!}
    <div class="input-group">
        <select class="select2 select-location-type select2-only-filter" id="location_type_id" name="location_type_id" disabled>
            @if ($entity->location_type_id)
                @foreach($locationTypes as $type)
                    @if ($type->id == $entity->location_type_id)
                        <option value="{{$type->id}}" selected="selected"
                                title="{{$type->title}}">
                            {{$type->title}}</option>
                    @endif
                @endforeach
            @endif 
        </select>
    </div>
    {!! MyForm::error('location_type_id') !!}
</div>

@push('scripts')
    <script>
        $(function(){
            let customer_id = @json($entity->customer_id);
            let is_create = @json(Request::is('*/create') ? true : false);

            var urlLocationType = "";
            if ($("body").find('.select2#customer_id').length > 0) {
                let c_id = 0;
                if (customer_id != null) {
                    triggerComboBoxLocationType(customer_id);              
                }

                $('#customer_id').on("select2:select", function(e) {
                    if (is_create) {
                        c_id = e.params.data.id;
                        triggerComboBoxLocationType(c_id);
                    }
                });
            } else {
                urlLocationType = '{{route('location-type.combo-location-type')}}';
            }

            function triggerComboBoxLocationType(c_id) {
                if (is_create) {
                    $(".select-location-type").val(null).trigger('change');  
                }
                $('.select-location-type').prop('disabled', false);
                urlLocationType = '{{route('location-type.combo-location-type')}}' + '?c_id=' + c_id;
                cboSelect2.locationType(urlLocationType);
            }
        });
    </script>
@endpush