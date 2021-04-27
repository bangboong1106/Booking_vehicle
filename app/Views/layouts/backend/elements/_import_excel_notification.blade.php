<?php
$success_total = array_filter($entities, function ($entity) {
    return $entity['importable'];
});
?>
<div class="row">
    <div class="col-md-4">
        <label>Số bản ghi hợp lệ: </label>
        <span class="badge badge-success" style="font-size: 16px">{!! count($success_total) !!}</span>
    </div>
    @if(count($entities)-count($success_total) > 0)
        <div class="col-md-4">
            <label>Số bản ghi không hợp lệ:</label>
            <span class="badge badge-danger" style="font-size: 16px">
                {!! count($entities)-count($success_total) !!}
            </span>
        </div>
        <div class="col-md-4 mb-2">
            <input id="filter_fail" class="switchery" type="checkbox"/>
            <label for="filter_fail">{{ trans('common.filter_fail') }}</label>
        </div>
    @endif
</div>