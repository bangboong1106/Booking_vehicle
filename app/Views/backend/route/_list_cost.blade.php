<table class="table table-bordered table-hover table-cost view">
    <thead id="head_content">
    <tr class="active">
        <th scope="col" class="text-left">
            Diễn giải
        </th>
        <th scope="col"
            class="text-right">{{ trans('models.route.attributes.amount_admin') }}</th>
        <th scope="col"
            class="text-right">{{ trans('models.route.attributes.amount_driver') }}</th>
        <th scope="col"
            class="text-right">{{ trans('models.route.attributes.amount_approval') }}</th>
    </tr>
    </thead>
    <tbody id="body_content">
    @if($entity->listCost)
        @foreach($entity->listCost as $cost)
            <tr>
                <td class="text-left">
                    {{$cost['receipt_payment_name']}}
                </td>
                <td class="text-right">{{ numberFormat($cost['amount_admin']) }}</td>
                <td class="text-right">{{ numberFormat($cost['amount_driver']) }}</td>
                <td class="text-right">{{ numberFormat($cost['amount']) }}</td>
            </tr>
        @endforeach
    @endif
    <tr>
        <td class="text-left"><b>Tổng định mức chi phí (VND)</b></td>
        <td class="text-right"><b>{{numberFormat($entity->total_cost_admin)}}</b></td>
        <td class="text-right"><b>{{numberFormat($entity->total_cost_driver)}}</b></td>
        <td class="text-right"><b>{{numberFormat($entity->final_cost)}}</b></td>
    </tr>
    </tbody>
</table>