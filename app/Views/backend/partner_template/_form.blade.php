<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['partner-template.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">

                        <div class="form-group m-t-30">
                            {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('type', $entity->tA('type')) !!}
                            {!! MyForm::dropDown('type', $entity->type, config('system.template_type'), false, ['class' => 'select2']) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('export_type', $entity->tA('export_type')) !!}
                            {!! MyForm::dropDown('export_type', $entity->export_type, config('system.template_export_type'), false, ['class' => 'select2']) !!}
                        </div>
                        <div class="form-group" id="wrap-is-print-empty-cost" style="{!! $entity->type == 3 || $entity->type == 7 ? "display: block" : "display: none" !!}">
                                {!! MyForm::label('is_print_empty_cost', $entity->tA('is_print_empty_cost'), [], false) !!}
                                <input hidden="hidden" name="is_print_empty_cost" id="form_is_print_empty_cost"
                                    value="{{ $entity->is_print_empty_cost }}"/>
                                <div>
                                    {!! MyForm::checkbox('switchery_is_print_empty_cost', $entity->is_print_empty_cost, $entity->is_print_empty_cost  == "1" ? true : false
                                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_print_empty_cost']) !!}
                                    <span></span>
                                </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('list_item', $entity->tA('list_item'), [], false) !!}
                            <div>
                                {!! MyForm::hidden('list_item', $entity->list_item, ['id' => 'list_item']) !!}
                                <div class="select-goods" style="display: {{ $entity->type == 1 || $entity->type == null ? 'block' :'none'}}">
                                    <select class="select2 " id="select-goods" name="select-goods" multiple>
                                        @if(isset($selectedList) &&  $entity->type == 1)
                                            @foreach ($goodsList as $goods)
                                                <?php 
                                                    $selected = empty($selectedList->filter(function($value) use ($goods){
                                                        return $value->id == $goods->id;
                                                    })->first()) ? '' : 'selected';

                                                ?>
                                                <option value="{{ $goods->id }}" {!! $selected !!}>
                                                    {{ $goods->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach ($goodsList as $goods)
                                                <option value="{{ $goods->id }}">
                                                    {{ $goods->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="select-cost" style="display:  {{ $entity->type == 3 || $entity->type == 7  ? 'block' :'none'}}">
                                    <select class="select2" id="select-cost" name="select-cost" multiple>
                                        @if(isset($selectedList) && ( $entity->type == 3 || $entity->type == 7))
                                            @foreach ($costList as $cost)
                                                <?php 
                                                    $selected = empty($selectedList->filter(function($value) use ($cost){
                                                        return $value->id == $cost->id;
                                                    })->first()) ? '' : 'selected';
                                                ?>
                                                <option value="{{ $cost->id }}" {!! $selected !!}>
                                                    {{ $cost->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach ($costList as $cost)
                                                <option value="{{ $cost->id }}">
                                                    {{ $cost->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="wrap-is-print-empty-goods"
                             style="{!! $entity->type == 1 ? "display: block" : "display: none" !!}">
                            {!! MyForm::label('is_print_empty_goods', $entity->tA('is_print_empty_goods'), [], false) !!}
                            <input hidden="hidden" name="is_print_empty_goods" id="form_is_print_empty_goods"
                                   value="{{ $entity->is_print_empty_goods }}"/>
                            <div>
                                {!! MyForm::checkbox('switchery_is_print_empty_goods', $entity->is_print_empty_goods, $entity->is_print_empty_goods  == "1" ? true : false
                                , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_print_empty_goods']) !!}
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('type', $entity->tA('file') . ' <span class="text-danger">*</span>', [], false) !!}
                            <div class="dropzone-outer previewsContainer"></div>
                            <div class="dropzone text-center" data-file_type="3"></div>
                            {!! MyForm::hidden('file_id', $entity->file_id, ['id' => 'file_id']) !!}
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('description', $entity->tA('description')) !!}
                            {!! MyForm::textarea('description', $entity->title, ['placeholder'=>$entity->tA('description')]) !!}

                        </div>
                        {!! MyForm::hidden('partner_id', \Auth::user()->partner_id  ,['id'=> 'partner_id'])!!}
                    </div>
                </div>
                @include('layouts.backend.elements._submit_form_button')
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