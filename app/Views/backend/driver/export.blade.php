<table>
    <thead>
    <tr>
        <th>{{ trans('models.driver.attributes.code') }}</th>
        <th>{{ trans('models.admin.attributes.username') }}</th>
        <th>{{ trans('models.admin.attributes.email') }}</th>
        <th>{{ trans('models.driver.attributes.full_name') }}</th>
        <th>{{ trans('models.driver.attributes.id_no') }}</th>
        <th>{{ trans('models.driver.attributes.mobile_no') }}</th>
        <th>{{ trans('models.driver.attributes.driver_license') }}</th>
        <th>{{ trans('models.driver.attributes.birth_date') }}</th>
        <th>{{ trans('models.driver.attributes.sex') }}</th>
        <th>{{ trans('models.driver.attributes.vehicle_team_id') }}</th>
        <th>{{ trans('models.driver.attributes.experience_drive') }}</th>
        <th>{{ trans('models.driver.attributes.work_date') }}</th>
{{--        <th>{{ trans('models.driver.attributes.vehicle_old') }}</th>--}}
        <th>{{ trans('models.driver.attributes.address') }}</th>
        <th>{{ trans('models.driver.attributes.hometown') }}</th>
        <th>{{ trans('models.driver.attributes.evaluate') }}</th>
        <th>{{ trans('models.driver.attributes.rank') }}</th>
        <th>{{ trans('models.driver.attributes.work_description') }}</th>
        <th>{{ trans('models.driver.attributes.note') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($drivers as $entity)
        <tr>
            <td>{{ $entity->code }}</td>
            <td>{{ $entity->tryGet('adminUser')->username }}</td>
            <td>{{ $entity->tryGet('adminUser')->email }}</td>
            <td>{{ $entity->full_name }}</td>
            <td>{{ $entity->id_no }}</td>
            <td>{{ $entity->mobile_no }}</td>
            <td>{{ $entity->driver_license }}</td>
            <td>{{ $entity->getDateTime('birth_date') }}</td>
            <td>{{ $entity->getSexText() }}</td>
            <td>{{ $entity->tryGet('vehicleTeam')->name }}</td>
            <td>{{ $entity->experience_drive }}</td>
            <td>{{ $entity->getDateTime('work_date') }}</td>
          {{--  <td>{{ $entity->vehicle_old }}</td>--}}
            <td>{{ $entity->address }}</td>
            <td>{{ $entity->hometown }}</td>
            <td>{{ $entity->evaluate }}</td>
            <td>{{ $entity->rank }}</td>
            <td>{{ $entity->work_description }}</td>
            <td>{{ $entity->note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>