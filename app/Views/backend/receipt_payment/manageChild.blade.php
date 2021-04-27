<ol class="dd-list">
    @foreach($children as $child)
        <li class="dd-item">
            <div class="dd-handle">
                @if($child->is_system == 0)
                    <a href="{{backUrl('receipt-payment.edit', $child->id)}}">{{ $child->name }}</a>
                @else
                    {{$child->name }}
                @endif
                <span>{{$category->amount}}<span>
                {{--TODO Ẩn nút xóa tạm thời--}}
                {{--@can('delete receipt_payment')--}}
                    {{--@if($child->is_system == 0)--}}
                        {{--<a class="delete-action" href="#del-confirm"--}}
                           {{--style="display:inline-block"--}}
                           {{--data-toggle="modal"--}}
                           {{--data-action="{{backUrl($deleteRoute,$child->id)}}">--}}
                            {{--<i class="fa fa-trash" aria-hidden="true" title="{{trans('actions.destroy')}}"></i>--}}
                        {{--</a>--}}
                    {{--@endif--}}
                {{--@endcan--}}
            </div>
            @if(count($child->children()->get()))
                @include('backend.receipt_payment.manageChild',['children' => $child->children()->get()])
            @endif
        </li>
    @endforeach
</ol>