<div class="form-inline m-b-10 justify-content-between">
    <div class="row">
        <div class="col-md-12 text-xs-center">
            <h4 class="page-title">{{$title}}</h4>
        </div>
    </div>
    <div class="row">
        @include('backend.route.list_to_create')
        @include('layouts.backend.elements.column_config._wrap_column_config',[
            'entity' => 'route', 
            'sort_field' => $sort_field,
            'sort_type' => $sort_type,
            'page_size' => $page_size,
            'attributes' => $attributes, 
            'configList'=> isset($configList) ? $configList : []])
    </div>
    <input type="hidden" id="back_url_key" value="{{isset($backUrlKey) ? $backUrlKey : ''}}">
</div>