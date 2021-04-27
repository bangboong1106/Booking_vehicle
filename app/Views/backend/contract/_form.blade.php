<div class="row">
    <div class="col-md-12">
        {!! MyForm::model($entity, [
            'route' => [empty($formAdvance) ? 'contract.valid' : 'contract.advance', $entity->id],
            'validation' => empty($validation) ? null : $validation
        ])!!}
        <div class="card-box form-display">
            @include('layouts.backend.elements._form_label')
            <div class="row content-body">

                <div class="col-md-12">

                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('contract_no', $entity->tA('contract_no'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('contract_no', $entity->contract_no, ['placeholder'=>$entity->tA('contract_no')]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::label('customer_id', $entity->tA('customer_id'), [], false) !!}
                            {!! MyForm::dropDown('customer_id', $entity->customer_id, $customers, true, ['class' => 'select2']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('issue_date', $entity->tA('issue_date'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('issue_date', $entity->issue_date, ['placeholder'=>$entity->tA('issue_date'), 'class' => 'datepicker']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::label('expired_date', $entity->tA('expired_date'), [], false) !!}
                            {!! MyForm::text('expired_date', $entity->expired_date, ['placeholder'=>$entity->tA('expired_date'), 'class' => 'datepicker']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('type', $entity->tA('type'), [], false) !!}
                            {!! MyForm::dropDown('type', $entity->type, $contractTypes, true, ['class' => 'select2']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::label('status', $entity->tA('status')) !!}
                            {!! MyForm::dropDown('status', $entity->status, config('system.contract_status'), false, ['class' => 'select2']) !!}
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="dropzone-outer previewsContainer"></div>
                            <div class="dropzone" id="contract"></div>
                            {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                        {!! MyForm::textarea('note', $entity->note,['rows'=>3]) !!}
                    </div>

                </div>
            </div>
            <div class="contract row card-box submit">
                @include('layouts.backend.elements._submit_form_button')
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>

<script>
    var currentLat = '{{ empty($entity->latitude) ? 0 : $entity->latitude }}',
        currentLng = '{{ empty($entity->longitude) ? 0 : $entity->longitude }}';
</script>
<script>
    var token = '{!! csrf_token() !!}',
        url = '{{ route('file.uploadFile') }}',
        urlDownload = '{{ route('file.downloadFile',999) }}',
        existingFiles = [],
        removeUrl = '{{ route('file.destroy', 999) }}';

    @foreach($file_list as $file)
    existingFiles.push({
        name: '{{ $file->file_name }}',
        size: '{{ $file->size }}',
        type: '{{ $file->file_type }}',
        url: '{{ route('file.getImage', $file->file_id) }}',
        urlDownload: '{{ route('file.downloadFile', $file->file_id) }}',
        full_url: '{{ route('file.getImage', ['id' => $file->file_id, 'full' => true]) }}',
        id: '{{ $file->file_id }}'
    });
    @endforeach
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