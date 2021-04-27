<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, [ 
            'route' => [empty($formAdvance) ? 'system-code-config.valid' : 'system-code-config.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation,
            'autocomplete' => 'off'
        ]
     )!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">

                        <div class="form-group">
                            {!! MyForm::label('type', $entity->tA('type') . ' <span class="text-danger">*</span>', [],
                            false) !!}
                            {!! MyForm::dropDown('type', $entity->type, $types, false) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('prefix', $entity->tA('prefix') . ' <span class="text-danger">*</span>',
                            [], false) !!}
                            {!! MyForm::text('prefix', $entity->prefix, ['placeholder' => $entity->tA('prefix')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('is_generate_time', $entity->tA('is_generate_time'), [], false) !!}
                            <input hidden="hidden" name="is_generate_time" id="is_generate_time"
                                value="{{ $entity->is_generate_time }}" />
                            <div>
                                {!! MyForm::checkbox('switchery_is_generate_time', $entity->is_generate_time,
                                $entity->is_generate_time == '1' ? true : false, ['data-plugin' => 'switchery',
                                'data-color' => '#11509b', 'class' => 'switchery', 'id' =>
                                'switchery_is_generate_time']) !!}
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('time_format', $entity->tA('time_format'), [], false) !!}
                            {!! MyForm::text('time_format', $entity->time_format, ['placeholder' =>
                            $entity->tA('time_format')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('suffix_length', $entity->tA('suffix_length') . ' <span
                                class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::number('suffix_length', $entity->suffix_length, ['placeholder' =>
                            $entity->tA('suffix_length')]) !!}
                        </div>
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
