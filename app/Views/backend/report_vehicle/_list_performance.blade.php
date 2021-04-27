<table class="table table-bordered">
    <thead>
    <tr>
        <th>{{ trans('models.report_vehicle.attributes.reg_no') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.driver_names') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.distance') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.distance_average_per_day') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.total_order') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.total_route') }}</th>
        @can('view revenue')
        <th>{{ trans('models.report_vehicle.attributes.total_amount') }}</th>
        @endcan
        @can('view cost')
        <th>{{ trans('models.report_vehicle.attributes.total_cost') }}</th>
        @endcan
        @can('view revenue')
        <th>{{ trans('models.report_vehicle.attributes.total_commission') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.total_cod') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.revenue') }}</th>
        <th>{{ trans('models.report_vehicle.attributes.ratio_revenue') }}</th>
        @endcan
    </tr>
    </thead>
    <tbody>
    @foreach($entities as $entity)
        <tr>
            <td>{{ $entity['reg_no'] }}</td>
            <td>{{ $entity['driver_names'] }}</td>
            <td>{{ numberFormat($entity['distance']) }}</td>
            <td>{{ numberFormat($entity['distance_average_per_day']) }}</td>
            <td>{{ numberFormat($entity['total_order']) }}</td>
            <td>{{ numberFormat($entity['total_route']) }}</td>
            @can('view revenue')
            <td>{{ numberFormat($entity['total_amount']) }}</td>
            @endcan
            @can('view cost')
            <td>{{ numberFormat($entity['total_cost']) }}</td>
            @endcan
            @can('view revenue')
            <td>{{ numberFormat($entity['total_commission']) }}</td>
            <td>{{ numberFormat($entity['total_cod']) }}</td>
            <td>{{ numberFormat($entity['revenue']) }}</td>
            <td>{{ numberFormat($entity['ratio_revenue']) }}</td>
            @endcan
        </tr>
    @endforeach
    </tbody>
</table>