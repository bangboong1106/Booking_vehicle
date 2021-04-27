@include('layouts.backend.elements.column_config._list',[
    'entity'=>'contract',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("contract"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total())
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm hợp đồng</span></i>
                </a>
            </div>
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action')
            <td class="text-middle" data-name="true">
                <a class="detail-toggle" href="#">

                    {{$entity->contract_no}}
                </a>
            </td>
            <td class="text-middle">{{ empty($entity->customer) ? '' : $entity->customer->full_name }}</td>
            <td class="text-middle text-center">{{ $entity->getDate('issue_date') }}</td>
            <td class="text-middle text-center">{{ $entity->getDate('expired_date') }}</td>
            <td class="text-middle">{{ $entity->tryGet('contractType')->name }}</td>
            <td class="text-middle" style="width: 100px">{{ $entity->getStatus() }}</td>

        </tr>
    @endforeach
@endif --}}