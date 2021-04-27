@if($auditing->count() === 0)
    <p>{{ trans('messages.no_result_found') }}</p>
@else
    <table id="mainTable" class="table table-striped">
        <thead>
        <tr>
            <td>{{ trans('models.auditing.attributes.username') }}</td>
            <td>{{ trans('actions.action') }}</td>
            <td>{{ trans('models.auditing.attributes.old_values') }}</td>
            <td>{{ trans('models.auditing.attributes.new_values') }}</td>
            <td>{{ trans('models.auditing.attributes.time') }}</td>
        </tr>
        </thead>
        <tbody>
        @foreach($auditing as $item)
            <tr>
                <td>{{ isset($item->user) ? $item->user->username : '' }}</td>
                <td>{{ trans('models.auditing.actions.' . $item->event) }}</td>
                <td>
                    @foreach($item->old_values as $attr => $value)
                        <p class="m-0">{{ $entity->tA($attr) }} : {{ $value }}</p>
                    @endforeach
                </td>
                <td>
                    @foreach($item->new_values as $attr => $value)
                        <p class="m-0">{{ $entity->tA($attr) }} : {{ $value }}</p>
                    @endforeach
                </td>
                <td>{{ $item->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif