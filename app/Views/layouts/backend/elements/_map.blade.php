<div id="map_modal" class="modal fade" role="dialog" aria-labelledby="mapModal" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"
                                                          title="Click để phóng to cửa sổ"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"
                                                                                title="Click để thu nhỏ cửa sổ"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                        title="Click để đóng cửa sổ">×
                </button>
                <h4 class="modal-title">Chọn vị trí</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            {!! MyForm::label('address_entered', trans('models.common.address_entered')) !!}
                            {!! MyForm::text('address_entered',$entity->address_entered, ['id'=>'address_entered', 'placeholder' => 'Paste hoặc nhập địa chỉ vào....']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            {!! MyForm::label('province_select', trans('models.common.province')) !!}
                            {!! MyForm::dropDown('province_select', $entity->province_id, $provinceList, true, ['class' => 'select2'])  !!}
                            <span class="invalid-feedback d-none" style="display: inline;">{{ trans('validation.required', ['attribute' => trans('models.common.province')]) }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            {!! MyForm::label('district_select', trans('models.common.district') )!!}
                            {!! MyForm::dropDown('district_select', $entity->district_id, $districtList, true, ['class' => 'select2']) !!}
                            <span class="invalid-feedback d-none" style="display: inline;">{{ trans('validation.required', ['attribute' => trans('models.common.district')]) }}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            {!! MyForm::label('ward_select', trans('models.common.ward')) !!}
                            {!! MyForm::dropDown('ward_select', $entity->ward_id, $wardList, true, ['class' => 'select2']) !!}
                            <span class="invalid-feedback d-none" style="display: inline;">{{ trans('validation.required', ['attribute' => trans('models.common.ward')]) }}</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            {!! MyForm::label('address', trans('models.common.address')) !!}
                            {!! MyForm::text('address', $entity->address, ['placeholder'=> trans('messages.input_address')])  !!}
                        </div>
                    </div>
                </div>
                <div class="map" id="map" style="height: 300px;"></div>
            </div>
            <div class="modal-footer">
                {{--{!! MyForm::text('location', empty($entity->address) ? '' : $entity->getCurrentLocation(), ['id'=>'location']) !!}--}}
                {!! MyForm::text('location', empty($entity->address) ? '' : method_exists($entity,'getCurrentLocation')?$entity->getCurrentLocation():'', ['id'=>'location','readonly' => 'readonly']) !!}
                <button id="location_submit" type="button" class="btn btn-info waves-effect waves-light"
                        data-dismiss="modal">{{trans('actions.done')}}
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let currentLatitude = '{!! empty($entity->latitude) ? 0 : $entity->latitude !!}',
        currentLongitude = '{!! empty($entity->longitude) ? 0 : $entity->longitude !!}';
    function stopRKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode === 13) && (node.type === "text")) {
            return false;
        }
    }
    document.onkeypress = stopRKey;
</script>
@push('scripts')
    <?php
    $jsFiles = [
        'vendor/utils/location_map'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush