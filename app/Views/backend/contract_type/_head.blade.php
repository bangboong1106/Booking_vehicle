@include('layouts.backend.elements.column_config._head',[
    'entity'=>'contract_type',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' =>  getColumnConfig("contract_type"),
    'configList'=> isset($configList) ? $configList : []])


{{-- @if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu trên chương trình</span><span>Không thể tìm thấy dữ liệu trên chương trình</span>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action')
            <td class="text-middle" data-name="true">
                <a class="detail-toggle" href="#">
                    {{$entity->name}}
                </a>
            </td>
            <td class="text-middle">{{$entity->description}}</td>
            <td class="text-center">{{$entity->ins_date}}</td>
            <td class="text-center">{{$entity->upd_date}}</td>

        </tr>
    @endforeach
@endif --}}