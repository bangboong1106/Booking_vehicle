@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-12">
            {!! MyForm::model($entity, [
                'route' => ['admin.profile', $entity->id],
                'autocomplete' => 'off',
            ])!!}
            <div class="card-box form-display">
                <div class="col-md-12">
                    @include('layouts.backend.elements._form_label')

                    <div class="row content-body">
                        <div class="admin col-md-4">
                            <div class="dropzone-outer previewsContainer"></div>
                            <div class="dropzone" id="avatar"></div>
                            {!! MyForm::hidden('avatar_id', $entity->avatar_id, ['id' => 'avatar_id']) !!}
                        </div>
                        <div class=" admin col-md-8">

                            <div class="form-group m-t-30">
                                {!! MyForm::label('username', $entity->tA('username'), [], false) !!}<br/>
                                {!! MyForm::label('username', $entity->username, ['placeholder'=>$entity->tA('username')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('email', $entity->tA('email'), [], false) !!}<br/>
                                {!! MyForm::text('email', $entity->email, ['placeholder'=>$entity->tA('email')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('full_name', $entity->tA('full_name'), [], false) !!}<br/>
                                {!! MyForm::text('full_name', $entity->full_name, ['placeholder'=>$entity->tA('full_name')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('password', $entity->tA('password') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                {!! MyForm::password('password',['placeholder'=>$entity->tA('password'), 'autocomplete'=>"new-password"]) !!}
                                {!! $entity->id ? '<p class="help-block">'.$entity->tA('password_note_text').'</p>' : '' !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('password_confirmation', $entity->tA('password_confirmation') . ($entity->id ? '' : ' <span class="text-danger">*</span>'), [], false ) !!}
                                {!! MyForm::password('password_confirmation',['placeholder'=>$entity->tA('password_confirmation')]) !!}
                            </div>

                            <div class="form-group">
                                {!! MyForm::label('role', $entity->tA('role')) !!}
                                <br/>
                                <ul>
                                    @foreach($entity->listRole as $role)
                                        <li>{{ $role }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <input type="hidden" name="backUrl" value="{!! request()->query('_o') !!}">

                            <div class="form-group">
                                {!! MyForm::label('vehicleTeams', $entity->tA('driver_team')) !!}
                                <br/>
                                <ul>
                                    @foreach($entity->listVehicleTeam as $vehicleTeam)
                                        <li>{{isset($vehicleTeamList[$vehicleTeam]) ? $vehicleTeamList[$vehicleTeam] : ''}}</li>
                                    @endforeach
                                </ul>
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
@endsection