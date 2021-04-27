<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['currency.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">

                        {!! MyForm::hidden('id', $entity->id) !!}
                        <div class="form-group">
                            {!! MyForm::label('currency_code', $entity->tA('currency_code'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('currency_code', $entity->currency_code, ['currency_code'=>$entity->tA('currency_code')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('currency_name', $entity->tA('currency_name'). ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('currency_name', $entity->currency_name, ['currency_name'=>$entity->tA('currency_name')]) !!}
                        </div>
                    </div>
                </div>
                @include('layouts.backend.elements._submit_form_button')
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
@push('scripts')
    <?php
    $jsFiles = [
        'autoload/object-select2'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush