<div class="form-group {{isset($class_form) ? $class_form : ""}}">
    {!! MyForm::label('location_group_id', $entity->tA('location_group_id'), [], false) !!}
    <div class="input-group">
        <select class="select2 select-location-group select2-only-filter" id="location_group_id" name="location_group_id" disabled>
            @if ($entity->location_group_id)
                @foreach($locationGroups as $group)
                    @if ($group->id == $entity->location_group_id)
                        <option value="{{$group->id}}" selected="selected"
                                title="{{$group->title}}">
                            {{$group->title}}</option>
                    @endif
                @endforeach
            @endif 
        </select>
    </div>
    {!! MyForm::error('location_group_id') !!}
</div>

@push('scripts')
    <script>
        $(function(){
            let customer_id = @json($entity->customer_id);
            let is_create = @json(Request::is('*/create') ? true : false);

            var urlLocationGroup = "";
            if ($("body").find('.select2#customer_id').length > 0) {
                let c_id = 0;
                if (customer_id != null) {
                    triggerComboBoxLocationGroup(customer_id);              
                }

                $('#customer_id').on("select2:select", function(e) {
                    if (is_create) {
                        c_id = e.params.data.id;
                        triggerComboBoxLocationGroup(c_id);
                    }
                });
            } else {
                urlLocationGroup = '{{route('location-group.combo-location-group')}}';
            }

            function triggerComboBoxLocationGroup(c_id) {
                if (is_create) {
                    $(".select-location-group").val(null).trigger('change');  
                }
                $('.select-location-group').prop('disabled', false);
                urlLocationGroup = '{{route('location-group.combo-location-group')}}' + '?c_id=' + c_id;
                cboSelect2.locationGroup(urlLocationGroup);
            }
        });
    </script>
@endpush