@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 100px">{{ trans('models.location.attributes.code') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.title') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.province_id') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.district_id') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.ward_id') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.address') }}</th>
            <th style="width: 100px">{{ trans('models.location.attributes.name_of_customer_id') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td>{{$entity['row']}}</td>
                <td>{{$entity['code']}}</td>
                <td>{{$entity['title']}}</td>
                <td>{{$entity['province_text']}}</td>
                <td>{{$entity['district_text']}}</td>
                <td>{{$entity['ward_text']}}</td>
                <td>{{$entity['address']}}</td>
                <td>{{$entity['customer_text']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>