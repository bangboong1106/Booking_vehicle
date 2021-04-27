@if(!$entities->total())
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle">{{$entity->username}}</td>
            <td class="text-middle">{{$entity->email}}</td>
            <td class="text-middle">{{$entity->description}}</td>
            <td class="text-middle">{{$entity->getDateTime('created_at', 'H:i:s d-m-Y')}}</td>
        </tr>
    @endforeach
@endif
