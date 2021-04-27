<div class="row">
    <div class="col-12 offset-3">
        {!! MyForm::model($entity, ['route' => ['vehicle-group.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-6">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="form-group">
                        {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('name', $entity->tA('name') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('partner_id', $entity->tA('partner_id') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::dropDown('partner_id', $entity->partner_id, $partnerList, true,
                        [$entity->id != null ? 'readonly':'']) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('parent_id', $entity->tA('parent_id')) !!}
                        {!! MyForm::dropDown('parent_id', $entity->parent_id, $vehicle_groups, true) !!}
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>