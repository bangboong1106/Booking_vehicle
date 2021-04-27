<div class="form-info-wrap" data-id="{{ $entity->id }}" id="repair_ticket_model" data-quicksave=''
    data-entity='repair_ticket'>
    @if (isset($show_history) && $show_history)
        <div class="related-list">
            <span class="collapse-view dIB detailViewCollapse dvLeftPanel_show"
                onclick="showHideDetailViewLeftPanel(this);" id="dv_leftPanel_showHide" style="">
                <span class="svgIcons dIB fCollapseIn"></span>
            </span>
            <ul class="list-related-list">
                <li>
                    <span class="title">Thông tin</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingInformation" href="#">Thông tin chung</a></li>
                        <li><a class="list-info" data-dest="headingMapping" href="#">Thông tin sửa chữa</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ isset($show_history) && $show_history ? 'width-related-list' : '' }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row content-body">
                    @if (isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action')
                        </div>
                    @endif
                    <div class="col-md-12 content-detail">
                        <div class="{{ isset($showAdvance) ? 'first' : '' }} card-header" role="tab"
                            id="headingInformation">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                    aria-controls="collapseInformation" class="collapse-expand">
                                    {{ trans('models.order.attributes.information') }}
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel" aria-labelledby="headingOne"
                            style="">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'code'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'vehicle_id',
                                        'controlType' => 'link',
                                        'model' => 'vehicle'
                                        ])
                                    @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'driver_id',
                                        'controlType' => 'link',
                                        'model' => 'driver'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'repair_date', 'controlType' =>'date'])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'amount', 'controlType' =>'number'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'description'])
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-header" role="tab" id="headingMapping">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseMapping" aria-expanded="true"
                            aria-controls="collapseMapping" class="collapse-expand">
                            Thông tin sửa chữa
                        </a>
                    </h5>
                </div>
                <div id="collapseMapping" class="collapse show" role="tabpanel" aria-labelledby="headingMapping"
                    style="">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover" style="max-width: 1200px">
                                    <thead id="head_content">
                                        <tr class="active">
                                            <th style="width: 40px">
                                                STT
                                            </th>
                                            <th style="width: 250px">
                                                {{ $entity->tA('item.accessory_id') }}
                                            </th>
                                            <th style="width: 180px">
                                                {{ $entity->tA('item.quantity') }}
                                            </th>
                                            <th style="width: 180px">
                                                {{ $entity->tA('item.price') }}
                                            </th>
                                            <th style="width: 180px">
                                                {{ $entity->tA('item.amount') }}
                                            </th>
                                            <th style="width: 220px">
                                                {{ $entity->tA('item.next_repair_date') }}
                                            </th>
                                            <th style="width: 220px">
                                                {{ $entity->tA('item.next_repair_distance') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_content">
                                        @if (isset($entity->items) && count($entity->items) > 0)
                                            @foreach ($entity->items as $index => $item)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="text-left">
                                                        {{ $item->name_of_accessory_id }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ numberFormat($item->quantity) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ numberFormat($item->price) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ numberFormat($item->amount) }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ \Carbon\Carbon::parse($item->next_repair_date)->format('d-m-Y H:i') }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ numberFormat($item->next_repair_distance) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    {{ trans('models.template_payment.attributes.empty_mapping') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                {{--Thông tin hệ thống--}}
                <div class="card-header" role="tab" id="headingSystem">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseSystem" aria-expanded="true"
                            aria-controls="collapseNote" class="collapse-expand">
                            Thông tin hệ thống
                            <i class="fa"></i>
                        </a>
                    </h5>
                </div>
                <div id="collapseSystem" class="collapse show" role="tabpanel" aria-labelledby="note_info">
                    <div class="card-body">
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=>
                            isset($entity->insUser) ? $entity->insUser->username : '', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable'
                            => false, 'controlType'=>'datetime'])
                        </div>
                        <div class="form-group row">
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=>
                            isset($entity->updUser) ? $entity->updUser->username : '', 'isEditable' => false])
                            @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable'
                            => false, 'controlType'=>'datetime'])
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script>
    if (typeof allowEditableControlOnForm !== 'undefined') {
        var editableFormConfig = {};
        allowEditableControlOnForm(editableFormConfig);
    }

</script>
