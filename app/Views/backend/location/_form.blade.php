<div class="row">
    <div class="col-12" id="location_model">
        {!! MyForm::model($entity, [
            'route' => [empty($formAdvance) ? 'location.valid' : 'location.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation
        ])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content-body content-detail">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! MyForm::label('customer_id', trans('models.customer.name'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('customer_id', $entity->customer_id, $customers, true, ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('customer_id') !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! MyForm::label('limited_day', $entity->tA('limited_day'), [], false) !!}
                                {!! MyForm::text('limited_day', numberFormat($entity->limited_day), ['placeholder'=>$entity->tA('limited_day')]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! MyForm::label('location_group_id', $entity->tA('location_group_id'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('location_group_id', $entity->location_group_id, $locationGroups, true, ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('location_group_id') !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! MyForm::label('location_type_id', $entity->tA('location_type_id'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('location_type_id', $entity->location_type_id, $locationTypes, true, 
                                    ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('location_type_id') !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! MyForm::label('address_pac', trans('models.common.address_search')) !!}
                            {!! MyForm::text('address_pac',$entity->address_pac, ['id'=>'address_pac', 'placeholder' => trans('models.common.address_placeholder')]) !!}
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! MyForm::label('province_id', trans('models.common.province'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('province_id', $entity->province_id, $provinceList, true, ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('province_id') !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! MyForm::label('district_id', trans('models.common.district'), [], false)!!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('district_id', $entity->district_id, $districtList, true, ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('district_id') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {!! MyForm::label('ward_id', trans('models.common.ward')) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('ward_id', $entity->ward_id, $wardList, true, ['class' => 'select2']) !!}
                                </div>
                                {!! MyForm::error('ward_id') !!}
                            </div>
                            <div class="form-group col-md-6">
                                {!! MyForm::label('address', trans('models.common.address')) !!}
                                {!! MyForm::text('address', $entity->address, ['placeholder'=> trans('messages.input_address')])  !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                {!! MyForm::label('full_address', trans('models.location.attributes.display_address')) !!}
                                {!! MyForm::text('full_address', $entity->full_address, [
                                    'placeholder'=> trans('messages.input_address'),
                                    'readonly' => 'readonly'
                                ]) !!}
                            </div>
                        </div>
                        <div class="map m-b-20" id="map_location" style="height: 300px;"></div>
                        @include('layouts.backend.elements._submit_form_button')
                    </div>

                </div>
            </div>
        </div>

        {!! MyForm::hidden('latitude', $entity->latitude ,['id'=>'latitude', 'class' => 'latitude']) !!}
        {!! MyForm::hidden('longitude', $entity->longitude,['id'=>'longitude', 'class' => 'longitude']) !!}
        {!! MyForm::close() !!}
    </div>
</div>
<script>
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

@if(empty($formAdvance))
    @push('scripts')
        {!! loadFiles(['vendor/lib/locationObject'], $area, 'js') !!}
    @endpush
@endif