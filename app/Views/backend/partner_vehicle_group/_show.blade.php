<ul class="list-group">
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('code')}}</strong>
        <br/>{{$entity->code}}
    </li>
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('name')}}</strong>
        <br/>{{$entity->name}}
    </li>
    @if(isset($parent))
    <li class="list-group-item">
        <strong class="text-primary">{{$entity->tA('parent_id')}}</strong>
        <br/>{{ $parent->name }}
        {!! MyForm::hidden('parent_id', $entity->parent_id) !!}
    </li>
    @endif
</ul>