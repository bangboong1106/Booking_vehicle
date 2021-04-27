@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 100px">{{ trans('models.client.attributes.customer_code') }}</th>
            <th style="width: 100px">{{ trans('models.client.attributes.parent_id') }}</th>
            <th style="width: 100px">{{ trans('models.client.attributes.full_name') }}</th>
            <th style="width: 100px">{{ trans('models.client.attributes.type') }}</th>
            <th style="width: 150px">{{ trans('models.admin.attributes.username') }}</th>
            <th style="width: 150px">{{ trans('models.admin.attributes.password') }}</th>
            <th style="width: 200px">{{ trans('models.admin.attributes.email') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.mobile_no') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.delegate') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.tax_code') }}</th>
            <th style="width: 200px">{{ trans('models.client.attributes.address') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.birth_date') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.sex') }}</th>
            <th style="width: 150px">{{ trans('models.client.attributes.note') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td>{{$entity['row']}}</td>
                <td>{{$entity['customer_code']}}</td>
                <td>{{$entity['parent_text']}}</td>
                <td>{{$entity['full_name']}}</td>
                <td>{!! $entity['type'] == config('constant.CORPORATE_CUSTOMERS') ? config('system.customer_type.1') :
                        config('system.customer_type.2') !!}</td>
                <td>{{$entity['adminUser']['username']}}</td>
                <td>{{ isset($entity['adminUser']['password']) ? $entity['adminUser']['password'] : ''}}</td>
                <td style="word-wrap: break-word">{{$entity['adminUser']['email']}}</td>
                <td>{{$entity['mobile_no']}}</td>
                <td>{{$entity['delegate']}}</td>
                <td>{{$entity['tax_code']}}</td>
                <td>{{$entity['current_address']}}</td>
                <td>{{$entity['birth_date']}}</td>
                <td>{!! $entity['sex_text'] !!}</td>
                <td>{{$entity['note']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>