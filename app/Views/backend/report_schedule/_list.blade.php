@include('layouts.backend.elements.column_config._list',[
    'entity'=>'report_schedule',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("report_schedule"),
    'configList'=> isset($configList) ? $configList : []])

{{-- @if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu</span></div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action')
            <td class="text-middle">
                <a class="detail-toggle" href="#">
                    {{$entity->description}}
                </a>
            </td>
            <td class="text-middle">{{$entity->email}}</td>
            <td class="text-center">{{$entity->getDateTime('date_from', 'd-m-Y')}}</td>
            <td class="text-center">{{$entity->getDateTime('date_to', 'd-m-Y')}}</td>
            <td class="text-middle">{!! $entity->getScheduleType()!!}</td>
            <td class="text-center">{{$entity->time_to_send}}</td>

        </tr>
    @endforeach
@endif --}}