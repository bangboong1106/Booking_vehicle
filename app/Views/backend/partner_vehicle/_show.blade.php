<div class="form-info-wrap" data-id="{{ $entity->id }}" id="vehicle_model"
    data-quicksave='{{ route('vehicle.quickSave') }}' data-entity='vehicle'>
    @if ($show_history)
        <div class="related-list">
            <span class="collapse-view dIB detailViewCollapse dvLeftPanel_show"
                onclick="showHideDetailViewLeftPanel(this);" id="dv_leftPanel_showHide" style="">
                <span class="svgIcons dIB fCollapseIn"></span>
            </span>
            <ul class="list-related-list">
                <li>
                    <span class="title">Thông tin</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingInformation"
                                href="#">{{ trans('models.order.attributes.information') }}</a></li>
                        <li><a class="list-info" data-dest="headingFiles"
                                href="#">{{ trans('models.vehicle.attributes.files_info') }}</a></li>
                        <li><a class="list-info" data-dest="headingExpand"
                                href="#">{{ trans('models.vehicle.attributes.information_expand') }}</a></li>
                        <li><a class="list-info" data-dest="headingSystem" href="#"> Thông tin hệ thống</a></li>
                        <li><a class="list-info" data-dest="headingExpand"
                                href="#">{{ trans('models.vehicle.attributes.repair_ticket') }}</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingHistory"
                                href="#">{{ trans('models.auditing.name') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? 'width-related-list' : '' }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row">
                    @if (isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action', ['exportEntityType' =>
                            config('constant.VEHICLE')])
                        </div>
                    @endif
                    <div class="col-md-12 content-detail">
                        <div class="{{ isset($showAdvance) ? 'first' : '' }} card-header" role="tab"
                            id="headingInformation">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                    aria-controls="collapseInformation" class="">
                                    {{ trans('models.driver.attributes.information') }}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel" aria-labelledby="headingOne"
                            style="">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'reg_no',
                                    'isEditable' => false])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'status',
                                    'isEditable' => false, 'value' => $entity->getStatus()])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'type',
                                    'isEditable' => false, 'value' => $entity->getType()])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'active',
                                    'isEditable' => false, 'value' => $entity->getActive()])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'group_id',
                                    'isEditable' => false, 'value' => isset($entity->vehicleGroup) ?
                                    $entity->vehicleGroup->name : '-'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'current_location', 'isEditable' => false])
                                </div>
                                <div class="form-group row">
                                    @php $tempDriverList = ''; @endphp
                                    @if (isset($entity->listDriver))
                                        @foreach ($entity->listDriver as $driverId => $name)
                                            @php $tempDriverList .= '<a class="driver-detail" href="#"
                                                data-show-url="' . (isset($showAdvance) ? route('driver.show', $driverId) : '') . '"
                                                data-id="' . $driverId . '"><span class="tag-order">' . $name .
                                                    '</span></a>';
                                            @endphp
                                        @endforeach
                                    @endif
                                    @include('layouts.backend.elements.detail_to_edit',[
                                    'property' => 'driver',
                                    'isEditable' => false,
                                    'controlType' => 'label',
                                    'widthWrap'=>'col-md-8',
                                    'value'=> $tempDriverList
                                    ])
                                </div>

                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'volume',
                                    'controlType' =>'number'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'weight',
                                    'controlType' =>'number'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'length_width_height',
                                    'isEditable' => false,
                                    'value' => ($entity->length !=null ? numberFormat($entity->length) :
                                    numberFormat(0)).'*'.($entity->width !=null ? numberFormat($entity->width) :
                                    numberFormat(0)).'*'.($entity->height !=null ? numberFormat($entity->height) :
                                    numberFormat(0))])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'repair_distance',
                                    'controlType' =>'number', 'value'=> numberFormat($entity->repair_distance)])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'repair_date',
                                    'controlType' =>'date'])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'max_fuel','isEditable' => false, 'controlType' =>'number', 'value'=>
                                    numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel)])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'max_fuel_with_goods','isEditable' => false, 'controlType' =>'number', 'value'=>
                                    numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel_with_goods)])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'category_of_barrel', 'isEditable' => false,'value'=>
                                    ($entity->tryGet('vehicleGeneralInfo')->category_of_barrel)])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'weight_lifting_system','isEditable' => false, 'value'=>
                                    ($entity->tryGet('vehicleGeneralInfo')->weight_lifting_system)])

                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'register_year','isEditable' => false, 'value'=>
                                    $entity->tryGet('vehicleGeneralInfo')->register_year])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'brand','isEditable' => false, 'value'=>
                                    ($entity->tryGet('vehicleGeneralInfo')->brand)])
                                    @include('layouts.backend.elements.detail_to_edit',['property' =>
                                    'gps_company_id','isEditable' => false, 'value'=> isset($entity->gpsCompany) ?
                                    $entity->gpsCompany->name : '-'])
                                </div>
                            </div>
                        </div>
                        {{--Thông tin file--}}
                        <div class="card-header" role="tab" id="headingFiles">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseFiles" aria-expanded="true"
                                    aria-controls="collapseFile">
                                    {{ trans('models.vehicle.attributes.files_info') }}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseFiles" role="tabpanel" aria-labelledby="headingFiles">
                            <div class="card-body">
                                @foreach ($vehicle_config_file_list as $vehicle_config_file)
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                {!! MyForm::label($vehicle_config_file->file_name,
                                                $vehicle_config_file->file_name, [], false) !!}
                                                @if (isset($vehicle_file_list[$vehicle_config_file->id]['file_id']))
                                                    @php $file_id_list = explode(';'
                                                    ,$vehicle_file_list[$vehicle_config_file->id]['file_id']) @endphp
                                                    <div class="row col-md-12">
                                                        @foreach ($file_id_list as $file_id)
                                                            @include('layouts.backend.elements.detail_to_edit',[
                                                            'isHiddenLabel'=> true,
                                                            'isEditable' => false,
                                                            'controlType' =>'image',
                                                            'value'=> (isset($file_id) && !empty($file_id)) ?
                                                            route('file.getImage', ['id' => $file_id, 'full' => true]) :
                                                            '',
                                                            ])
                                                            @if ($file_id)
                                                                <div class="text-center">
                                                                    <a id="download_file" class="fa fa-download"
                                                                        target="_blank"
                                                                        href="{{ route('file.downloadFile', $file_id) }}"></a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                            </div>
                                            <div class="row mt-2">
                                                @include('layouts.backend.elements.detail_to_edit',[
                                                'property' => !isset($vehicle_file_list[$vehicle_config_file->id]) ?
                                                $vehicle_file_list[$vehicle_config_file->id]['note'] : '',
                                                'label'=> trans('models.vehicle_config_file.attributes.note'),
                                                'isEditable' => false,
                                                'value'=> empty($vehicle_file_list[$vehicle_config_file->id]['note']) ?
                                                '' :$vehicle_file_list[$vehicle_config_file->id]['note'] ,
                                                ])
                                            </div>
                                            <div class="row mt-2">
                                                @include('layouts.backend.elements.detail_to_edit',[
                                                'property' => !isset($vehicle_file_list[$vehicle_config_file->id]) ?
                                                $vehicle_file_list[$vehicle_config_file->id]['register_date'] : '',
                                                'label'=> trans('models.vehicle_config_file.attributes.register_date'),
                                                'isEditable' => false,
                                                'value'=>
                                                empty($vehicle_file_list[$vehicle_config_file->id]['register_date']) ?
                                                ''
                                                :\Carbon\Carbon::parse($vehicle_file_list[$vehicle_config_file->id]['register_date'])->format('d-m-Y')
                                                ,
                                                ])
                                                @include('layouts.backend.elements.detail_to_edit',[
                                                'property' => !isset($vehicle_file_list[$vehicle_config_file->id]) ?
                                                $vehicle_file_list[$vehicle_config_file->id]['expire_date'] : '',
                                                'label'=> trans('models.vehicle_config_file.attributes.expired_date'),
                                                'isEditable' => false,
                                                'value'=>
                                                empty($vehicle_file_list[$vehicle_config_file->id]['expire_date']) ? ''
                                                :\Carbon\Carbon::parse($vehicle_file_list[$vehicle_config_file->id]['expire_date'])->format('d-m-Y')
                                                ,
                                                ])
                                            </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{--Thông tin bổ sung--}}
                <div class="card-header" role="tab" id="headingExpand">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseExpand" aria-expanded="true"
                            aria-controls="collapseExpand">
                            {{ trans('models.vehicle.attributes.information_expand') }}
                            <i class="fa"></i>
                        </a>
                    </h5>
                </div>
                <div id="collapseExpand" role="tabpanel" aria-labelledby="headingExpand">
                    <div class="card-body">
                        @foreach ($vehicle_config_specification_list as $vehicle_config_specification)
                            <div class="form-group row mt-2">
                                @include('layouts.backend.elements.detail_to_edit',[
                                'property' => $vehicle_config_specification->name,
                                'label'=> $vehicle_config_specification->name,
                                'isEditable' => false,
                                'value'=> isset($vehicle_specification_list[$vehicle_config_specification->id]['value'])
                                ? $vehicle_specification_list[$vehicle_config_specification->id]['value'] : '-',
                                'widthWrap' => 'col-md-12'
                                ])
                            </div>

                        @endforeach
                    </div>
                </div>


                @if ($show_history)
                    <div class="card-header" role="tab" id="headingSystem">
                        <h5 class="mb-0 mt-0 font-16">
                            <a data-toggle="collapse" href="#collapseSystem" aria-expanded="true"
                                aria-controls="collapseNote" class="collapse-expand">
                                Thông tin hệ thống
                                <i class="fa"></i>
                            </a>
                        </h5>
                    </div>
                    <div id="collapseSystem" class="collapse show" role="tabpanel" aria-labelledby="note_info">
                        <div class="card-body">
                            <div class="form-group row">
                                @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=>
                                isset($entity->insUser) ? $entity->insUser->username : '-', 'isEditable' => false])
                                @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date',
                                'isEditable' => false, 'controlType'=>'datetime'])
                            </div>
                            <div class="form-group row">
                                @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=>
                                isset($entity->updUser) ? $entity->updUser->username : '-', 'isEditable' => false])
                                @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date',
                                'isEditable' => false, 'controlType'=>'datetime'])
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($showAdvance) &&
                auth()
                    ->user()
                    ->can('view repair_ticket'))
                                <div class="card-header" role="tab" id="headingRepairTicket">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseRepairTicket" aria-expanded="true"
                                            aria-controls="collapseRepairTicket" class="collapse-expand">
                                            {{ trans('models.vehicle.attributes.repair_ticket') }}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseRepairTicket" role="tabpanel" aria-labelledby="headingRepairTicket">
                                    <div class="card-body">
                                        <?php $repairTickets = $entity->repairTickets; ?>
                                        @if ($repairTickets->count() === 0)
                                            <p>{{ trans('messages.no_result_found') }}</p>
                                        @else
                                            <table class="table table-responsive table-striped m-b-0">
                                                <thead>
                                                    <tr>
                                                        <td>{{ trans('models.repair_ticket.attributes.code') }}</td>
                                                        <td>{{ trans('models.repair_ticket.attributes.name_of_driver_id') }}</td>
                                                        <td>{{ trans('models.repair_ticket.attributes.ins_date') }}</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($repairTickets as $item)
                                                        <tr>
                                                            <td>
                                                                <a class="view-detail-info" href="#" data-id="{{ $item->id }}"
                                                                    data-show-url="{{ route('repair-ticket.show', empty($item->id) ? 0 : $item->id) }}">
                                                                    {{ $item->code }}</a>
                                                            </td>
                                                            <td>{{ $item->driver->full_name }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($item->ins_date)->format('d-m-Y H:i')}} </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            @endif
                @if (isset($showAdvance) &&
                    isset($auditing) &&
                    auth()
                        ->user()
                        ->can('view auditing'))
                    <div class="card-header" role="tab" id="headingAudit">
                        <h5 class="mb-0 mt-0 font-16">
                            <a data-toggle="collapse" href="#collapseAudit" aria-expanded="true"
                                aria-controls="collapseExpand">
                                {{ trans('models.auditing.name') }}
                            </a>
                        </h5>
                    </div>
                    <div id="collapseAudit" role="tabpanel" aria-labelledby="headingExpand">
                        <div class="card-body">
                            @if ($auditing->count() === 0)
                                <p>{{ trans('messages.no_result_found') }}</p>
                            @else
                                <table id="mainTable" class="table table-responsive table-striped m-b-0">
                                    <thead>
                                        <tr>
                                            <td>{{ trans('models.auditing.attributes.username') }}</td>
                                            <td>{{ trans('actions.action') }}</td>
                                            <td>{{ trans('models.auditing.attributes.old_values') }}</td>
                                            <td>{{ trans('models.auditing.attributes.new_values') }}</td>
                                            <td>{{ trans('models.auditing.attributes.time') }}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditing as $item)
                                            <tr>
                                                <td>{{ $item->user->username }}</td>
                                                <td>{{ trans('models.auditing.actions.' . $item->event) }}</td>
                                                <td>
                                                    @foreach ($item->old_values as $attr => $value)
                                                        <p class="m-0">{{ $entity->tA($attr) }} : {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($item->new_values as $attr => $value)
                                                        <p class="m-0">{{ $entity->tA($attr) }} : {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>{{ $item->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endif
    </div>
</div>
</li>
@include('layouts.backend.elements.auditing._show_auditing')
</ul>
</div>
</div>
<script>
    let driverDropdownUri = '{{ route('driver.combo-driver') }}';
    let driverUri = '{{ route('quicksearch.driver') }}';
    let backendUri = '{{ getBackendDomain() }}';
    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        allowEditableControlOnForm(editableFormConfig);
    }

</script>
