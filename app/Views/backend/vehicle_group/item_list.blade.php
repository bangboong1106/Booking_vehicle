<ol class="dd-list">
    @if($categories)
        @foreach($categories as $category)
            <li class="dd-item no-drag">
                <div class="dd-handle no-drag">
                    <a href="{{backUrl('vehicle-group.edit', $category->id)}}">{{ $category->name }}</a>
                    @can('delete vehicle_group')
                        <a class="delete-action" href="#del-confirm"
                           style="display:inline-block"
                           data-toggle="modal"
                           data-action="{{backUrl($deleteRoute,$category->id)}}">
                            <i class="fa fa-trash" aria-hidden="true"
                               title="{{trans('actions.destroy')}}"></i></a>
                    @endcan
                </div>
                @if(count($category->children()->get()))
                    @include('backend.vehicle_group.manageChild',['children' => $category->children()->get()])
                @endif

            </li>
        @endforeach
    @endif
</ol>