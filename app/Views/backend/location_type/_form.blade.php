<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['location-type.valid', $entity->id]])!!}
        <div class="row">
            <div class="offset-md-3 col-md-6">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content-body">
                        <div class="form-group">
                            {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('code', $entity->id != null ? $entity->code : '' , ['placeholder'=>$entity->tA('code')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                        </div>
                        @include('layouts.backend.elements.combobox._cb_customer', [
                            'entity' => $entity,
                            'customers' => $customers
                        ])
                        <div class="form-group">
                            {!! MyForm::label('description', $entity->tA('note'), [], false) !!}
                            {!! MyForm::textarea('description', $entity->description, ['placeholder'=>$entity->tA('note'), 'rows' => 3]) !!}
                        </div>
                    </div>

                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
<script>
    var url = '{{route('ward.getDistrict')}}';
</script>