<tr class="active">
    @include('layouts.backend.elements.head_to_checkbox_all')
    @if ($is_action)
        <th class="text-center header-sticky">{{ trans('actions.action') }}</th>
    @endif
    @if (isset($configList) && count($configList) > 0)
        @foreach ($configList as $key => $config)
            @if ($config['shown'])
                <?php
                $attribute = collect($attributes)->first(function ($value, $key) use ($config) {
                return $value['attribute'] === $config['name'];
                });
                if (empty($attribute)) {
                $attribute = [
                'attribute' => $config['name'],
                'data_type' => 'string',
                'default_width' => 200,
                ];
                }
                $field = array_key_exists('field', $attribute) ? $attribute['field'] : $attribute['attribute'];
                ?>
                @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
                    <th style="width: {{ $config['width'] ? $config['width'] : $attribute['default_width'] }}px"
                        class="text-center  header-sticky" name="{{ $attribute['attribute'] }}">
                        {!! Sorting::aLink($field, trans('models.' . $entity . '.attributes.' .
                        $attribute['attribute'])) !!}
                    </th>
                @else
                    <th style="width: {{ $config['width'] ? $config['width'] : $attribute['default_width'] }}px"
                        name="{{ $attribute['attribute'] }}">
                        {!! Sorting::aLink($field, trans('models.' . $entity . '.attributes.' .
                        $attribute['attribute'])) !!}
                    </th>
                @endif
            @endif
        @endforeach
    @else
        @foreach ($attributes as $key => $attribute)
            <?php $field = array_key_exists('field', $attribute) ? $attribute['field'] :
            $attribute['attribute']; ?>
            @if ($attribute['show'] || (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky']))
                @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
                    <th style="width: {{ $attribute['default_width'] }}px" class="text-center  header-sticky"
                        name="{{ $attribute['attribute'] }}">
                        {!! Sorting::aLink($field, trans('models.' . $entity . '.attributes.' .
                        $attribute['attribute'])) !!}
                    </th>
                @else
                    <th style="width: {{ $attribute['default_width'] }}px" name="{{ $attribute['attribute'] }}">
                        {!! Sorting::aLink($field, trans('models.' . $entity . '.attributes.' .
                        $attribute['attribute'])) !!}
                    </th>
                @endif
            @endif
        @endforeach
    @endif
</tr>
<tr class="filter-row">
    <th class="text-center header-sticky"></th>
    @if ($is_action)
        <th></th>
    @endif
    @if (isset($configList) && count($configList) > 0)
        @foreach ($configList as $key => $config)
            @if ($config['shown'])
                <?php
                $attribute = collect($attributes)->first(function ($value, $key) use ($config) {
                return $value['attribute'] === $config['name'];
                });
                $field = (empty($attribute) ? $config['name'] : array_key_exists('field', $attribute)) ?
                $attribute['field'] : $attribute['attribute'];
                ?>
                @if (array_key_exists('disable_filter', empty($attribute) ? [] : $attribute) && $attribute['disable_filter'])
                    <th></th>
                @else
                    @switch($attribute["data_type"])
                        @case("string")
                        @case("email")
                        @case("phone")
                        @case("location")
                        @case("link")
                        @case("ins_user")
                        @case("upd_user")
                        @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
                            <th class="text-center header-sticky">
                                @include('layouts.backend.elements._filter_string', [
                                'field' => $field,
                                ])
                            </th>
                        @else
                            <th>@include('layouts.backend.elements._filter_string',[
                                'field' => $field,
                                ])</th>
                        @endif
                        @break
                        @case("list")
                        @case("bool")
                        <th>@include('layouts.backend.elements._filter_string', [
                            'field' => $field,
                            'element' => 'dropDown',
                            'options' => $attribute["list"]
                            ])</th>
                        @break
                        @case("number")
                        <th>@include('layouts.backend.elements._filter_number',[
                            'field' => $field,
                            ])</th>
                        @break
                        @case("date")
                        @case("time")
                        @case("datetime")
                        <th>@include('layouts.backend.elements._filter_number',[
                            'field' => $field,
                            'class' => 'datepicker'
                            ])</th>
                        @break
                        @case('concat_string')
                        @case("image")
                        @case("download")
                        <th></th>
                        @break
                    @endswitch
                @endif
            @endif
        @endforeach
    @else

        @foreach ($attributes as $key => $attribute)
            @if ($attribute['show'] || (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky']))
                <?php $field = array_key_exists('field', $attribute) ? $attribute['field'] :
                $attribute['attribute']; ?>

                @if (array_key_exists('disable_filter', $attribute) && $attribute['disable_filter'])
                    <th></th>
                @else
                    @switch($attribute["data_type"])
                        @case("string")
                        @case("email")
                        @case("phone")
                        @case("location")
                        @case("link")
                        @case("ins_user")
                        @case("upd_user")
                        @if (array_key_exists('is_sticky', $attribute) && $attribute['is_sticky'])
                            <th class="text-center header-sticky">
                                @include('layouts.backend.elements._filter_string', ['field' => $field])
                            </th>
                        @else
                            <th>@include('layouts.backend.elements._filter_string',[
                                'field' => $field,
                                ])</th>
                        @endif
                        @break
                        @case("list")
                        @case("bool")
                        <th>@include('layouts.backend.elements._filter_string', [
                            'field' => $field,
                            'element' => 'dropDown',
                            'options' => $attribute["list"]
                            ])</th>
                        @break
                        @case("number")
                        <th>@include('layouts.backend.elements._filter_number',[
                            'field' => $field
                            ])</th>
                        @break
                        @case("date")
                        @case("time")
                        @case("datetime")
                        <th>@include('layouts.backend.elements._filter_number',[
                            'field' => $field,
                            'class' => 'datepicker'
                            ])</th>
                        @break
                        @case('concat_string')
                        @case("image")
                        @case("download")
                        <th></th>
                        @break
                    @endswitch
                @endif
            @endif
        @endforeach
    @endif
</tr>
