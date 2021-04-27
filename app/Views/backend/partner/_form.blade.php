<?php
$disabled = '';
if ($entity->id != null && !strpos($routeName, 'duplicate')) {
    $disabled = 'disabled';
}
?>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, [
            'route' => [empty($formAdvance) ? 'partner.valid' : 'partner.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation,
            'autocomplete' => 'off'
        ])!!}
        {!! MyForm::hidden('id', isset($isDuplicate) ? '' : $entity->id) !!}
        <div class="row">
            <div class="col-md-12" id="partner_model">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('code', $entity->tA('code'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('partner_code')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('full_name', $entity->tA('full_name'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('full_name', $entity->full_name, ['placeholder'=>$entity->tA('full_name')]) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                {!! MyForm::label('mobile_no', $entity->tA('mobile_no'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('mobile_no', $entity->mobile_no, ['placeholder'=>$entity->tA('mobile_no')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('email', $entity->tA('email'), [], false) !!}
                                {!! MyForm::email('email', $entity->email,
                                    ['placeholder'=>$entity->tA('email')]) !!}</div>
                        </div>
                        <div class="form-group row corporate">
                            <div class="col-md-6">
                                {!! MyForm::label('delegate', trans('models.partner.attributes.delegate'), [], false) !!}
                                {!! MyForm::text('delegate', $entity->delegate, ['placeholder'=>trans('models.partner.attributes.delegate')]) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::label('tax_code', trans('models.partner.attributes.tax_code'), [], false) !!}
                                {!! MyForm::text('tax_code', $entity->tax_code, ['placeholder'=>trans('models.partner.attributes.tax_code')]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('current_address', $entity->tA('current_address'), [], false) !!}
                            {!! MyForm::text('current_address', $entity->current_address, ['placeholder'=>trans('models.partner.attributes.current_address')]) !!}
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
        {!! MyForm::close() !!}
    </div>
</div>