<div class="form-info-wrap" data-id="{{ $entity->id }}" id="customer_default_data_model"
    data-entity='customer_default_data'>
    <div>
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row form-info" data-id="{{ $entity->id }}">
                    @if (isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action')
                        </div>
                    @endif
                    <div class="col-md-12 content-body">
                        <div class="content-detail">
                            <div class="{{ isset($showAdvance) ? 'first' : '' }} card-header" role="tab"
                                id="headingInformation">
                                <h5 class="mb-0 mt-0 font-16">
                                    <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                        aria-controls="collapseInformation" class="">
                                        {{ trans('models.driver.attributes.information') }}
                                        <i class="fa"></i>

                                    </a>
                                </h5>
                            </div>
                            <div id="collapseInformation" class="collapse show" role="tabpanel"
                                aria-labelledby="headingOne" style="">
                                <div class="card-body">
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',
                                        ['property' => 'code',
                                        'isEditable' => false])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',
                                        ['property' => 'customer_id',
                                        'isEditable' => false, 'value' => isset($entity->customer) ?
                                        $entity->customer->full_name : "-"])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',
                                        ['property' => 'client_id',
                                        'isEditable' => false, 'value' => isset($entity->client) ?
                                        $entity->client->full_name : "-"])
                                    </div>
                                    <div class="form-group row">
                                        <?php
                                        $locationDestinationList = '';
                                        foreach ($entity->locationDestinationAttributes() as $item) {
                                        $locationDestinationList .= '<a class="tag-order detail-info" href="#"
                                            style="margin: 0 1px;"
                                            data-show-url="' . (isset($showAdvance) ? route('location.show', $item->id) : '') . '"
                                            data-id="' . $item->id . '">' . $item->title . '</a>';
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'location_destination_ids',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'widthWrap'=>'col-md-12',
                                        'value'=> $locationDestinationList
                                        ])
                                    </div>
                                    <div class="form-group row">
                                        <?php
                                        $locationArrivalList = '';
                                        foreach ($entity->locationArrivalAttributes() as $item) {
                                        $locationArrivalList .= '<a class="tag-order detail-info" href="#" style="margin: 0 1px;"
                                            data-show-url="' . (isset($showAdvance) ? route('location.show', $item->id) : '') . '"
                                            data-id="' . $item->id . '">' . $item->title . '</a>';
                                        }
                                        ?>
                                        @include('layouts.backend.elements.detail_to_edit',[
                                        'property' => 'location_arrival_ids',
                                        'isEditable' => false,
                                        'controlType' => 'label',
                                        'widthWrap'=>'col-md-12',
                                        'value'=> $locationArrivalList
                                        ])
                                    </div>
                                   {{-- <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' =>
                                        'system_code_config_id', 'isEditable' => false, 'value' =>
                                        isset($entity->systemCodeConfig) ? $entity->systemCodeConfig->prefix : "-"])
                                    </div>--}}
                                </div>
                            </div>
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
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id',
                                        'value'=> isset($entity->insUser) ? $entity->insUser->username : '',
                                        'isEditable' => false])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date',
                                        'isEditable' => false, 'controlType'=>'datetime'])
                                    </div>
                                    <div class="form-group row">
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id',
                                        'value'=> isset($entity->updUser) ? $entity->updUser->username : '',
                                        'isEditable' => false])
                                        @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date',
                                        'isEditable' => false, 'controlType'=>'datetime'])
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
