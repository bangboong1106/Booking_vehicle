<ol class="dd-list">
    @if($children)
        @foreach($children as $child)
            <li class="dd-item no-drag">
                <div class="dd-handle no-drag">
                    <a href="{{backUrl('vehicle-group.edit', $child->id)}}">{{ $child->name }}</a>
                    @can('delete vehicle_group')
                        <a class="delete-action" href="#del-confirm"
                           style="display:inline-block"
                           data-toggle="modal"
                           data-action="{{backUrl($deleteRoute,$child->id)}}"><i class="fa fa-trash" aria-hidden="true"
                                                                                 title="{{trans('actions.destroy')}}"></i></a>
                    @endcan
                </div>
                @if(count($child->children()->get()))
                    @include('backend.vehicle_group.manageChild',['children' => $child->children()->get()])
                @endif
            </li>
        @endforeach
    @endif
</ol>