<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped" style="width: 2000px">
        <thead>
            <tr>
                <th></th>
                <th>{{ trans('models.customer.attributes.customer_code') }}</th>
                <th>{{ trans('models.customer.attributes.full_name') }}</th>
                <th>{{ trans('models.customer.attributes.type') }}</th>
                <th>{{ trans('models.customer.attributes.username') }}</th>
                <th>{{ trans('models.customer.attributes.password') }}</th>
                <th>{{ trans('models.customer.attributes.email') }}</th>
                <th>{{ trans('models.customer.attributes.mobile_no') }}</th>
                <th>{{ trans('models.customer.attributes.delegate') }}</th>
                <th>{{ trans('models.customer.attributes.tax_code') }}</th>
                <th>{{ trans('models.customer.attributes.address') }}</th>
                <th>{{ trans('models.customer.attributes.birth_date') }}</th>
                <th>{{ trans('models.customer.attributes.sex') }}</th>
                <th>{{ trans('models.customer.attributes.note') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $entity)
                <tr>
                    <td>{!! $entity['importable'] ? '<p class="text-success">Hợp lệ</p>' : '<p class="text-danger">'.implode($entity['failures']).'</p>' !!}</td>
                    <td>{{$entity['customer_code']}}</td>
                    <td>{{$entity['full_name']}}</td>
                    <td>{!! $entity['type'] == config('constant.CORPORATE_CUSTOMERS') ? config('system.customer_type.1') :
                        config('system.customer_type.2') !!}</td>
                    <td>{{$entity['username']}}</td>
                    <td>{{$entity['password']}}</td>
                    <td>{{$entity['email']}}</td>
                    <td>{{$entity['mobile_no']}}</td>
                    <td>{{$entity['delegate']}}</td>
                    <td>{{$entity['tax_code']}}</td>
                    <td>{{$entity['current_address']}}</td>
                    <td>{{$entity['birth_date']}}</td>
                    <td>{!! $entity['sex'] == config('constant.MALE_SEX_TYPE') ? config('system.sex.male') : config('system.sex.female') !!}</td>
                    <td>{{$entity['note']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>