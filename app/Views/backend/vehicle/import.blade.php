@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.reg_no') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.status') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.type') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.active') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.group_id') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.partner_id') }}</th>
            <th style="width: 250px">{{ trans('models.vehicle.attributes.driver') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.volume') }} (m3)</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.weight') }} (kg)</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.length') }} (m)</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.width') }} (m)</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.height') }} (m)</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.category_of_barrel') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.weight_lifting_system') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.max_fuel') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.max_fuel_with_goods') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.register_year') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle_general_info.attributes.brand') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.gps_company_id') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.repair_distance') }}</th>
            <th style="width: 150px">{{ trans('models.vehicle.attributes.repair_date') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td>{{$entity['row']}}</td>
                <td>{{$entity['reg_no']}}</td>
                <td>{{$entity['status_text']}}</td>
                <td>{{$entity['type_text']}}</td>
                <td>{{$entity['active_text']}}</td>
                <td>{{$entity['group_text']}}</td>
                <td>{{$entity['partner_text']}}</td>
                <td>{{$entity['driver_codes_text']}}</td>
                <td>{{$entity['volume_text']}}</td>
                <td>{{$entity['weight_text']}}</td>
                <td>{{$entity['length_text']}}</td>
                <td>{{$entity['width_text']}}</td>
                <td>{{$entity['height_text']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['category_of_barrel']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['weight_lifting_system']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['max_fuel_text']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['max_fuel_with_goods_text']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['register_year']}}</td>
                <td>{{$entity['vehicleGeneralInfo']['brand']}}</td>
                <td>{{$entity['gps_company_text']}}</td>
                <td>{{$entity['repair_distance']}}</td>
                <td>{{$entity['repair_date']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
