@php
$today = (new DateTime());
@endphp
<div class="row">
    <div class="col-12">
        <input type="hidden" id="id" value="{{ $entity->id }}">
        {!! MyForm::model($entity, ['route' => ['repair-ticket.valid', $entity->id]]) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <ul class="nav nav-tabs tabs-bordered">
                        <li class="nav-item">
                            <a href="#route_info" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                {{ trans('models.order.attributes.communication') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active">
                            <div class="content-body">

                                {{--Thông tin chung--}}
                                <div class="card-header" role="tab" id="headingInformation">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                            aria-controls="collapseInformation" class="collapse-expand">
                                            {{ trans('models.order.attributes.information') }}
                                            <i class="fa"></i>
                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseInformation" class="collapse show" role="tabpanel"
                                    aria-labelledby="headingOne" style="">
                                    <div class="card-body">

                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                {!! MyForm::label('code', $entity->tA('code') . ' <span
                                                    class="text-danger">*</span>', [], false) !!}
                                                {!! MyForm::text('code', $entity->code != null ? $entity->code : $code, ['placeholder' =>
                                                $entity->tA('code')]) !!}
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('name', $entity->tA('name'), [], false) !!}
                                                {!! MyForm::text('name', $entity->name, ['placeholder' =>
                                                $entity->tA('name')]) !!}
                                            </div>
                                        </div>
                                        <div class="advanced form-group row">
                                            <div class="col-md-4">
                                                <label for="vehicle_id">{{ $entity->tA('vehicle_id') }}</label>
                                                <div class="input-group select2-bootstrap-prepend">
                                                    <input type="hidden" name="name_of_vehicle_id" id="name_of_vehicle_id"
                                                        value="{{ !empty($entity->vehicle) ? $entity->vehicle->reg_no : '' }}">
                                                    <select class="select2 select-vehicle" id="vehicle_id"
                                                        name="vehicle_id">
                                                        @if (!empty($entity->vehicle))
                                                            <option value="{{ $entity->vehicle->id }}" selected="selected"
                                                                title="{{ $entity->vehicle->reg_no }}">{{ $entity->vehicle->reg_no }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                    <span class="input-group-addon vehicle-search" id="vehicle-search"
                                                        data-distance="1">
                                                        <div class="input-group-text bg-transparent">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="driver">{{ $entity->tA('driver_id') }}</label>
                                                <div class="input-group select2-bootstrap-prepend">
                                                    <input type="hidden" name="name_of_driver_id" id="name_of_driver_id"
                                                    value="{{ !empty($entity->driver) ? $entity->driver->full_name : '' }}">
                                                    <select class="select2 select-driver" id="driver_id"
                                                        name="driver_id">
                                                        <option></option>
                                                        @if (!empty($entity->driver))
                                                            <option value="{{ $entity->driver->id }}" selected="selected"
                                                                title="{{ $entity->driver->full_name }}">
                                                                {{ $entity->driver->full_name }}</option>
                                                        @endif
                                                    </select>
                                                    <span class="input-group-addon driver-search"
                                                        id="primary-driver-search">
                                                        <div class="input-group-text bg-transparent">
                                                            <i class="fa fa-id-card"></i>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        {!! MyForm::label('repair_date', $entity->tA('repair_date'), [],
                                                        false) !!}
                                                        {!! MyForm::text('repair_date', empty($entity->id) &&
                                                        empty($entity->repair_date) ? $today->format('d-m-Y') :
                                                        $entity->repair_date, ['placeholder' =>
                                                        $entity->tA('repair_date'), 'class' => 'datepicker date-input'])
                                                        !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                {!! MyForm::label('amount', $entity->tA('amount'), [], false) !!}
                                                <div class="input-group">
                                                    {!! MyForm::text('amount', numberFormat($entity->amount),
                                                    ['placeholder' => $entity->tA('amount'), 'class' => 'number-input',
                                                    'id' => 'amount']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-8">
                                                {!! MyForm::label('description', $entity->tA('description')) !!}
                                                {!! MyForm::textarea('description', $entity->description, ['rows' => 4,
                                                'placeholder' => $entity->tA('description')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-header" role="tab" id="headingMapping">
                                    <h5 class="mb-0 mt-0 font-16">
                                        <a data-toggle="collapse" href="#collapseMapping" aria-expanded="true"
                                            aria-controls="collapseMapping" class="collapse-expand">
                                            Thông tin sửa chữa <i class="fa"></i>

                                        </a>
                                    </h5>
                                </div>
                                <div id="collapseMapping" class="collapse show" role="tabpanel"
                                    aria-labelledby="headingMapping" style="">
                                    <div class="card-body">

                                        <table id="table-amount" class="table table-bordered table-hover table-mapping">
                                            <thead id="head_content">
                                                <tr class="active">
                                                    <th style="width: 250px">
                                                        {{ $entity->tA('item.accessory_id') }}
                                                    </th>
                                                    <th style="width: 180px">
                                                        {{ $entity->tA('item.quantity') }}
                                                    </th>
                                                    <th style="width: 180px">
                                                        {{ $entity->tA('item.price') }}
                                                    </th>
                                                    <th style="width: 180px">
                                                        {{ $entity->tA('item.amount') }}
                                                    </th>
                                                    <th style="width: 220px">
                                                        {{ $entity->tA('item.next_repair_date') }}
                                                    </th>
                                                    <th style="width: 220px">
                                                        {{ $entity->tA('item.next_repair_distance') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_content">

                                                @if (isset($accessories))
                                                    <?php
                                                        $selected_items = $accessories->filter(function($value) use ($entity){
                                                            return !empty($entity->items->filter(function($temp) use ($value){
                                                                return $value->id == $temp->accessory_id;
                                                            })->first());
                                                        });
                                                        $unselected_items = $accessories->filter(function($value) use ($entity){
                                                            return empty($entity->items->filter(function($temp) use ($value){
                                                                return $value->id == $temp->accessory_id;
                                                            })->first());
                                                        });
                                                        $mergedList = $selected_items->merge($unselected_items);
                                                    ?>
                                                    @foreach ($mergedList as $index => $accessory)
                                                        <?php
                                                            $item = $entity->items->filter(function($value) use ($accessory){
                                                                return $value->accessory_id == $accessory->id;
                                                            })->first();
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input class="form-control input-uppercase mapping"
                                                                        type="hidden"
                                                                        name="items[{{ $index }}][id]"
                                                                        id="items[{{ $index }}][id]"
                                                                        value="{{!empty($item) ? ($item->id) : null}}}}" />
                                                                    <input class="form-control input-uppercase mapping"
                                                                        type="hidden"
                                                                        name="items[{{ $index }}][accessory_id]"
                                                                        id="items[{{ $index }}][accessory_id]"
                                                                        value="{{$accessory->id}}" />
                                                                        <input class="form-control input-uppercase mapping"
                                                                        type="hidden"
                                                                        name="items[{{ $index }}][name_of_accessory_id]"
                                                                        id="items[{{ $index }}][name_of_accessory_id]"
                                                                        value="{{$accessory->name}}" />
                                                                        <span>{{ $accessory->name }}</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input
                                                                        data-field="quantity"
                                                                        placeholder="{{ $entity->tA('item.quantity') }}"
                                                                        class="form-control number-input input-uppercase mapping"
                                                                        type="text" name="items[{{ $index }}][quantity]"
                                                                        id="items[{{ $index }}][quantity]"
                                                                        value = "{{ !empty($item) ? numberFormat($item->quantity) : null}}" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input 
                                                                        data-field="price"
                                                                        placeholder="{{ $entity->tA('item.price') }}"
                                                                        class="form-control number-input input-uppercase mapping"
                                                                        type="text" name="items[{{ $index }}][price]"
                                                                        id="items[{{ $index }}][price]"
                                                                        value = "{{ !empty($item) ? numberFormat($item->price) : null}}" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input
                                                                        data-field="amount"
                                                                        placeholder="{{ $entity->tA('item.amount') }}"
                                                                        class="form-control number-input input-uppercase mapping"
                                                                        type="text" name="items[{{ $index }}][amount]"
                                                                        id="items[{{ $index }}][amount]"
                                                                        value = "{{ !empty($item) ? numberFormat($item->amount) : null}}" />
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <div class="input-group">
                                                                    <input
                                                                        placeholder="{{ $entity->tA('item.next_repair_date') }}"
                                                                        class="form-control datepicker input-uppercase mapping"
                                                                        type="text"
                                                                        name="items[{{ $index }}][next_repair_date]"
                                                                        id="items[{{ $index }}][next_repair_date]"
                                                                        value = "{{ !empty($item) ? \Carbon\Carbon::parse($item->next_repair_date)->format('d-m-Y') : null}}" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input
                                                                        placeholder="{{ $entity->tA('item.next_repair_distance') }}"
                                                                        class="form-control number-input input-uppercase mapping"
                                                                        type="text"
                                                                        name="items[{{ $index }}][next_repair_distance]"
                                                                        id="items[{{ $index }}][next_repair_distance]"
                                                                        value = "{{ !empty($item) ? numberFormat($item->next_repair_distance) : null}}" />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="m-t-20"></div>
                        @include('layouts.backend.elements._submit_form_button')
                    </div>
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
@include('layouts.backend.elements.search._vehicle_search')
@include('layouts.backend.elements.search._driver_search')
<?php
$jsFiles = [
    'autoload/object-select2',
];
?>
{!! loadFiles($jsFiles, $area, 'js') !!}
<script>
    var urlDriver = '{{route('driver.combo-driver')}}',
    urlVehicle = '{{route('vehicle.combo-vehicle')}}',
    urlVehicleDriver = '{{route('driver.getVehicleDriver')}}',
    urlCodeConfig = '{{route('system-code-config.getCodeConfig')}}';

</script>
