@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 100px">{{ trans('models.quota.attributes.quota_code') }}</th>
            <th style="width: 200px">{{ trans('models.quota.attributes.name') }}</th>
            <th style="width: 200px">{{ trans('models.quota.attributes.vehicle_group_id') }}</th>
            <th style="width: 250px">{{ trans('models.quota.attributes.location_destination_id') }}</th>
            <th style="width: 250px">{{ trans('models.quota.attributes.location_arrival_id') }}</th>
            <th style="width: 100px">{{ trans('models.quota.attributes.distance') }}</th>
            @foreach($listCost as $i=>$cost)
                @if($i != 0)
                    <th style="width: 150px">{{$cost}}</th>
                @endif
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td>{{$entity['row']}}</td>
                <td>{{$entity['quota_code']}}</td>
                <td>{{$entity['name']}}</td>
                <td>{{$entity['vehicle_group_text']}}</td>
                <td>{{$entity['location_destination_text']}}</td>
                <td>{{$entity['location_arrival_text']}}</td>
                <td>{{$entity['distance']}}</td>
                @foreach($entity['listCost'] as $i=>$costItem)
                    @if($i != 0)
                        <td style="width: 150px">{{isset($costItem) ? numberFormat($costItem, '.', ',', ' VND') : ''}}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>