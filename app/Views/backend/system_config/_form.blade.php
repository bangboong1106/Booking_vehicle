<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['system-config.valid', $entity->id]])!!}
        <div class="row">
            <div class="offset-md-3 col-md-6">
                <div class="card-box">
                    @include('layouts.backend.elements._form_label')
                    <div class="form-group">
                        {!! MyForm::label('key', $entity->tA('key') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('key', $entity->key, ['placeholder'=>$entity->tA('key')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('value', $entity->tA('value') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('value', $entity->value, ['placeholder'=>$entity->tA('value')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('description', $entity->tA('description'), [], false) !!}
                        {!! MyForm::text('description', $entity->description, ['placeholder'=>$entity->tA('description')]) !!}
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>