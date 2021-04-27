<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['province.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card-box">
                    @include('layouts.backend.elements._form_label')

                    <div class="form-group m-t-30">
                        {!! MyForm::label('province_id', $entity->tA('province_id') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('province_id', $entity->province_id, ['placeholder'=>$entity->tA('province_id')]) !!}
                    </div>
                    <div class="form-group m-t-30">
                        {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('type', $entity->tA('type')) !!}
                        {!! MyForm::dropDown('type', $entity->type, config('system.province'), false) !!}
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>