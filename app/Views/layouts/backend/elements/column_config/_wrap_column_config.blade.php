<div class="column-config" title="Chọn cột để ẩn/hiện">
    <input type="hidden" id="table_id" value='{{config('constant.cf_'.$entity)}}'>
    <div class="config-popup">
        <div class="row" style="padding: 8px">
            <div class="col-md-5">
                Cột sắp xếp mặc định
            </div>
           <div class="col-md-7">
                <select class="select2" id="sort_field">
                    <option value="id">Vui lòng chọn cột sắp xếp</option>
                    @foreach($attributes as $key=>$attribute)
                        <?php 
                            $field = array_key_exists('field', $attribute) ?  $attribute["field"] : $attribute["attribute"]; 
                        ?>
                        <option {{$sort_field == $field ? "selected" : ""}} value="{{$field}}">
                            {{trans('models.'.$entity.'.attributes.'.$attribute["attribute"])}}
                        </option>
                    @endforeach
                </select>
           </div>
        </div>
        <div class="row" style="padding: 8px">
            <div class="col-md-5">
                Thứ tự sắp xếp mặc định
            </div>
           <div class="col-md-7">
                <select class="select2" id="sort_type">
                    <option value="desc">Vui lòng chọn thứ tự sắp xếp</option>
                    <option {{$sort_type == "asc" ? "selected" : ""}} value="asc">Tăng dần (A-Z)</option>
                    <option {{$sort_type == "desc" ? "selected" : ""}} value="desc">Giảm dần (Z-A)</option>
                </select>
           </div>
        </div>
        <div class="row" style="padding: 8px">
            <div class="col-md-5">
                Số bản ghi mặc định
            </div>
           <div class="col-md-7">
                <select class="select2" id="config_page_size">
                    <option value="50">Vui lòng chọn số bản ghi</option>
                    <option {{$page_size == 10 ? "selected" : ""}} value=10>10</option>
                    <option {{$page_size == 20 ? "selected" : ""}} value=20>20</option>
                    <option {{$page_size == 50 ? "selected" : ""}} value=50>50</option>
                    <option {{$page_size == 100 ? "selected" : ""}} value=100>100</option>

                </select>
           </div>
        </div>
        <div style="padding: 8px; font-weight: bold">Danh sách cột tuỳ chỉnh</div>
        <div class="btn-config-group">
            @if(isset($configList) && count($configList) > 0)
                    <?php
                    $displayConfigList = collect($configList)->filter(function($value){
                        return $value['shown'] == true;
                    });
            
                    $hiddenConfigList = [];
            
                    collect($attributes)->each(function($value) use ($displayConfigList,&$hiddenConfigList){
                        $temp = collect($displayConfigList)->filter(function($displayConfigList) use ($value){
                            return $displayConfigList["name"] == $value["attribute"];
                        })->first();

                        if($temp == null){
                            $hiddenConfigList[] = [
                                'name' => $value["attribute"],
                                'width' => $value["default_width"],
                                'shown' => false
                            ];
                        }
                    });
                    $mergedList = $displayConfigList->merge($hiddenConfigList);
                ?>
                @foreach($mergedList as $key=>$config)
                    @include('layouts.backend.elements.column_config._column_config',[
                        'entity' => $entity,
                        'attribute' => $config["name"], 
                        'index'=> $key, 
                        'show'=> $config["shown"]])
                @endforeach
            @else
                @foreach($attributes as $key=>$attribute)
                    @include('layouts.backend.elements.column_config._column_config',[
                        'entity' => $entity,
                        'attribute' => $attribute["attribute"], 
                        'index'=> $key, 
                        'show'=> $attribute["show"]])
                @endforeach
            @endif
        </div>
        <div class="config-footer">
            <a href="#" class="btn" id="config-reset"><i class="fa fa-remove"></i> Về mặc định</a>
            <a href="#" class="btn btn-blue" id="config-submit"><i class="fa fa-save"></i> Lưu</a>
        </div>
    </div>
</div>