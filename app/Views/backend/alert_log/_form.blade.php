<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['alert-log.valid', $entity->id]])!!}
        <div class="row">
            <div class="offset-md-3 col-md-6">
                <div class="card-box">
                    @include('layouts.backend.elements._form_label')
                    {!! MyForm::hidden('id', $entity->id) !!}
                    <div class="form-group">
                        {!! MyForm::label('name', $entity->tA('name'). ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('title', $entity->tA('title'). ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('alert_type', $entity->tA('alert_type')) !!}
                        {!! MyForm::dropDown('alert_type', $entity->alert_type, $alert_types, false) !!}
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-7">
                                {!! MyForm::label('date_to_send', $entity->tA('date_to_send'), [], false) !!}
                                {!! MyForm::text('date_to_send', $entity->date_to_send, ['placeholder'=>$entity->tA('date_to_send'), 'class'=>'datepicker']) !!}
                            </div>
                            <div class="col-md-5">
                                <label>Thời gian</label>
                                {!! MyForm::text('time_to_send', $entity->time_to_send, ['placeholder'=>$entity->tA('time_to_send'), 'class'=>'timepicker']) !!}
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        {!! MyForm::label('content', $entity->tA('content'). ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::textarea('content', $entity->content, ['rows'=>2, 'placeholder'=>$entity->tA('content')]) !!}
                    </div>
                </div>
                @include('layouts.backend.elements._submit_form_button')
            </div>
        </div>
    </div>
    {!! MyForm::close() !!}
</div>
</div>