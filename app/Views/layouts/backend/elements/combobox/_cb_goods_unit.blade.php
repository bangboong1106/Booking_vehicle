<div class="form-group {{isset($class_form) ? $class_form : ""}}">
    {!! MyForm::label('goods_unit_id', $entity->tA('goods_unit_id'), [], false) !!}
    <div class="input-group">
        <select class="select2 select-goods-unit select2-only-filter" id="goods_unit_id" name="goods_unit_id" disabled>
            @if ($entity->goods_unit_id)
                @foreach($goodsUnits as $unit)
                    @if ($unit->id == $entity->goods_unit_id)
                        <option value="{{$unit->id}}" selected="selected"
                                title="{{$unit->title}}">
                            {{$unit->title}}</option>
                    @endif
                @endforeach
            @endif 
        </select>
    </div>
    {!! MyForm::error('goods_unit_id') !!}
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
                    triggerComboBoxGoodsUnit(customer_id);              
                }

                $('#customer_id').on("select2:select", function(e) {
                    if (is_create) {
                        c_id = e.params.data.id;
                        triggerComboBoxGoodsUnit(c_id);
                    }
                });
            } else {
                urlLocationType = '{{route('goods-unit.combo-goods-unit')}}';
            }

            function triggerComboBoxGoodsUnit(c_id) {
                $('.select-goods-unit').prop('disabled', false);
                if (is_create) {
                    $(".select-location-type").val(null).trigger('change');  
                }

                urlLocationType = '{{route('goods-unit.combo-goods-unit')}}' + '?c_id=' + c_id;
                cboSelect2.goodsUnit(urlLocationType);
            }
        });
    </script>
@endpush