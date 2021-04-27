<?php
    $value = '';
    $typeText = 'cons';
    if (isset($dataIndex) && isset($field)) {
        foreach ($dataIndex as $key => $data) {
            if (strpos($key, $field) === 0) {
                $value = $data;
                $type = explode('_', $key);
                $typeText = end($type);

                break;
            }
        }
    }
?>

<?php if (isset($element) && $element == 'dropDown'): ?>
    {{ MyForm::dropDown($field.'_eq', $value, isset($options) ? $options : [], true, ['class' => 'select2 filter-index']) }}
<?php else: ?>
    <div class="input-group col-mb-3 filter-group">
        <div class="input-group-prepend">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{getTypeFilterText($typeText)}}
            </button>
            <div class="dropdown-menu filter-operation">
                <a class="dropdown-item" href="#">*: Chứa</a>
                <a class="dropdown-item" href="#">=: Bằng</a>
                <a class="dropdown-item" href="#">+: Bắt đầu bằng</a>
                <a class="dropdown-item" href="#">-: Kết thúc bằng</a>
                <a class="dropdown-item" href="#">!: Không chứa</a>
            </div>
        </div>
        <?php if(isset($field)): ?>
        <input type="text" class="form-control filter-index {{isset($class) ? $class : ''}}" name="{{$field}}_cons"
               value="{{$value}}"/>
        <?php else: ?>
        {!! $input !!}
        <?php endif; ?>
    </div>
<?php endif; ?>