<div class="form-group row">
    <?php
    $orderList = '';
    foreach ($orders as $order) {
        $orderList .= '<a class="order-detail" href="#"
        data-show-url="' . (isset($showAdvance) ? route('order.show', $order->order_id) : '') . '"
        data-id="' . $order->order_id . '"><span class="tag-order">' . $order->order_code . '</span></a>';
    }
    ?>
    @include('layouts.backend.elements.detail_to_edit',[
        'property' => 'order',
        'isEditable' => false,
        'controlType' => 'label',
        'widthWrap'=>'col-md-8',
        'value'=> $orderList
        ])
</div>

<div class="col-md-12">
    <label for="order_vehicle_id">{{ $entity->tA('vehicle')}}</label>
    <div class="input-group select2-bootstrap-prepend">
        <select class="select2 select-vehicle" id="order_vehicle_id"
                name="order_vehicle_id" multiple>
            <option></option>
            @if(!empty($vehicles))
                @foreach($vehicles as $vehicle)
                    <option value="{{$vehicle->id}}" selected="selected"
                            title="{{$vehicle->reg_no}}">{{$vehicle->reg_no}}</option>
                @endforeach
            @endif
        </select>
        <span class="input-group-addon vehicle-search" data-distance="1">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-truck"></i>
                        </div>
                    </span>
    </div>
</div>

<div class="input-group select2-bootstrap-prepend">
    <select class="select2 select-route"
            id="route_id" name="route_id" {{ empty($entity->id) ? 'disabled' : ''  }}>
        <option></option>
    </select>
    <span class="input-group-addon route-search" id="route-search">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-barcode"></i>
                        </div>
                    </span>
</div>

<div class="advanced form-group row">
    <div class="col-md-6">
        <label for="route_vehicle_id">{{ $entity->tA('vehicle')}}</label>
        <div class="input-group select2-bootstrap-prepend">
            <select class="select2 select-vehicle" id="route_vehicle_id"
                    name="route_vehicle_id">
                <option></option>
            </select>
            <span class="input-group-addon vehicle-search" data-distance="1">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-truck"></i>
                        </div>
                    </span>
        </div>
    </div>
    <div class="col-md-6">
        <label for="driver">{{ $entity->tA('primary_driver')}}</label>
        <div class="input-group select2-bootstrap-prepend">
            <select class="select2 select-driver" id="primary_driver_id"
                    name="primary_driver_id">
                <option></option>
            </select>
            <span class="input-group-addon driver-search">
                        <div class="input-group-text bg-transparent">
                            <i class="fa fa-id-card"></i>
                        </div>
                    </span>
        </div>
    </div>
</div>