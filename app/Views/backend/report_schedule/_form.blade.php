<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['report-schedule.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    {!! MyForm::hidden('id', $entity->id) !!}
                    <div class="content-body">
                        <div class="form-group">
                            {!! MyForm::label('email', 'Gửi email tới' . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('email', $entity->email, ['placeholder'=>$entity->tA('email')]) !!}
                            <p class="help-block m-b-0">
                                <small>Chú ý : Để gửi cho nhiều mail vui lòng thêm dấu ',' vào giữa mỗi mail . <br>
                                    VD : mail1@gmail.com,mail2@gmail.com
                                </small>
                            </p>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('description', $entity->tA('description'), [], false) !!}
                            {!! MyForm::text('description', $entity->description, ['placeholder'=>$entity->tA('description')]) !!}
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-5">
                                    {!! MyForm::label('date_from', $entity->tA('date_from'), [], false) !!}
                                    {{--{!! MyForm::text('date_from', $entity->date_from, ['placeholder'=>$entity->tA('date_from'), 'class'=>'datepicker']) !!}--}}
                                    {!! MyForm::text('date_from',$entity->getDateTime('date_from','d-m-Y') , ['placeholder'=>$entity->tA('date_from'), 'class'=>'datepicker']) !!}
                                </div>
                                <div class="col-md-5">
                                    {!! MyForm::label('date_to', $entity->tA('date_to'), [], false) !!}
                                    {{--{!! MyForm::text('date_to', $entity->date_to, ['placeholder'=>$entity->tA('date_to'), 'class'=>'datepicker']) !!}--}}
                                    {!! MyForm::text('date_to',$entity->getDateTime('date_to','d-m-Y') , ['placeholder'=>$entity->tA('date_to'), 'class'=>'datepicker']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('schedule_type', $entity->tA('schedule_type')) !!}
                            {!! MyForm::dropDown('schedule_type', $entity->schedule_type, $schedule_type, false) !!}
                        </div>
                        <div class="form-group">
                            <label>Thời gian</label>
                            {!! MyForm::text('time_to_send', $entity->time_to_send, ['placeholder'=>$entity->tA('time_to_send'), 'class'=>'timepicker']) !!}
                        </div>

                        <div class="advanced form-group row">
                            <div class="col-md-12">
                                <label>Loại báo cáo </label><span class="text-danger">*</span>
                                <div class="input-group select2-bootstrap-prepend">
                                    <select class="select2" id="report_type"
                                            name="report_type[]"
                                            multiple='multiple'>
                                        @if($report_type)
                                            @foreach($report_type as $key => $value)
                                                <option value="{{$key}}"
                                                        {{strpos($reportTypeList, "".$key) !== false ? "selected='selected'" : ""}}
                                                        title="{{$value}}">{{$value}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.backend.elements._submit_form_button')
            </div>
        </div>
    </div>
    {!! MyForm::close() !!}
</div>