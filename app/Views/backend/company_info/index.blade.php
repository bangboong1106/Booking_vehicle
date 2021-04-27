@extends('layouts.backend.layouts.main')
@section('content')
    <ul class="list-group">
        <li class="list-group-item detail-info">
            <div class="row form-info-wrap">
                <div class="col-md-12">
                    <div class="card-header" role="tab" id="headingInformation">
                        <h6 class="mb-0 mt-0 font-18">
                            THÔNG TIN CÔNG TY
                        </h6>
                    </div>
                    <div id="collapseRoute" class="collapse show" role="tabpanel" aria-labelledby="headingOne" style="">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">Tên công ty
                                        </div>
                                        <div class="col-md-8 edit-group-control">
                                            {!! MyForm::text('company.name', $companyName, ['class' => 'setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i
                                                    class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i
                                                    class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">Địa chỉ công ty
                                        </div>
                                        <div class="col-md-8 edit-group-control">
                                            {!! MyForm::text('company.address', $companyAddress, ['class' => 'setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i
                                                    class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i
                                                    class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">Số điện thoại
                                        </div>
                                        <div class="col-md-8 edit-group-control">
                                            {!! MyForm::text('company.mobile_no', $companyMobileNo, ['class' => 'setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i
                                                    class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i
                                                    class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">Email
                                        </div>
                                        <div class="col-md-8 edit-group-control">
                                            {!! MyForm::text('company.email', $companyEmail, ['class' => 'setting-view-control disabled']) !!}
                                            <span class="edit setting-edit-control"><i class="fa fa-pencil"></i></span>
                                            <span class="accept setting-edit-control hidden"><i
                                                    class="fa fa-check"></i></span>
                                            <span class="cancel setting-edit-control hidden"><i
                                                    class="fa fa-close"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <span>Ảnh con dấu công ty (kích cỡ 200*200)</span>
                                        <div class="avatar-wrapper">
                                            <img class="profile-pic" src="{{empty($companyStampPath) ? '' : $companyStampPath}}" />
                                            <div class="upload-button">
                                                <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                            </div>
                                            <input class="file-upload" type="file" accept="image/*"/>
                                        </div>
                                        

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <script>
        let url = '{{ route('system-config.updateSystemConfig') }}',
            uploadUrl = '{{ route('company-info.stamp') }}',
            token = '{!! csrf_token() !!}';

    </script>
@endsection
@push('scripts')
    <?php $jsFiles = [
        'autoload/system-config',
        'autoload/company-info'
        ]; ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush
