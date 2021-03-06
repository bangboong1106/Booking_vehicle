<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">

<link rel="stylesheet"
      href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css">

<script>
    let urlLocation = '{{route('location.combo-location')}}',
        locationDestinationId = '{{empty($entity->location_destination_id) ? 0 : $entity->location_destination_id}}',
        locationArrivalId = '{{empty($entity->location_arrival_id) ? 0 : $entity->location_arrival_id}}';

    let backendUri = '{{getBackendDomain()}}';
    let costsUri = '{{route('quota.suggest-costs-by-locations')}}';

</script>
<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['quota.valid', $entity->id], 'id'=>'quota_form'])!!}

        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    {!! MyForm::hidden('locations', $locations, ['id'=>'locations']) !!}
                    {!! MyForm::hidden('costs', $costs, ['id'=>'costs']) !!}
                    {!! MyForm::hidden('update_route', false, ['id'=>'update_route']) !!}
                    <div class="content-body">
                        <div class="card-header" role="tab" id="headingInformation">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                   aria-controls="collapseInformation" class="collapse-expand">
                                    {{trans('models.order.attributes.information')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! MyForm::label('quota_code', $entity->tA('quota_code')  . ' <span class="text-danger">*</span>', [], false) !!}
                                        {!! MyForm::text('quota_code', $entity->quota_code != null ? $entity->quota_code : $code, ['placeholder'=>$entity->tA('quota_code')]) !!}

                                    </div>
                                    <div class="col-md-6">
                                        {!! MyForm::label('name', $entity->tA('name')  . ' <span class="text-danger">*</span>', [], false) !!}
                                        {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name'), 'maxLength' => 255]) !!}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        {!! MyForm::label('vehicle_group_id', $entity->tA('vehicle_group_id')) !!}
                                        {!! MyForm::dropDown('vehicle_group_id', $entity->vehicle_group_id, $vehicle_group_list, true, [ 'class' => 'select2 minimal','id' => 'vehicle_group_id']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! MyForm::label('distance', $entity->tA('distance')) !!}
                                        {!! MyForm::text('distance', numberFormat($entity->distance - $entity->distance),['class' => 'number-input','id'=>'distance']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingRoute">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseRoute" aria-expanded="true"
                                   aria-controls="collapseInformation" class="collapse-expand">
                                    Th??ng tin l??? tr??nh
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseRoute" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <div class=" form-group row">
                                    <div class="timeline location">
                                        <article class="timeline-item">
                                            <div class="timeline-desk">
                                                <div class="panel">
                                                    <div class="panel-body">
                                                        <span class="arrow"></span>
                                                        <span class="timeline-icon"></span>
                                                        <div class="row">
                                                            <div class="col-md-11">
                                                                <div class="input-group with-button-add">
                                                                    <select class="select2 select-location"
                                                                            name="location_0"
                                                                            id="location_0">
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-third quick-add"
                                                                                type="button" data-model="location"
                                                                                data-url="{{route('location.advance')}}">
                                                                            <i class="fa fa-plus"
                                                                               aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 delete-timeline">
                                                                <span class="delete-timeline-item">X</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group">
                                                        <span></span>
                                                        <input type="hidden" class="location-group-id" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <article class="timeline-item">
                                            <div class="timeline-desk">
                                                <div class="panel">
                                                    <div class="panel-body">
                                                        <span class="arrow"></span>
                                                        <span class="timeline-icon"></span>
                                                        <div class="row">
                                                            <div class="col-md-11">
                                                                <div class="input-group with-button-add">
                                                                    <select class="select2 select-location"
                                                                            name="location_1"
                                                                            id="location_1">
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-third quick-add"
                                                                                type="button"
                                                                                data-model="location"
                                                                                data-url="{{route('location.advance')}}">
                                                                            <i class="fa fa-plus"
                                                                               aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 delete-timeline">
                                                                <span class="delete-timeline-item">X</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group">
                                                        <span></span>
                                                        <input type="hidden" class="location-group-id" value=""/>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <article class="timeline-item add-route">
                                            <div class="add-plus text-left ">
                                                <div class="time-show">
                                                <span class="fa-stack fa-lg">
                                                <i class="fa fa-circle fa-stack-2x"></i>
                                                <strong class="fa-stack-1x text-white">+</strong>
                                            </span>
                                                </div>

                                            </div>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab" id="headingCost">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseCost" aria-expanded="true"
                                   aria-controls="collapseCost" class="collapse-expand">
                                    Th??ng tin chi ph??
                                    <i class="fa"></i>

                                </a>
                            </h5>
                        </div>
                        <div id="collapseCost" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <table class="table table-bordered table-hover table-cost">
                                <thead id="head_content">
                                <tr class="active">
                                    <td style="font-size: 14px; font-weight: bold;">
                                        Di???n gi???i
                                    </td>
                                    <td style="width: 200px; font-size: 14px; font-weight: bold;" class="text-right">S???
                                        ti???n (VND)
                                    </td>
                                    <td style="width: 80px" class="text-center"></td>
                                </tr>
                                <tbody id="body_content">
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <select class="select2 select-cost" name="cost_0" id="cost_0">
                                                <option></option>
                                                @if($receiptPayments)
                                                    @foreach($receiptPayments as $receiptPayment)
                                                        <option value="{{explode("_", $receiptPayment)[0]}}">
                                                            {{explode("_", $receiptPayment)[1]}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input placeholder="S??? ti???n"
                                                   class="number-input form-control"
                                                   name="amount_0" type="text" id="amount_0" aria-invalid="false"
                                                   value="0"/>
                                        </div>
                                    </td>
                                    <td class="text-center text-middle">
                                        <a class="btn l-create cost" style="display:inline-block" title="Th??m">
                                            <i class="fa fa-plus" title="Th??m"></i>
                                        </a>
                                        <a class="delete-cost" href="#" style="display:inline-block" title="X??a">
                                            <i class="fa fa-trash" aria-hidden="true" title="X??a"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <td>S??? d??ng: <span class="row-number">1</span></td>
                                <td class="result-cost text-right"></td>
                                <td></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                @if($entity->id)
                    <div class="submit-button text-right row">
                        <div class="col-md-6">
                            <h4 class="m-t-0 header-title">
                                {{$entity->id ? (strpos($routeName, 'duplicate') ? trans('actions.create').' '. transb($routePrefix.'.name')
                                : trans('actions.edit').' '. transb($routePrefix.'.name'))
                                : trans('actions.create').' '. transb($routePrefix.'.name')}}
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <span class="padr20">
                                <a class="btn btn-default back-button" href="{!! getBackUrl() !!}">
                                    <span class="ls-icon ls-icon-reply"
                                          aria-hidden="true"></span>    <i
                                            class="fa fa-backward"></i>{{trans('actions.back')}}
                                </a>
                            </span>
                            <span>
                                <button type="button" id="update_quota" class="btn btn-blue">
                                    <span class="ls-icon ls-icon-check"
                                          aria-hidden="true"></span> {{trans('actions.submit')}}
                                </button>
                            </span>
                        </div>
                    </div>
                @else
                    @include('layouts.backend.elements._submit_form_button')
                @endif

            </div>
        </div>
        {!! MyForm::close() !!}
    </div>

</div>

{{--X??c nh???n load th??ng tin chi ph??--}}
<div class="modal fade" id="confirm-location" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">X??c nh???n c???p nh???t th??ng tin ?????a ??i???m<span></span></h4>
            </div>
            <div class="modal-body">
                H??? th???ng s??? l???y th??ng tin ?????a ??i???m ????? thay th??? cho ?????a ??i???m ???? ch???n<br/>
                B???n c?? mu???n th???c hi???n thay th??? hay kh??ng?
            </div>
            <div class="modal-footer">
                <button id="close-confirm-location" type="button" class="btn btn-default" data-dismiss="modal">????ng
                </button>
                <button id="update-confirm-location" type="button" class="btn btn-success">X??c nh???n</button>
            </div>
        </div>
    </div>
</div>
{{--X??c nh???n load th??ng tin chi ph??--}}
<div class="modal fade" id="confirm-costs" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">X??c nh???n th??ng tin chi ph??<span></span></h4>
            </div>
            <div class="modal-body">
                H??? th???ng ph??t hi???n t???n t???i B???ng ?????nh m???c chi ph?? tr??ng v???i l??? tr??nh tr??ng b???n ???? ch???n.<br/>
                B???n c?? mu???n l???y th??ng tin chi ph?? c???a b???ng ?????nh m???c ???? l??n th??ng tin chi ph?? hi???n t???i hay kh??ng?
            </div>
            <div class="modal-footer">
                <button id="close-confirm-costs" type="button" class="btn btn-default" data-dismiss="modal">????ng
                </button>
                <button id="update-confirm-costs" type="button" class="btn btn-success">X??c nh???n</button>
            </div>
        </div>
    </div>
</div>

{{--X??c nh???n c???p nh???t b???ng ?????nh m???c v??o chuy???n--}}
<div class="modal fade" id="confirm-route" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">X??c nh???n c???p nh???t<span></span></h4>
            </div>
            <div class="modal-body">
                H??? th???ng s??? l???y th??ng tin c??c chi ph?? c???a b???ng ?????nh m???c ????? c???p nh???t cho c??c chuy???n ch??a ph?? duy???t s???
                d???ng b???ng ?????nh m???c
                n??y .<br/>
                B???n c?? mu???n th???c hi???n c???p nh???t v??o c??c chuy???n hay kh??ng ?
            </div>
            <div class="modal-footer">
                <button id="close-confirm-route" type="button" class="btn btn-default" data-dismiss="modal">H???y
                </button>
                <button id="cancel-confirm-route" type="button" class="btn btn-blue">L??u</button>
                <button id="update-confirm-route" type="button" class="btn btn-success">L??u v?? C???p nh???t</button>

            </div>
        </div>
    </div>
</div>
