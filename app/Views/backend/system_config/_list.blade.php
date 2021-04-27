@if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm mã hệ thống</span></i>
                </a>
            </div>
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            @include('layouts.backend.elements.list_to_action')
            <td>{{$entity->key}}</td>
            <td>{{$entity->value}}</td>
            <td>{{$entity->description}}</td>
        </tr>
    @endforeach
@endif