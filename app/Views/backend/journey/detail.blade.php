<div class=content-iw>
    <div class="row">
        <div class="col-4">
            <b><span style="color: red">{{ $vehicle->reg_no }}</span></b>
        </div>
        <div class="col-8 text-right">
            <span
                class="status {!!  $vehicle->status == 4 ? 'fail' : ($vehicle->status == 2 ? 'drive' : '') !!}">{{ $vehicle->getStatus() }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-6"><b>{{ numberFormat($vehicle->weight) }} </b>(kg)</div>
        <div class="col-6 text-right"><b>{{ numberFormat($vehicle->volume) }} </b>(m3)
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            {{ $vehicle->length != null ? numberFormat($vehicle->length) : numberFormat(0) }}
            *
            {{ $vehicle->width != null ? numberFormat($vehicle->width) : numberFormat(0) }}
            *
            {{ $vehicle->height != null ? numberFormat($vehicle->height) : numberFormat(0) }}
        </div>
    </div>
    @if (!empty($vehicle->current_location))
        <div class="row">
            <div class="col-12">
                <i>{{ $vehicle->current_location }}</i>
            </div>
        </div>
    @endif
    @if (isset($vehicle->current))
        <?php
        $weight = $vehicle->current->capacity_weight_ratio == null ? 0 : $vehicle->current->capacity_weight_ratio;
        $volume = $vehicle->current->capacity_volume_ratio == null ? 0 : $vehicle->current->capacity_volume_ratio;
        ?>
        <div class="row">
            <div class="col-6">Tỉ lệ loading khối lượng</div>
            <div class="col-6 text-right"><b>{{ numberFormat($weight) }}%</b></div>
        </div>
        <div class="row">
            <div class="col-12">
                <progress max=100 value={{ $weight }}>{{ number_format($weight, 2, ',', '.') }}</progress>
            </div>
        </div>
        <div class="row">
            <div class="col-6">Tỉ lệ loading thể tích</div>
            <div class="col-6 text-right"><b>{{ numberFormat($volume) }}%</b></div>
        </div>
        <div class="row">
            <div class="col-12">
                <progress max=100 value={{ $volume }}>{{ number_format($volume, 2, ',', '.') }}</progress>
            </div>
        </div>
        <div class="row">
            <div class="col-6">Tổng số lượng ĐH</div>
            <div class="col-6 text-right"><b>{{ numberFormat($vehicle->current->count_order) }} </b>(m3)</div>
        </div>
    @endif
</div>
