<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['goods-type.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content-body">
                        <div class="form-group">
                            {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
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
                            {!! MyForm::label('title', $entity->tA('goods_group_id'), [], false) !!}
                            <div class="input-group" >
                                <div class="input-group-btn" style="width: 100%">
                                    <select class="form-group-right minimal form-control " name="goods_group_id">
                                        @foreach($goodsGroups as $goodsGroup)
                                            @if ($entity->goods_group_id == $goodsGroup->id)
                                                <option value={{ $goodsGroup->id }} selected>{{ $goodsGroup->name}}</option>
                                            @else
                                                <option value={{ $goodsGroup->id }} >{{ $goodsGroup->name}}</option>
                                            @endif    
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @include('layouts.backend.elements.combobox._cb_goods_unit', [
                              'entity' => $entity,
                                'goodsUnit' => $goodsUnits,
                            ])
                        <div class="form-group">
                            {!! MyForm::label('volume', trans('models.goods_type.attributes.volume'), [], false) !!}
                            <div class="input-group">
                                {!! MyForm::text('volume', numberFormat($entity->volume),
                                ['placeholder'=>$entity->tA('volume'),'class' => 'number-input']) !!}
                                <div class="input-group-prepend">
                                <span class="input-group-text form-group-right">
                                   m³
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('weight', trans('models.goods_type.attributes.weight'), [], false) !!}
                            <div class="input-group">
                                {!! MyForm::text('weight', numberFormat($entity->weight), ['placeholder'=>$entity->tA('weight'),'class' => 'number-input'] ) !!}
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-group-right">kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('weight', trans('models.goods_type.attributes.in_amount'), [], false) !!}
                            <div class="input-group">
                                {!! MyForm::text('in_amount', numberFormat($entity->in_amount), ['placeholder'=>$entity->tA('weight'),'class' => 'number-input'] ) !!}
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-group-right">₫</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('weight', trans('models.goods_type.attributes.out_amount'), [], false) !!}
                            <div class="input-group">
                                {!! MyForm::text('out_amount', numberFormat($entity->out_amount), ['placeholder'=>$entity->tA('weight'),'class' => 'number-input'] ) !!}
                                <div class="input-group-prepend">
                                    <span class="input-group-text form-group-right">₫</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('note', $entity->tA('note'), [], false) !!}
                            {!! MyForm::textarea('note', $entity->note, ['placeholder'=>$entity->tA('note'), 'rows' => 3]) !!}
                        </div>

                        <div class="form-group">
                            {!! MyForm::label('type', $entity->tA('file')) !!}
                            <div class="dropzone-outer previewsContainer"></div>
                            <div class="dropzone text-center" data-file_type="3"></div>
                            {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
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
    
    var token = '{!! csrf_token() !!}',
        uploadUrl = '{{ route('file.uploadFile') }}',
        downloadUrl = '{{ route('file.downloadFile',-1) }}',
        removeUrl = '{{ route('file.destroy', -1) }}',
        existingFiles = [],
        comboCustomerUri = '{{route('location-type.combo-customer')}}';

    @if(!empty($entity->file_id))
    existingFiles.push({
        name: '{{ $entity->tryGet('getFile')->file_name }}',
        size: '{{ $entity->tryGet('getFile')->size }}',
        url: '{{ route('file.getImage', $entity->tryGet('getFile')->file_id) }}',
        type: '{{ $entity->tryGet('getFile')->file_type }}',
        urlDownload: '{{ route('file.downloadFile',$entity->tryGet('getFile')->file_id) }}',
        id: '{{ $entity->tryGet('getFile')->file_id }}',
    });
    @endif
</script>