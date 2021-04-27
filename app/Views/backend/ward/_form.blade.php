<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['ward.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card-box">
                    @include('layouts.backend.elements._form_label')

                    <div class="form-group m-t-30">
                        {!! MyForm::label('ward_id', $entity->tA('ward_id') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('ward_id', $entity->ward_id, ['placeholder'=>$entity->tA('ward_id')]) !!}
                    </div>
                    <div class="form-group m-t-30">
                        {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('type', $entity->tA('type')) !!}
                        {!! MyForm::dropDown('type', $entity->type, config('system.ward'), false) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('province_id', $entity->tA('province')) !!}
                        {!! MyForm::dropDown('province_id', $entity->tryGet('district')->tryGet('province')->province_id, $provinceList, false)  !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('district_id', $entity->tA('district')) !!}
                        {!! MyForm::dropDown('district_id', $entity->district_id, $districtList, false, ['data-origin' => $entity->district_id]) !!}
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