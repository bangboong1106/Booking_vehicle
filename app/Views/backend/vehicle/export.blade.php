<table>
    <thead>
    <tr>
        <th>{{ trans('models.vehicle.attributes.reg_no') }}</th>
        <th>{{ trans('models.vehicle.attributes.status') }}</th>
        <th>{{ trans('models.vehicle.attributes.active') }}</th>
        <th>{{ trans('models.vehicle.attributes.group_id') }}</th>
        <th>{{ trans('models.vehicle.attributes.driver') }}</th>
        <th>{{ trans('models.vehicle.attributes.volume') }}</th>
        <th>{{ trans('models.vehicle.attributes.weight') }}</th>
        <th>{{ trans('models.vehicle.attributes.length') }}</th>
        <th>{{ trans('models.vehicle.attributes.width') }}</th>
        <th>{{ trans('models.vehicle.attributes.height') }}</th>
        <th>{{ trans('models.vehicle_general_info.attributes.category_of_barrel') }}</th>
        <th>{{ trans('models.vehicle_general_info.attributes.weight_lifting_system') }}</th>
        <th>{{ trans('models.vehicle_general_info.attributes.max_fuel') }}</th>
        <th>{{ trans('models.vehicle_general_info.attributes.register_year') }}</th>
        <th>{{ trans('models.vehicle_general_info.attributes.brand') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vehicles as $entity)
        <tr>
            <td>{{ $entity->reg_no }}</td>
            <td>{{ $entity->getStatus() }}</td>
            <td>{{ $entity->getActive() }}</td>
            <td>{{ $entity->tryGet('vehicleGroup')->name }}</td>
            <td>{{ $entity->driver_codes }}</td>
            <td>{{ $entity->volume }}</td>
            <td>{{ $entity->weight }}</td>
            <td>{{ $entity->length }}</td>
            <td>{{ $entity->width }}</td>
            <td>{{ $entity->height }}</td>
            <td>{{ $entity->tryGet('vehicleGeneralInfo')->category_of_barrel }}</td>
            <td>{{ $entity->tryGet('vehicleGeneralInfo')->weight_lifting_system }}</td>
            <td>{{ $entity->tryGet('vehicleGeneralInfo')->max_fuel }}</td>
            <td>{{ $entity->tryGet('vehicleGeneralInfo')->register_year }}</td>
            <td>{{ $entity->tryGet('vehicleGeneralInfo')->brand }}</td>
        </tr>
    @endforeach
    </tbody>
</table>