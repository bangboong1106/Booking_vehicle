<?php
$disabled = '';
if ($entity->id != null && !strpos($routeName, 'duplicate')) {
    $disabled = 'disabled';
}
?>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, [
            'route' => [empty($formAdvance) ? 'client.valid' : 'client.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation,
            'autocomplete' => 'off'
        ])!!}
        {!! MyForm::hidden('adminUser[id]', isset($isDuplicate) ? '' : $entity->tryGet('adminUser')->id) !!}
        {!! MyForm::hidden('adminUser[role]', 'customer') !!}
        <div class="row">
            <div class="col-md-12" id="customer_model">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">
                        <div class="form-group row" style="margin-top: 20px">
                            {!! MyForm::hidden('type', $entity->type == null ? 1: $entity->type,['id'=>'type']) !!}
                            <div class="col-md-6">
                                <div class="radio radio-info form-check-inline">
                                    <input type="radio"
                                           id="customer_type_1"
                                           value="1" name="type"
                                            {!! $disabled !!}
                                            {!! ($entity->type == 1 || empty($entity->type) )? 'checked' : '' !!}>
                                    <label for="customer_type_1"> Khách hàng doanh nghiệp </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="radio radio-info form-check-inline">
                                    <input type="radio" id="customer_type_2" value="2"
                                           name="type"
                                            {!! $disabled !!}
                                            {!! $entity->type == 2 ? 'checked' : '' !!}>
                                    <label for="customer_type_2" class="c"> Khách hàng cá nhân </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('parent_id', trans('models.client.attributes.parent_id'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::dropDown('parent_id', $entity->parent_id, $parentList, true, [ 'class' => 'select2 minimal']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('customer_code', trans('models.client.attributes.customer_code'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('customer_code', $entity->id != null ? $entity->customer_code : $customer_code , ['placeholder'=>$entity->tA('customer_code')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('full_name', trans('models.client.attributes.full_name'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('full_name', $entity->full_name, ['placeholder'=>$entity->tA('full_name')]) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('adminUser[username]', trans('models.admin.attributes.username') . ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('adminUser[username]', $entity->tryGet('adminUser')->username,['placeholder'=>trans('models.admin.attributes.username')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('adminUser[email]', trans('models.admin.attributes.email'), [], false) !!}
                                {!! MyForm::email('adminUser[email]', $entity->tryGet('adminUser')->email,
                                    ['placeholder'=>trans('models.admin.attributes.email')]) !!}</div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('adminUser[password]', trans('models.admin.attributes.password') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                {!! MyForm::password('adminUser[password]',['placeholder'=>trans('models.admin.attributes.password'),'autocomplete' => 'new-password']) !!}
                                <p class="help-block m-b-0">
                                    <small>{{trans('passwords.password')}}</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('adminUser[password_confirmation]', trans('models.admin.attributes.password_confirmation') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                {!! MyForm::password('adminUser[password_confirmation]',['placeholder'=>trans('models.admin.attributes.password_confirmation')]) !!}
                            </div>
                        </div>
                        <div class="form-group row corporate">
                            <div class="col-md-6">
                                {!! MyForm::label('delegate', trans('models.client.attributes.delegate') . ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('delegate', $entity->delegate, ['placeholder'=>trans('models.client.attributes.delegate')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('tax_code', trans('models.client.attributes.tax_code'), [], false) !!}
                                {!! MyForm::text('tax_code', $entity->tax_code, ['placeholder'=>trans('models.client.attributes.tax_code')]) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('mobile_no', $entity->tA('mobile_no'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('mobile_no', $entity->mobile_no, ['placeholder'=>$entity->tA('mobile_no')]) !!}
                            </div>
                        </div>
                        <div class="form-group row individual" style="{{$entity->type == 2 ? '': 'display: none;' }}">
                            <div class="col-md-6">
                                {!! MyForm::label('birth_date', $entity->tA('birth_date'), [], false) !!}
                                {!! MyForm::text('birth_date',$entity->getDateTime('birth_date','d-m-Y') , ['placeholder'=>$entity->tA('birth_date'), 'class'=>'datepicker']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('sex', $entity->tA('sex')) !!}
                                {!! MyForm::dropDown('sex', $entity->sex, $sexs, true, [ 'class' => 'minimal']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('address', $entity->tA('address'), [], false) !!}
                            <div class="input-group group-address {{empty($entity->current_address) ? 'not-address' : ''}}">
                                {!! MyForm::text('current_address', $entity->current_address,
                                    ['readonly'=>'readonly', 'id'=>'customer_address', 'class' => 'address-input']) !!}
                                <div class="input-group-append">
                                    <button id="input_location" type="button"
                                            class="btn btn-primary"
                                            data-toggle="modal" data-target="#map_modal"><i class="fa fa-map-marker"
                                                                                            title="Nhập vị trí"></i>
                                    </button>
                                    <button id="clear_location" class="btn btn-danger"
                                            type="button"><i class="fa fa-trash" title="Xóa"></i>
                                    </button>
                                </div>
                            </div>
                            @if(empty($formAdvance))
                                @include('layouts.backend.elements._map')
                            @endif
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                            {!! MyForm::textarea('note', $entity->note,['rows'=>3]) !!}
                        </div>
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::hidden('latitude', $entity->latitude ,['id'=>'latitude', 'class' => 'latitude']) !!}
        {!! MyForm::hidden('longitude', $entity->longitude,['id'=>'longitude', 'class' => 'longitude']) !!}
        {!! MyForm::hidden('province_id', $entity->province_id, ['id'=>'province_id', 'class' => 'province_id']) !!}
        {!! MyForm::hidden('district_id', $entity->district_id, ['id'=>'district_id', 'class' => 'district_id']) !!}
        {!! MyForm::hidden('ward_id', $entity->ward_id, ['id'=>'ward_id', 'class' => 'ward_id']) !!}
        {!! MyForm::hidden('address', $entity->address, ['id'=>'address-hidden', 'class' => 'address_text']) !!}
        {!! MyForm::hidden('customer_type', config("constant.KHACH_HANG"), ['id'=>'customer_type-hidden', 'class' => 'customer_type']) !!}
        {!! MyForm::close() !!}
    </div>
</div>
<script>
    let currentLat = '{{ empty($entity->latitude) ? 0 : $entity->latitude }}',
        currentLng = '{{ empty($entity->longitude) ? 0 : $entity->longitude }}';
</script>
