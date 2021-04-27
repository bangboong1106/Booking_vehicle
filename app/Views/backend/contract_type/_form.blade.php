<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['contract-type.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">

                        {!! MyForm::hidden('id', $entity->id) !!}
                        <div class="form-group">
                            {!! MyForm::label('name', $entity->tA('name'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('description', $entity->tA('description'), [], false) !!}
                            {!! MyForm::text('description', $entity->description, ['placeholder'=>$entity->tA('description')]) !!}
                        </div>
                    </div>
                </div>
                @include('layouts.backend.elements._submit_form_button')
            </div>
        </div>
    </div>
    {!! MyForm::close() !!}
</div>
</div>