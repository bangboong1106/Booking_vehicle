<div class="form-group {{isset($class_form) ? $class_form : ""}}">
    <div class="advanced form-group row">
        <div class="col-md-12">
            {!! MyForm::label('location_ids', $entity->tA('location_ids')) !!}
            <select class="select2 select-location select2-only-filter" id="location_ids[]"
                    name="location_ids[]" multiple='multiple' disabled>
                @if(empty($entity->location_ids))
                    @foreach($entity->locations as $location)
                        <option value="{{$location->id}}" selected="selected"
                                title="{{$location->title}}">{{$location->title}}</option>
                    @endforeach
                @elseif ($entity->selectedLocations())
                    @foreach($entity->selectedLocations() as $location)
                        <option value="{{$location->id}}" selected="selected"
                                title="{{$location->title}}">{{$location->title}}</option>
                    @endforeach
                @endif
            </select>
            {!! MyForm::error('location_ids') !!}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function(){
            let customer_id = @json($entity->customer_id);
            let is_create = @json(Request::is('*/create') ? true : false);

            var urlLocation = "";
            if ($("body").find('.select2#customer_id').length > 0) {
                let c_id = 0;
                if (customer_id != null) {
                    triggerComboBoxLocation(customer_id);              
                }

                $('#customer_id').on("select2:select", function(e) { 
                    if (is_create) {
                        c_id = e.params.data.id;
                        triggerComboBoxLocation(c_id);
                    }
                });
            } else {
                urlLocation = '{{route('location.combo-location')}}';
            }

            function triggerComboBoxLocation(c_id) {
                if (is_create) {
                    $(".select-location").val(null).trigger('change');
                }
                $('.select-location').prop('disabled', false);
                urlLocation = '{{route('location.combo-location')}}' + '?c_id=' + c_id;
                cboSelect2.location(urlLocation, $(".select-location"), true, true, 'Vui lòng chọn địa điểm');
            }
        });
    </script>
@endpush