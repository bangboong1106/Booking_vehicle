<table class="table table-bordered">
    <thead>
        <tr>
            <th>{{ trans('models.report_vehicle.attributes.reg_no') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.driver_names') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.total_order') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.total_route') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.total_route_average_per_day') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.total_order_on_time') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.total_order_late') }}</th>
            <th>{{ trans('models.report_vehicle.attributes.ratio_order') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entities as $entity)
            <tr>
                <td>{{ $entity['reg_no'] }}</td>
                <td>{{ $entity['driver_names'] }}</td>
                <td>{{ $entity['total_order'] }}</td>
                <td>{{ $entity['total_route'] }}</td>
                <td>{{ $entity['total_route_average_per_day'] }}</td>
                <td>{{ $entity['total_order_on_time'] }}</td>
                <td>{{ $entity['total_order_late'] }}</td>
                <td>{{ $entity['ratio_order'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>