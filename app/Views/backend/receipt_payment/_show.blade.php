<ul class="list-group">
    <li class="list-group-item">
        @if ($entity->type == 1)
            <strong class="text-primary">{{ $entity->tA('name_thu') }}</strong>
        @else
            <strong class="text-primary">{{ $entity->tA('name_chi') }}</strong>
        @endif
        <br />{{ $entity->name }}
    </li>
    <li class="list-group-item">
        {!! MyForm::label('is_display_driver', $entity->tA('is_display_driver'), [], false) !!}
        <br/>
        <span class="view-control" id="is_display_driver">
            @if($entity->is_display_driver == "1")
                <i class="fa fa-check" aria-hidden="true"></i>
            @else
                <i class="fa"></i>
            @endif
        </span>
    </li>
    <li class="list-group-item">
        <table class="table table-bordered table-hover" style="width: 100% !important;">
            <thead id="head_content">
                <tr class="active">
                    <th scope="col" class="text-center">
                        STT
                    </th>
                    <th scope="col" class="text-left">Chi phí mặc định (VND)</th>
                </tr>
            </thead>
            <tbody id="body_content">
                @if (isset($entity->amount_list))
                    @foreach ($entity->amount_list as $index => $amount_item)
                        <tr>
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>
                            <td class="text-right">
                                {{ numberFormat($amount_item) }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </li>
    {{-- <li class="list-group-item">
        @if ($entity->type == 1)
            <strong class="text-primary">{{ $entity->tA('parent_thu') }}</strong>
            @else
            <strong class="text-primary">{{ $entity->tA('parent_chi') }}</strong>
        @endif
        <br />{{ $parent_name }}
        {!! MyForm::hidden('parent_id', $entity->parent_id) !!}
    </li> --}}
</ul>
