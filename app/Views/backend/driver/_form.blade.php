<script>

    let urlVehicle = '{{route('vehicle.combo-vehicle')}}';
    let backendUri = '{{getBackendDomain()}}';

</script>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['driver.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#driver_account" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{trans('models.driver.attributes.information')}}
                            </a>
                        </li>
                        @if(!$driver_config_list->isEmpty())
                            <li class="nav-item">
                                <a href="#driver_file" data-toggle="tab" aria-expanded="false" class="nav-link">
                                    {{trans('models.driver.attributes.files_info')}}
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="driver_account">
                            {!! MyForm::hidden('adminUser[id]', isset($isDuplicate) ? '' : $entity->tryGet('adminUser')->id) !!}
                            {!! MyForm::hidden('adminUser[role]', 'driver') !!}
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <div class="dropzone-outer previewsContainer"></div>
                                    <div class="dropzone text-center" id="avatar" data-id="avatar"
                                         data-file_type="1"></div>
                                    {!! MyForm::hidden('avatar_id', $entity->avatar_id, ['id' => 'avatar_id']) !!}
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group row create_account">
                                        <div class="col-md-3">
                                            {!! MyForm::label('open_account', 'Mở tài khoản', [], false) !!}
                                            <div>
                                                {!! MyForm::checkbox('create_account',$create_account_flag ? 1 : 0, $create_account_flag, ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'create_account']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            {!! MyForm::label('adminUser[active]', trans('models.admin.attributes.active'), [], false) !!}
                                            <input hidden="hidden" name="adminUser[active]" id="form_is_active"
                                                   value="{{ $entity->tryGet('adminUser')->active }}"/>
                                            <div>
                                                {!! MyForm::checkbox('switchery_is_active', $entity->tryGet('adminUser')->active,
                                                $entity->tryGet('adminUser')->active == 1,
                                                ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_active']) !!}
                                                <span> </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            {!! MyForm::label('ready_status', $entity->tA('ready_status'), [], false) !!}
                                            <input hidden="hidden" name="ready_status" id="form_ready_status"
                                                   value="{{ $entity->ready_status }}"/>
                                            <div>
                                                {!! MyForm::checkbox('switchery_ready_status', $entity->ready_status,
                                                $entity->ready_status == 1,
                                                ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_ready_status']) !!}
                                                <span> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row create_account_form row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('code', $entity->tA('code'). ' <span class="text-danger">*</span>', [], false) !!}
                                            {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! MyForm::label('adminUser[username]', trans('models.admin.attributes.username'), [], false) !!}
                                            {!! MyForm::text('adminUser[username]', $entity->tryGet('adminUser')->username,['placeholder'=>trans('models.admin.attributes.username'),'id'=>'user_name']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group row create_account_form row">
                                        <div class="col-md-6">
                                            {!! MyForm::label('adminUser[email]', trans('models.admin.attributes.email'), [], false) !!}
                                            {!! MyForm::email('adminUser[email]', $entity->tryGet('adminUser')->email,['placeholder'=>trans('models.admin.attributes.email'),'id'=>'email']) !!}
                                        </div>
                                    </div>
                                    <div class="row create_account_form form-group">
                                        <div class="col-md-6">
                                            {!! MyForm::label('adminUser[password]', trans('models.admin.attributes.password') , [], false ) !!}
                                            {!! MyForm::password('adminUser[password]',['placeholder'=>trans('models.admin.attributes.password'),'id'=>'password','autocomplete'=>'new-password']) !!}
                                            <p class="help-block">
                                                <small>{{trans('passwords.password')}}</small>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            {!! MyForm::label('adminUser[password_confirmation]', trans('models.admin.attributes.password_confirmation'), [], false ) !!}
                                            {!! MyForm::password('adminUser[password_confirmation]',['placeholder'=>trans('models.admin.attributes.password_confirmation'),'id'=>'password_confirmation']) !!}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('partner_id', $entity->tA('partner_id'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {!! MyForm::dropDown('partner_id', $entity->partner_id, $partnerList, true, [ 'class' => 'select2 minimal']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('full_name', $entity->tA('full_name'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {!! MyForm::text('full_name', $entity->full_name,['placeholder'=>$entity->tA('full_name')]) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('mobile_no', $entity->tA('mobile_no'). ' <span class="text-danger">*</span>', [], false) !!}
                                    {!! MyForm::number('mobile_no', $entity->mobile_no,['placeholder'=>$entity->tA('mobile_no')]) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('id_no', $entity->tA('id_no'), [], false) !!}
                                    {!! MyForm::text('id_no', $entity->id_no,['placeholder'=>$entity->tA('id_no')]) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('birth_date', $entity->tA('birth_date'), [], false) !!}
                                    {!! MyForm::text('birth_date',$entity->getDateTime('birth_date','d-m-Y') , ['placeholder'=>$entity->tA('birth_date'), 'class'=>'datepicker']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('sex', $entity->tA('sex'), [], false) !!}
                                    {!! MyForm::dropDown('sex', $entity->sex, $sexs, true, [ 'class' => 'select2 minimal']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('driver_license', $entity->tA('driver_license'), [], false) !!}
                                    {!! MyForm::dropDown('driver_license', $entity->driver_license, $driver_licenses, false, [ 'class' => 'select2 minimal']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {{-- <div class="col-md-6">
                                     {!! MyForm::label('vehicle_team_id', $entity->tA('vehicle_team_id'), [], false) !!}
                                     {!! MyForm::dropDown('vehicle_team_id', $entity->vehicle_team_id, $vehicle_team_list, true, [ 'class' => 'select2 minimal']) !!}
                                 </div>--}}

                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    {!! MyForm::label('experience_drive', $entity->tA('experience_drive')) !!}
                                    <div class="input-group">
                                        <input type="number" class="form-control" placeholder="Số năm"
                                               aria-label="{{$entity->tA('experience_drive')}}"
                                               aria-describedby="basic-addon1" name="experience_drive"
                                               value="{{ $entity->experience_drive}}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">năm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('work_date', $entity->tA('work_date'), [], false) !!}
                                    {!! MyForm::text('work_date', $entity->getDateTime('work_date','d-m-Y'), ['placeholder'=>$entity->tA('work_date'), 'class'=>'datepicker','id'=>'work_date'] ) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::label('experience_work', $entity->tA('experience_work'), [], false) !!}
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Số năm"
                                               aria-label="{{$entity->tA('experience_work')}}"
                                               aria-describedby="basic-addon1" name="experience_work"
                                               id="experience_work"
                                               value="{{ $entity->experience_work }}" readonly>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">năm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="form-group row">
                                <div class="col-md-12">
                                    {!! MyForm::label('vehicle_old', $entity->tA('vehicle_old'), [], false) !!}
                                    {!! MyForm::text('vehicle_old', $entity->vehicle_old, ['placeholder'=>$entity->tA('vehicle_old')]) !!}
                                </div>
                            </div>--}}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('address', $entity->tA('address'), [], false) !!}
                                    {!! MyForm::textarea('address', $entity->address, ['rows'=>2,'placeholder'=>$entity->tA('address')]) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::label('hometown', $entity->tA('hometown'), [], false) !!}
                                    {!! MyForm::textarea('hometown', $entity->hometown, ['rows'=>2,'placeholder'=>$entity->tA('hometown')]) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {!! MyForm::label('evaluate', $entity->tA('evaluate'), [], false) !!}
                                    {!! MyForm::textarea('evaluate', $entity->evaluate, ['rows'=>2,'placeholder'=>$entity->tA('evaluate')]) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::label('rank', $entity->tA('rank'), [], false) !!}
                                    {!! MyForm::textarea('rank', $entity->rank, ['rows'=>2,'placeholder'=>$entity->tA('rank')]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('work_description', $entity->tA('work_description'), [], false) !!}
                                {!! MyForm::textarea('work_description', $entity->work_description, ['rows'=>2,'placeholder'=>$entity->tA('work_description')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                                {!! MyForm::textarea('note', $entity->note,['rows'=>2,'placeholder'=>$entity->tA('note')]) !!}
                            </div>
                        </div>
                        @if(!$driver_config_list->isEmpty())
                            <div class="tab-pane fade" id="driver_file">
                                @foreach($driver_config_list as $driver_config)
                                    <div class="card-box">
                                        <h6 class="m-t-0 m-b-10 header-title">{!! $driver_config->file_name !!}</h6>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <div class="dropzone-outer previewsContainer_{{$driver_config->id}} ">
                                                </div>
                                                <div class="dropzone"
                                                     id={{$driver_config->id}} data-id="{{$driver_config->id}}"
                                                     data-file_type="{{$driver_config->allow_extension}}"></div>
                                                {!! MyForm::hidden('driver_file['.$driver_config->id.'][file_id]',
                                                $driver_file_list[$driver_config->id]->pluck('file_id')->implode(';'),
                                                ['id' => $driver_config->id.'_file_id']) !!}
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    @if($driver_config->is_show_register)
                                                        <div class="form-group">
                                                            {!! MyForm::label(trans('models.driver_config_file.attributes.register_date')) !!}
                                                            {!! MyForm::date('driver_file['.$driver_config->id.'][register_date]',$driver_file_list[$driver_config->id]->first()['register_date']) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col">
                                                    @if($driver_config->is_show_expired)
                                                        <div class="form-group">
                                                            {!! MyForm::label(trans('models.driver_config_file.attributes.expired_date')) !!}
                                                            {!! MyForm::date('driver_file['.$driver_config->id.'][expire_date]',$driver_file_list[$driver_config->id]->first()['expire_date']) !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
<script>
    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [];

    @foreach($driver_config_list as $driver_config)
    @foreach($driver_file_list[$driver_config->id] as $driver_file)
    @if(isset($driver_file['file_id']))
    existingFiles.push({
        name: '{{ $driver_file['file_name'] }}',
        size: '{{ $driver_file['size'] }}',
        type: 'image/jpeg',
        url: '{{ route('file.getImage',  $driver_file['file_id']) }}',
        urlDownload: '{{ route('file.downloadFile',$driver_file['file_id']) }}',
        full_url: '{{ route('file.getImage', ['id' =>  $driver_file['file_id'], 'full' => true]) }}',
        id: '{{  $driver_file['file_id'] }}',
        driver_config_id: '{{$driver_config->id}}'
    });
    @endif
    @endforeach
    @endforeach
    @if(!empty($entity->avatar_id))
    existingFiles.push({
        name: '{{ $entity->tryGet('avatarFile')->file_name }}',
        size: '{{ $entity->tryGet('avatarFile')->size }}',
        type: 'image/jpeg',
        url: '{{ route('file.getImage', $entity->tryGet('avatarFile')->file_id) }}',
        urlDownload: '{{ route('file.downloadFile',$entity->tryGet('avatarFile')->file_id) }}',
        full_url: '{{ route('file.getImage', ['id' => $entity->tryGet('avatarFile')->file_id, 'full' => true]) }}',
        id: '{{ $entity->tryGet('avatarFile')->file_id }}',
        driver_config_id: 'avatar'
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
<?php
$searchJsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($searchJsFiles, $area, 'js') !!}
