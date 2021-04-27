<div class="form-info-wrap" data-id="{{$entity->id}}" id="driver_model" data-quicksave='{{route('driver.quickSave')}}'
     data-entity='driver'>
    @if($show_history)
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
                               href="#">{{trans('models.order.attributes.information')}}</a></li>
                        <li><a class="list-info" data-dest="headingFiles"
                               href="#">{{trans('models.driver.attributes.files_info')}}</a></li>
                        <li><a class="list-info" data-dest="headingSystem"
                               href="#"> Thông tin hệ thống</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingHistory"
                               href="#">{{trans('models.auditing.name')}}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? "width-related-list" : "" }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row form-info" data-id="{{$entity->id}}">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action', ['exportEntityType' => config('constant.DRIVER')])
                        </div>
                    @endif
                    <div class="col-md-12 content-body">
                        <div class="content-detail">
                            <div class="{{isset($showAdvance) ? 'first' : ''}} card-header" role="tab"
                                 id="headingInformation">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                       aria-controls="collapseInformation" class="">
                                        {{trans('models.driver.attributes.information')}}
                                        <i class="fa"></i>

                                    </a>
                                </h5>
                            </div>
                            <div id="collapseInformation" class="collapse show" role="tabpanel"
                                 aria-labelledby="headingOne"
                                 style="">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('avatar', $entity->tA('avatar'), [], false) !!}
                                            <br/>
                                            @if ($entity->avatar_id)
                                                <img src="{{ route('file.getImage', ['id' => $entity->avatar_id, 'full' => true]) }}"
                                                     class="img-fluid" style="width: 200px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
                                        <div class="col-md-4 edit-group-control">
                                            <label for="active">{{ trans('models.admin.attributes.active') }}</label><br>
                                            <p>{{ $entity->tryGet('adminUser')->active == 1 ? trans('models.admin.attributes.activate') :
                                            trans('models.admin.attributes.disable')}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'username', 'isEditable' => false, 'value' => $entity->tryGet('adminUser')->username ? $entity->tryGet('adminUser')->username : '-'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'email', 'isEditable' => false, 'value' => $entity->tryGet('adminUser')->email ? $entity->tryGet('adminUser')->email : '-'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'password', 'isEditable' => false, 'value' =>$entity->tryGet('adminUser')->passwordText()])

                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'full_name'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'mobile_no'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'id_no'])

                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'sex', 'isEditable' => false, 'value' =>$entity->getSexText()])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'birth_date', 'controlType' =>'date'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'driver_license'])

                                    </div>
                                    <div class="form-group row">
                                        <?php
                                        $tempVehicleTeamList = '';
                                        foreach ($entity->vehicleTeams as $index => $vehicleTeam) {
                                            $tempVehicleTeamList .= '<a class="vehicle-team-detail" href="#"
                                                              data-show-url="' . (isset($showAdvance) ? route('vehicle-team.show', $vehicleTeam->id) : '') . '"
                                                   data-id="' . $vehicleTeam->id . '"><span class="tag-order">' . $vehicleTeam->name . '</span></a>';
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'vehicle-team',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'widthWrap'=>'col-md-8',
                                            'value'=> $tempVehicleTeamList
                                            ])
                                    </div>
                                    <div class="form-group row">
                                        <?php
                                        $tempVehicleList = '';
                                        if (!empty($entity->id)) {
                                            foreach ($entity->vehicles as $index => $vehicle) {
                                                $tempVehicleList .= '<a class="vehicle-team-detail" href="#"
                                                                  data-show-url="' . (isset($showAdvance) ? route('vehicle.show', $vehicle->id) : '') . '"
                                                       data-id="' . $vehicle->id . '"><span class="tag-order">' . $vehicle->reg_no . '</span></a>';
                                            }
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                            'property' => 'vehicle',
                                            'isEditable' => false,
                                            'controlType' => 'label',
                                            'widthWrap'=>'col-md-8',
                                            'value'=> $tempVehicleList
                                            ])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'work_date', 'controlType' =>'date'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'experience_drive', 'controlType' =>'number'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'experience_work', 'controlType' =>'number'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'address', 'controlType' =>'text'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'hometown', 'controlType' =>'text'])

                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'evaluate', 'controlType' =>'text'])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'rank', 'controlType' =>'text'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'work_description', 'controlType' =>'textarea', 'widthWrap' => 'col-md-12'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'note', 'controlType' =>'textarea', 'widthWrap' => 'col-md-12'])
                                    </div>
                                </div>
                            </div>
                            {{--Thông tin file--}}
                            <div class="card-header" role="tab" id="headingFiles">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseFiles" aria-expanded="true"
                                       aria-controls="collapseFile">
                                        {{trans('models.driver.attributes.files_info')}}
                                        <i class="fa"></i>

                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFiles"
                                 role="tabpanel"
                                 aria-labelledby="headingFiles">
                                <div class="card-body">
                                    @foreach($driver_config_list as $driver_config)
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                {!! MyForm::label($driver_config->file_name, $driver_config->file_name , [], false) !!}
                                                @if(isset($driver_file_list[$driver_config->id]['file_id']))
                                                    @php $file_id_list = explode(';' ,$driver_file_list[$driver_config->id]['file_id']) @endphp
                                                    <div class="row mt-2">
                                                        @foreach($file_id_list as $file_id)
                                                            @if(isset($file_id) && !empty($file_id))
                                                                <div class="col-md-3"><img
                                                                            src="{{ route('file.getImage', ['id' => $file_id, 'full' => true]) }}"
                                                                            class="img-fluid"></div>
                                                                <div class="text-center">
                                                                    <a id="download_file" class="fa fa-download"
                                                                       target="_blank"
                                                                       href="{{ route('file.downloadFile',$file_id) }}"></a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            @if(isset($driver_file_list[$driver_config->id]['register_date']))
                                                                <div class="form-group">
                                                                    {!! MyForm::label(trans('models.driver_config_file.attributes.register_date')) !!}
                                                                    <br/> {!! !empty($driver_file_list[$driver_config->id]['register_date']) ? \Carbon\Carbon::parse($driver_file_list[$driver_config->id]['register_date'])->format('d-m-Y') :"" !!}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-4">
                                                            @if(isset($driver_file_list[$driver_config->id]['expire_date']))
                                                                <div class="form-group">
                                                                    {!! MyForm::label(trans('models.driver_config_file.attributes.expired_date')) !!}
                                                                    <br/>{!! !empty($driver_file_list[$driver_config->id]['expire_date']) ? \Carbon\Carbon::parse($driver_file_list[$driver_config->id]['expire_date'])->format('d-m-Y') : "" !!}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                            </div>
                            @if($show_history)
                                {{--Thông tin hệ thống--}}
                                <div class="card-header" role="tab" id="headingSystem">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseSystem" aria-expanded="true"
                                           aria-controls="collapseNote" class="collapse-expand">
                                            Thông tin hệ thống
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseSystem" class="collapse show"
                                     role="tabpanel" aria-labelledby="note_info">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '', 'isEditable' => false])
                                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                        </div>
                                        <div class="form-group row">
                                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '', 'isEditable' => false])
                                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
            </li>
            @include('layouts.backend.elements.auditing._show_auditing')
        </ul>
    </div>
</div>
<script>
    let urlVehicle = '{{route('vehicle.combo-vehicle')}}';
    let vehicleUri = '{{route('quicksearch.vehicle')}}';
    let backendUri = '{{getBackendDomain()}}';
    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        allowEditableControlOnForm(editableFormConfig);
    }
</script>