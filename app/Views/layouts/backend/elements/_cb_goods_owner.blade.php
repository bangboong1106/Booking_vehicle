<div class="form-group {{isset($class_form) ? $class_form : ""}}" >
    <label for="customer_id">{{trans('models.location_type.attributes.name_of_customer_id')}} <span class="text-danger">*</span></label>
    <div class="input-group select2-bootstrap-prepend">
        <select class="select2 select-customer filter-customer-only" name="customer_id" id="customer_id">
            @if ($entity->customer_id)
                @foreach($customers as $customer)
                    @if ($customer->id == $entity->customer_id)
                        <option value="{{$customer->id}}" selected="selected"
                                title="{{$customer->full_name}}">
                            {{$customer->full_name}}</option>
                    @endif
                @endforeach
            @endif 
        </select>
    </div>
</div>

@push('scripts')
    <script>
        var comboCustomerUri = '{{route('customer.combo-owner')}}';

        $(function(){
            if (typeof cboSelect2 !== "undefined" && typeof comboCustomerUri !== "undefined") {
                cboSelect2.customer(comboCustomerUri);
            }
        })        
    </script>
@endpush

