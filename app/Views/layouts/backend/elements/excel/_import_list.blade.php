@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped import-table">
        <thead>
            <tr>
                <th style="width: 130px"></th>
                <th style="width: 100px">Dòng số</th>
                @foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig)
                    @if ($excelColumnMappingConfig->data_type != 'nested')
                        <th style="width: 200px">{{ $excelColumnMappingConfig->column_name }}</th>
                    @endif
                @endforeach
                <?php
                $nested_field_group = $excelColumnMappingConfigs->filter(function ($value) {
                return $value->data_type == 'nested';
                });
                $count_nested_group = $nested_field_group->count();
                ?>
                @if ($count_nested_group > 0)
                    <?php
                    $excelColumnMappingConfig = $nested_field_group->first();
                    $items = $entities[0][$excelColumnMappingConfig->nested_field];
                    ?>
                    @foreach ($items as $item)
                        <?php $idx = 0; ?>
                        @foreach ($nested_field_group as $key => $value)
                            <th style="width: 200px; display: {!!  $idx == $count_nested_group - 1 ? '' : 'none' !!}"
                                data-name="{{ $item[$value->nested_name] }}">
                                {{ $item[$value->nested_name] . '|' . $value->column_name }}
                                @if ($idx == $count_nested_group - 1)
                                    <i class="fa fa-plus-circle" aria-hidden="true" onclick="expand(this)"
                                        data-name="{{ $item[$value->nested_name] }}"></i>
                                 @endif
                            </th>
                            <?php $idx = $idx + 1; ?>
                        @endforeach
                    @endforeach
                @endif
                {{ isset($header) ? $header : '' }}
            </tr>
        </thead>
        <tbody>
            <?php $nest_property = isset($nest_property) ? $nest_property : ''; ?>
            @foreach ($entities as $entity)

                <?php $is_set = false; ?>
                @if (empty($nest_property))
                    <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                        <td>{!! $entity['importable'] ? (empty($entity['warning']) ? '<p class="text-success">' .
                                trans('messages.valid') . '</p>' : '<p class="text-warning">' . implode('<br>',
                                $entity['warning']) . '</p>') : '<p class="text-danger">' . implode('<br>',
                                $entity['failures']) . '</p>' !!}</td>
                        <td>{{ $entity['row'] }}</td>
                        @foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig)
                            <?php $field = isset($entity[$excelColumnMappingConfig->field]) ?
                            $entity[$excelColumnMappingConfig->field] : ''; ?>
                            @switch($excelColumnMappingConfig->data_type)
                                @case("string")
                                <td>{{ $field }}</td>
                                @break
                                @case("number")
                                <td class="text-right">{{ numberFormat($field) }}</td>
                                @break
                                @case("date")
                                <td>{{ $field }}</td>
                                @break
                                @case("time")
                                <td>{{ $field }}</td>
                                @break
                                @case("list")
                                <td>{{ isset($entity['name_of_' . $excelColumnMappingConfig->field]) ? $entity['name_of_' . $excelColumnMappingConfig->field] : '' }}
                                </td>
                                @break
                                @case("nested")
                                <?php
                                if ($is_set == true) {
                                continue;
                                }

                                $nested_field = $excelColumnMappingConfig->nested_field;
                                $nested_field_group = $excelColumnMappingConfigs->filter(function ($value) use
                                ($nested_field) {
                                return $value->nested_field == $nested_field;
                                });

                                $is_set = true;
                                $count_nested_group = count($nested_field_group);
                                ?>
                                @foreach ($entity[$excelColumnMappingConfig->nested_field] as $nested_field_prop)
                                    <?php $idx = 0; ?>
                                    @foreach ($nested_field_group as $key => $value)
                                        <td style="display: {!!  $idx == $count_nested_group - 1 ? '' : 'none' !!}"
                                            data-name="{{ $nested_field_prop[$value->nested_name] }}">
                                                {{ isset($nested_field_prop[$value->field]) ? $nested_field_prop[$value->field] : '' }}
                                            </td>
                                            <?php $idx = $idx + 1; ?>
                                         @endforeach
                                    @endforeach
                                    @break
                                @endswitch

                            @endforeach
                            {{ isset($list) ? $list : '' }}
                    </tr>
                @else
                    @foreach ($entity[$nest_property] as $index => $item)
                        <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                            <td>{!! $entity['importable'] ? (empty($entity['warning']) ? '<p class="text-success">' .
                                    trans('messages.valid') . '</p>' : '<p class="text-warning">' . implode('<br>',
                                    $entity['warning']) . '</p>') : '<p class="text-danger">' . implode('<br>',
                                    $entity['failures']) . '</p>' !!}</td>
                            <td>{{ $entity['row'] }}</td>
                            @foreach ($excelColumnMappingConfigs as $excelColumnMappingConfig)
                                @if ($index == 0)
                                    @switch($excelColumnMappingConfig->data_type)
                                        @case("string")
                                        <td>{{ isset($entity[$excelColumnMappingConfig->field]) ? $entity[$excelColumnMappingConfig->field] : null }}
                                        </td>
                                        @break
                                        @case("number")
                                        <td>{{ numberFormat($entity[$excelColumnMappingConfig->field]) }}</td>
                                        @break
                                        @case("date")
                                        <td>{{ $entity[$excelColumnMappingConfig->field] }}</td>
                                        @break
                                        @case("time")
                                        <td>{{ $entity[$excelColumnMappingConfig->field] }}</td>
                                        @break
                                        @case("list")
                                        <td>{{ isset($entity['name_of_' . $excelColumnMappingConfig->field]) ? $entity['name_of_' . $excelColumnMappingConfig->field] : '' }}
                                        </td>
                                        @break
                                    @endswitch
                                @else
                                    @if ($excelColumnMappingConfig->is_multiple == 1)
                                        @switch($excelColumnMappingConfig->data_type)
                                            @case("string")
                                            <td>{{ $item[$excelColumnMappingConfig->field] }}</td>
                                            @break
                                            @case("number")
                                            <td>{{ numberFormat($item[$excelColumnMappingConfig->field]) }}</td>
                                            @break
                                            @case("date")
                                            <td>{{ $item[$excelColumnMappingConfig->field] }}</td>
                                            @break
                                            @case("time")
                                            <td>{{ $item[$excelColumnMappingConfig->field] }}</td>
                                            @break
                                            @case("list")
                                            <td>{{ isset($item['name_of_' . $excelColumnMappingConfig->field]) ? $item['name_of_' . $excelColumnMappingConfig->field] : '' }}
                                            </td>
                                            @break
                                            @default
                                            <td>{{ $item[$excelColumnMappingConfig->field] }}</td>
                                            @break
                                        @endswitch
                                    @else
                                        <td></td>
                                    @endif
                                @endif
                            @endforeach
                            @if (isset($extend_list))
                                @foreach ($extend_list as $extend_item)
                                    <td>{{ isset($entity[$nested_extend_property][$extend_item]) ? $entity[$nested_extend_property][$extend_item] : '' }}
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                @endif

            @endforeach
        </tbody>
    </table>
</div>
<script>
    function expand(event) {
        var $this = $(event);
        var name = $this.data('name');
        if ($this.hasClass('fa-plus-circle')) {
            $('th[data-name="' + name + '"').show();
            $('td[data-name="' + name + '"').show();
            $this.removeClass('fa-plus-circle');
            $this.addClass('fa-minus-circle')
        } else {
            $('th[data-name=' + name).hide();
            $('td[data-name="' + name + '"').hide();
            $this.addClass('fa-plus-circle');
            $this.removeClass('fa-minus-circle')
        }
    }

</script>
