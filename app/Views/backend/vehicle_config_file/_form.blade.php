<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['vehicle-config-file.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    {!! MyForm::hidden('id', $entity->id) !!}
                    <div class="content content-body">

                        <div class="form-group">
                            {!! MyForm::label('file_name', $entity->tA('file_name'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('file_name', $entity->file_name, ['placeholder'=>$entity->tA('file_name')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('allow_extension', $entity->tA('allow_extension')) !!}
                            {!! MyForm::dropDown('allow_extension', $entity->allow_extension, $fileTypes, true) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('is_show_register', $entity->tA('is_show_register')) !!}
                            {!! MyForm::dropDown('is_show_register', $entity->is_show_register, $options, false) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('is_show_expired', $entity->tA('is_show_expired')) !!}
                            {!! MyForm::dropDown('is_show_expired', $entity->is_show_expired, $options, false) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('active', $entity->tA('active')) !!}
                            {!! MyForm::dropDown('active', $entity->active, $actives, false) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                            {!! MyForm::textarea('note', $entity->note,['rows'=>3]) !!}
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