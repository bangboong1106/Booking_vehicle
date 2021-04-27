@if(!$vehicle_config_file_list->isEmpty())
    <div class="tab-pane fade" id="vehicle_file">
        @foreach($vehicle_config_file_list as $vehicle_config)
            <div class="card-box">
                <h6 class="m-t-0 m-b-10 header-title">{!! $vehicle_config->file_name !!}</h6>
                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="dropzone-outer previewsContainer_{{$vehicle_config->id}} ">
                        </div>
                        <div class="dropzone" id={{$vehicle_config->id}} data-id="{{$vehicle_config->id}}"
                             data-file_type="{{$vehicle_config->allow_extension}}"></div>
                        {!! MyForm::hidden('vehicle_file['.$vehicle_config->id.'][file_id]',
                         $vehicle_file_list[$vehicle_config->id]->pluck('file_id')->implode(';'),
                        ['id' => $vehicle_config->id.'_file_id']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        {!! MyForm::label(trans('models.vehicle_config_file.attributes.note')) !!}
                        {!! MyForm::text('vehicle_file['.$vehicle_config->id.'][note]',$vehicle_file_list[$vehicle_config->id]->first()['note']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        @if($vehicle_config->is_show_register)
                            <div class="form-group">
                                {!! MyForm::label(trans('models.vehicle_config_file.attributes.register_date')) !!}
                                {!! MyForm::date('vehicle_file['.$vehicle_config->id.'][register_date]',$vehicle_file_list[$vehicle_config->id]->first()['register_date']) !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if($vehicle_config->is_show_expired)
                            <div class="form-group">
                                {!! MyForm::label(trans('models.vehicle_config_file.attributes.expired_date')) !!}
                                {!! MyForm::date('vehicle_file['.$vehicle_config->id.'][expire_date]',$vehicle_file_list[$vehicle_config->id]->first()['expire_date']) !!}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endforeach
    </div>
@endif
