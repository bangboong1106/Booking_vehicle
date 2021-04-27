<?php
    use Illuminate\Support\Str;

    $value = '';
    $typeText = 'eq';
    if (isset($dataIndex)) {
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
<div class="input-group col-mb-3 filter-group">
    <div class="input-group-prepend">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{getTypeFilterText($typeText)}}
        </button>
        <div class="dropdown-menu filter-operation">
            <a class="dropdown-item" href="#">
                =: Bằng
            </a>
            <a class="dropdown-item" href="#">
                {!! '<:' !!} Nhỏ hơn
            </a>
            <a class="dropdown-item" href="#">
                <=: Nhỏ hơn hoặc bằng
            </a>
            <a class="dropdown-item" href="#">
                >: Lớn hơn
            </a>
            <a class="dropdown-item" href="#">
                >=: Lớn hơn hoặc bằng
            </a>
            @if(isset($class) && Str::contains($class, 'datepicker'))
                <a class="dropdown-item" href="#">
                    <>: Trong khoảng
                </a>
            @endif
        </div>
    </div>
    <?php if(isset($field)): ?>
    <input type="text" class="form-control filter-index {{isset($class) ? $class : ''}}" name="{{$field}}_eq"
           value="{{$value}}" autocomplete="off">
    <?php else: ?>
        {!! $input !!}
    <?php endif; ?>
</div>