{!! MyForm::hidden('vehicleGeneralInfo[id]', isset($isDuplicate) ? '' : $entity->tryGet('vehicleGeneralInfo')->id) !!}
<div class="form-group row">
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[max_fuel]', trans('models.vehicle_general_info.attributes.max_fuel'), [], false) !!}
        <div class="input-group">
            {!! MyForm::text('vehicleGeneralInfo[max_fuel]', numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel),
            ['placeholder'=>trans('models.vehicle_general_info.attributes.max_fuel'),'class' => 'number-input']) !!}
            <div class="input-group-prepend">
                <span class="input-group-text form-group-right">
                    lít
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[max_fuel_with_goods]', trans('models.vehicle_general_info.attributes.max_fuel_with_goods'), [], false) !!}
        <div class="input-group">
            {!! MyForm::text('vehicleGeneralInfo[max_fuel_with_goods]', numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel_with_goods),
            ['placeholder'=>trans('models.vehicle_general_info.attributes.max_fuel_with_goods'),'class' => 'number-input']) !!}
            <div class="input-group-prepend">
                <span class="input-group-text form-group-right">
                    lít
                </span>
            </div>
        </div>
    </div>

</div>
<div class="form-group row">
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[category_of_barrel]', trans('models.vehicle_general_info.attributes.category_of_barrel'), [], false) !!}
        {!! MyForm::text('vehicleGeneralInfo[category_of_barrel]', $entity->tryGet('vehicleGeneralInfo')->category_of_barrel) !!}
    </div>
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[weight_lifting_system]', trans('models.vehicle_general_info.attributes.weight_lifting_system'), [], false) !!}
        {!! MyForm::text('vehicleGeneralInfo[weight_lifting_system]', $entity->tryGet('vehicleGeneralInfo')->weight_lifting_system) !!}
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[register_year]', trans('models.vehicle_general_info.attributes.register_year'), [], false) !!}
        {!! MyForm::number('vehicleGeneralInfo[register_year]', $entity->tryGet('vehicleGeneralInfo')->register_year) !!}
    </div>
    <div class="col-md-6">
        {!! MyForm::label('vehicleGeneralInfo[brand]', trans('models.vehicle_general_info.attributes.brand'), [], false) !!}
        {!! MyForm::text('vehicleGeneralInfo[brand]', $entity->tryGet('vehicleGeneralInfo')->brand) !!}
    </div>
</div>