<div class="container">
    @foreach ($items as $index => $item)
        <div class="row item">
            <div class="col-1">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radios" id="{{ $item->id }}"
                        value="{{ $item->id }}" {!! $index==0 ? 'checked' : '' !!}>
                </div>
            </div>
            <div class="col-11 info" {!! $index==0 ? '' : 'disabledbutton' !!}>
                <div class="row">
                    <div class="col-12"><label>Danh sách điểm nhận hàng</label></div>
                    <div class="col-12 default-info">
                        <?php $locationDestinations = $item->locationDestinationAttributes(); ?>
                        @if (count($locationDestinations) == 1)
                            <input type="hidden" id="hdf_location_destination_{{ $item->id }}"
                                value="{{ $locationDestinations->first()->id }}">
                            <span class="location-destination-info" href="#" data-item-id={{ $item->id }}
                                data-id={{ $locationDestinations->first()->id }}>
                                {{ $locationDestinations->first()->title }}</span>
                        @else
                            <input type="hidden" id="hdf_location_destination_{{ $item->id }}">
                            @foreach ($locationDestinations as $data)
                                <span class="location-destination-info" href="#"
                                    onclick="clickLocationDestinationInfo(event)" data-item-id={{ $item->id }}
                                    data-id={{ $data->id }}>{{ $data->title }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12"><label>Danh sách điểm trả hàng</label></div>
                    <div class="col-12 default-info">
                        <?php $locationArrivals = $item->locationArrivalAttributes(); ?>
                        @if (count($locationArrivals) == 1)
                            <input type="hidden" id="hdf_location_arrival_{{ $item->id }}"
                                value="{{ $locationArrivals->first()->id }}">
                            <span class="location-arrival-info" href="#" data-item-id={{ $item->id }}
                                data-id={{ $locationArrivals->first()->id }}>
                                {{ $locationArrivals->first()->title }}</span>
                        @else
                            <input type="hidden" id="hdf_location_arrival_{{ $item->id }}">
                            @foreach ($item->locationArrivalAttributes() as $data)
                                <span class="location-arrival-info" onclick="clickLocationArrivalInfo(event)"
                                    data-item-id={{ $item->id }} data-id={{ $data->id }}>{{ $data->title }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12"><label>Tiền tố sinh mã đơn hàng</label></div><br />
                    <div class="col-12 default-info">
                        <input type="hidden" id="hdf_system_code_config_{{ $item->id }}"
                            value="{{ isset($item->systemCodeConfig) ? $item->systemCodeConfig->id : '' }}">
                        <span class="system-code-config" data-item-id={{ $item->id }}
                            data-id={{ isset($item->systemCodeConfig) ? $item->systemCodeConfig->id : '' }}>
                            {{ isset($item->systemCodeConfig) ? $item->systemCodeConfig->prefix : '' }}</span>

                    </div>
                </div>
            </div>

        </div>

    @endforeach
</div>
<style>
    .default-info {
        margin: 8px 0;
    }

    .location-destination-info,
    .location-arrival-info {
        border: 1px solid #12509b;
        border-radius: 4px;
    }

    .location-destination-info.active,
    .location-arrival-info.active {
        background: #1f5e7d;
        color: white;
    }

    .item {
        border-radius: 4px;
        margin: 8px 0px;
        border: 1px solid #dedede;
    }

    .disabledbutton {
        pointer-events: none;
        opacity: 0.4;
    }

</style>
<script>
    function clickLocationDestinationInfo(e) {
        e.preventDefault();
        $('.location-destination-info').removeClass('active');
        $this = $(e.currentTarget);
        $this.addClass('active');
        var id = $this.data('item-id');
        $('#hdf_location_destination_' + id).val($this.data('id'));
    }

    function clickLocationArrivalInfo(e) {
        e.preventDefault();
        $('.location-arrival-info').removeClass('active');
        $this = $(e.currentTarget);
        $this.addClass('active');
        var id = $this.data('item-id');
        $('#hdf_location_arrival_' + id).val($this.data('id'));
    }

    $('input[name=radios]').change(function(e) {
        $(".row.item").find('.info').addClass("disabledbutton");
        $(e.currentTarget).parents('.row.item').find('.info').removeClass("disabledbutton");
    });

</script>
