<div id="header-filter" class="collapse" role="tabpanel" aria-labelledby="collapse-filter">
    <div class="form-inline">
        <label for={{$id}} class="pr-2">{{$label}}</label>
        <div class="input-group select2-bootstrap-prepend col-5">
            <select class="select2 select-customer filter-customer-only {{$select2_class}}" name={{$name}} id={{$id}}>
        
            </select>
        </div>
    </div>

    <br/>
</div>

@push('scripts')
<script>
    var comboCustomerUri = '{{route('customer.combo-owner')}}';
    $(function(){
        $('.filter-customer').on('select2:select', function (e) {
            oneLogGrid._ajaxSearch($('.list-ajax'));
        });

        $('.filter-customer').on('select2:clear', function (e) {
            oneLogGrid._ajaxSearch($('.list-ajax'));
        });

        if (typeof cboSelect2 !== "undefined") {
            if (typeof comboCustomerUri !== "undefined") {
                cboSelect2.customer(comboCustomerUri);
            }
        }
    })
</script>   
@endpush