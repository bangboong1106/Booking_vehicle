@include('layouts.backend.elements._import_excel_notification')
<div class="table-responsive import-table-scroll">
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 130px"></th>
            <th style="width: 100px">Dòng số</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.code') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.full_name') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.mobile_no') }}</th>
            <th style="width: 150px">{{ trans('models.admin.attributes.username') }}</th>
            <th style="width: 200px">{{ trans('models.admin.attributes.email') }}</th>
            <th style="width: 150px">{{ trans('models.admin.attributes.password') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.vehicle_team_id') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.id_no') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.driver_license') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.birth_date') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.sex') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.experience_drive') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.work_date') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.address') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.hometown') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.evaluate') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.rank') }}</th>
            <th style="width: 200px">{{ trans('models.driver.attributes.work_description') }}</th>
            <th style="width: 150px">{{ trans('models.driver.attributes.note') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr {!! $entity['importable'] ? 'class="importable"' : 'class="un-importable"' !!}>
                <td>{!! $entity['importable'] ? '<p class="text-success">'.trans('messages.valid').'</p>'
                    : '<p class="text-danger">'.implode('<br>',$entity['failures']).'</p>' !!}</td>
                <td>{{$entity['row']}}</td>
                <td>{{$entity['code']}}</td>
                <td>{{$entity['full_name']}}</td>
                <td>{{$entity['mobile_no']}}</td>
                <td>{{$entity['adminUser']['username']}}</td>
                <td style="word-wrap: break-word">{{$entity['adminUser']['email']}}</td>
                <td>{{isset($entity['adminUser']['password']) ? $entity['adminUser']['password'] : ''}}</td>
                <td>{{$entity['vehicle_team_text']}}</td>
                <td>{{$entity['id_no']}}</td>
                <td>{{$entity['driver_license']}}</td>
                <td>{{$entity['birth_date']}}</td>
                <td>{!! $entity['sex_text'] !!}</td>
                <td>{{$entity['experience_drive']}}</td>
                <td>{{$entity['work_date']}}</td>
                <td>{{$entity['address']}}</td>
                <td>{{$entity['hometown']}}</td>
                <td>{{$entity['evaluate']}}</td>
                <td>{{$entity['rank']}}</td>
                <td>{{$entity['work_description']}}</td>
                <td>{{$entity['note']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>