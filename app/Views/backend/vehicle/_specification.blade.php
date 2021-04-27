@if(!empty($vehicle_config_specification_list))
    <div class="tab-pane fade" id="vehicle_specification">
        @foreach($vehicle_config_specification_list as $vehicle_config)
            <div class="form-group">
                {!! MyForm::hidden('vehicle_specification['.$vehicle_config->id.'][id]', $vehicle_specification_list[$vehicle_config->id]['id']) !!}
                {!! MyForm::label($vehicle_config->name, $vehicle_config->name, [], false) !!}
                @if($vehicle_config->type==1)
                    {!! MyForm::text('vehicle_specification['.$vehicle_config->id.'][value]', $vehicle_specification_list[$vehicle_config->id]['value'], ['placeholder'=> $vehicle_config->name]) !!}
                @elseif($vehicle_config->type==2)
                    {!! MyForm::text('vehicle_specification['.$vehicle_config->id.'][value]', numberFormat($vehicle_specification_list[$vehicle_config->id]['value']), ['class' => 'number-input','placeholder'=> $vehicle_config->name]) !!}
                @else
                    {!! MyForm::date('vehicle_specification['.$vehicle_config->id.'][value]', $vehicle_specification_list[$vehicle_config->id]['value'], ['placeholder'=> $vehicle_config->name, 'class' => 'datepicker date-input']) !!}
                @endif
            </div>
        @endforeach
    </div>
@endif