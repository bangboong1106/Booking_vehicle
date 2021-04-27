<?php
$disabled = '';
if ($entity->id != null && !strpos($routeName, 'duplicate')) {
    $disabled = 'disabled';
}
?>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['partner-admin.valid', $entity->id]])!!}
        {!! MyForm::hidden('role', 'partner',['id'=>'role']) !!}
        {!! MyForm::hidden('partner_id', $entity->id ? $entity->partner_id : $partnerId  ,['id'=> 'partner_id'])!!}
        <div class="card-box form-display">
            <div class="col-md-12">
                @include('layouts.backend.elements._form_label')
                <div class="row content-body">
                    <div class=" col-md-4">
                        <div class="dropzone-outer previewsContainer"></div>
                        <div class="dropzone" id="avatar" style="height: 250px;"></div>
                        {!! MyForm::hidden('avatar_id', $entity->avatar_id, ['id' => 'avatar_id']) !!}
                    </div>
                    <div class="col-md-8">
                        <div class="content-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('username', $entity->tA('username') . ' <span class="text-danger">*</span>', [], false) !!}
                                        {!! MyForm::text('username', $entity->username, ['placeholder'=>$entity->tA('username')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('email', $entity->tA('email') . ' <span class="text-danger">*</span>', [], false) !!}
                                        {!! MyForm::email('email', $entity->email, ['placeholder'=>$entity->tA('email')]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('full_name', $entity->tA('full_name'), [], false) !!}
                                        {!! MyForm::text('full_name', $entity->full_name, ['placeholder'=>$entity->tA('full_name')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('active', $entity->tA('active'), [], false) !!}
                                        <input hidden="hidden" name="active" id="form_is_active"
                                               value="{{ $entity->active }}"/>
                                        <div>
                                            {!! MyForm::checkbox('switchery_is_active', $entity->active, $entity->active  == 1
                                            , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_active']) !!}
                                            <span> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('password', $entity->tA('password') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                        {!! MyForm::password('password',['placeholder'=>$entity->tA('password'), 'autocomplete'=>"new-password"]) !!}
                                        {!! $entity->id ? '<p class="help-block">'.$entity->tA('password_note_text').'</p>' : '' !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! MyForm::label('password_confirmation', $entity->tA('password_confirmation') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                        {!! MyForm::password('password_confirmation',['placeholder'=>$entity->tA('password_confirmation')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! MyForm::label('role', $entity->tA('role')) !!}
                                <div class="row" style="padding: 0 8px;">
                                    @foreach($roles as $role)
                                        <div class="custom-control custom-checkbox col-md-4">
                                            <input type="checkbox" class="custom-control-input" id="role_{{$role->id}}"
                                                   name="listRole[]"
                                                   disabled
                                                   checked
                                                   value="{{$role->name}}">
                                            <label style="    margin-left: 12px;" class="custom-control-label"
                                                   for="role_{{$role->id}}">{{$role->title}}</label>
                                            @if($entity->hasRole($role->name))
                                                <input type="hidden" name="current_roles[]" value="{{ $role->title }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                {!! MyForm::label('vehicleTeams', $entity->tA('driver_team'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('listVehicleTeam[]', isset($entity->listVehicleTeam) ? $entity->listVehicleTeam : $entity->vehicleTeams, $vehicleTeamList, false,
                                    ['multiple' => 'multiple', 'class' => 'select2 minimal', 'style'=>'visibility: hidden']) !!}
                                </div>
                                @if(isset($currentListVehicleTeam))
                                    @foreach($currentListVehicleTeam as $item)
                                        <input type="hidden" name="current_vehicle_teams[]" value="{{ $item }}">
                                    @endforeach
                                @endif
                            </div>

                            <div class="form-group admin_form">
                                {!! MyForm::label('customer_groups', $entity->tA('customer_groups'), [], false) !!}
                                <div class="input-group">
                                    {!! MyForm::dropDown('listCustomerGroup[]', isset($entity->listCustomerGroup) ? $entity->listCustomerGroup : $entity->customer_groups, $customerGroupList, false,
                                    ['multiple' => 'multiple', 'class' => 'select2 minimal', 'style'=>'visibility: hidden']) !!}
                                </div>
                                @if(isset($currentListCustomerGroups))
                                    @foreach($currentListCustomerGroups as $item)
                                        <input type="hidden" name="current_customer_groups[]" value="{{ $item }}">
                                    @endforeach
                                @endif
                            </div>

                        </div>

                    </div>
                </div>

            </div>


        </div>
        @include('layouts.backend.elements._submit_form_button')
        {!! MyForm::close() !!}
    </div>
</div>

<script>
    var token = '{!! csrf_token() !!}',
        url = '{{ route('file.uploadFile') }}',
        urlDownload = '{{ route('file.downloadFile',999) }}',
        existingFiles = [],
        removeUrl = '{{ route('file.destroy', 999) }}';
    @if(!empty($entity->avatar_id))
    existingFiles.push({
        name: '{{ $entity->tryGet('avatarFile')->file_name }}',
        size: '{{ $entity->tryGet('avatarFile')->size }}',
        type: 'image/jpeg',
        url: '{{ route('file.getImage', $entity->tryGet('avatarFile')->file_id) }}',
        urlDownload: '{{ route('file.downloadFile',$entity->tryGet('avatarFile')->file_id) }}',
        full_url: '{{ route('file.getImage', ['id' => $entity->tryGet('avatarFile')->file_id, 'full' => true]) }}',
        id: '{{ $entity->tryGet('avatarFile')->file_id }}'
    });
    @endif
</script>

<div class="modal" id="preview-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4>&nbsp;</h4>
            </div>
            <div class="modal-body">
                <img src="" id="preview" width="100%">
            </div>
        </div>
    </div>
</div>