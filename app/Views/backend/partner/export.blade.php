<table>
    <thead>
        <tr>
            <th>{{trans('models.customer.attributes.customer_code')}}</th>
            <th>{{trans('models.customer.attributes.full_name')}}</th>
            <th>{{trans('models.customer.attributes.delegate')}}</th>
            <th>{{trans('models.customer.attributes.mobile_no')}}</th>
            <th>{{trans('models.customer.attributes.type')}}</th>
            <th>{{trans('models.customer.attributes.tax_code')}}</th>
            <th>{{trans('models.customer.attributes.address')}}</th>
            <th>{{trans('models.customer.attributes.birth_date')}}</th>
            <th>{{trans('models.customer.attributes.email')}}</th>
            <th>{{trans('models.customer.attributes.sex')}}</th>
            <th>{{trans('models.customer.attributes.note')}}</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->customer_code }}</td>
            <td>{{ $customer->full_name }}</td>
            <td>{{ $customer->delegate }}</td>
            <td>{{ $customer->mobile_no }}</td>
            <td>{{ $customer->getCustomerType() }}</td>
            <td>{{ $customer->tax_code }}</td>
            <td>{{ $customer->address }}</td>
            <td>{{ $customer->getDateTime('birth_date') }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->getSexText() }}</td>
            <td>{{ $customer->note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>