
@if(!$entities->total() || $entities->total() ==0)
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        @if(!isset($is_add) || (isset($is_add) && $is_add))
            @can('add ' . str_replace('-', '_', $routePrefix))
                <div class="wrap-btn">
                    <div class="btn" id="btn-add-empty">
                        <a href="{{backUrl($routePrefix.'.create') }}">
                        <i class="fa fa-plus"><span>Thêm {{trans('models.'.$entity.'.name')}}</span></i>
                        </a>
                    </div>
                </div>
            @endcan
        @endif
    </div>
@else
    <?php
        $sticky = collect($attributes)->first(function($value){ return $value["is_sticky"] == true; });
        $configAction = [];
        if (!(isset($is_show_history) && !$is_show_history)) {
            $configAction['history'] = $sticky["attribute"];
        }

        if (isset($is_show_split_order) && $is_show_split_order) {
            $configAction['split_order'] = true;
        }
     ?>
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}
            {{isset($selectedItem) && in_array($entity->id, $selectedItem) ? 'row-selected' : '' }}"
            data-id="{{$entity->id}}"
            {!!isset($dbclick) && !$dbclick ? "data-dbclick='off'" : "" !!}
            >
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            {{--  @if($is_action || (isset($entity->is_action) && $entity->is_action == 1))  --}}
                {{-- @if(isset($is_show_history) && !$is_show_history)
                    @include('layouts.backend.elements.list_to_action')
                @else
                    @include('layouts.backend.elements.list_to_action', ['history' => $sticky["attribute"]])
                @endif --}}
                @include('layouts.backend.elements.list_to_action', $configAction)
            {{--  @elseif(isset($entity->is_action))
                <td></td>
            @endif  --}}
            @if(isset($configList)  && count($configList) > 0)
                @foreach($configList as $key=>$config)
                    @if($config['shown'])
                        <?php
                            $attribute = collect($attributes)->first(function ($value, $key) use ($config) {
                                return $value["attribute"] === $config["name"];
                            });
                            if(empty($attribute)){
                                    $attribute = [
                                        'attribute' => $config["name"],
                                        'data_type' => 'string',
                                        'default_width' => 200
                                    ];
                                }
                        ?>
                        @include('layouts.backend.elements.column_config._list_item',[
                            'entity'=> $entity,
                            'attribute' => $attribute])
                    @endif
                @endforeach
            @else
                @foreach($attributes as $key=>$attribute)
                    @if($attribute['show'] || (array_key_exists('is_sticky', $attribute) && $attribute["is_sticky"]))
                        @include('layouts.backend.elements.column_config._list_item',[
                            'entity'=> $entity,
                            'attribute' => $attribute])
                    @endif
                @endforeach
            @endif
        </tr>
    @endforeach
@endif