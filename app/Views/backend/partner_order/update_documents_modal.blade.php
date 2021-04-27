@php
    $today = (new DateTime());
@endphp
<div class="modal fade order-modal" id="mass_update_documents" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
                <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">Cập nhật chứng từ</h4>
            </div>
            <div class="modal-body" style="overflow-y: visible">
                <input type="hidden" id="type_update">
                <div class="form-group">
                    Bạn có muốn cập nhật trạng thái của chứng từ cho các đơn hàng <b><span id="span_orders"></span></b>
                    không?
                </div>
                <div class="row">
                    <div class="col-12">
                        {!! MyForm::label('datetime_collected_documents_reality',trans('models.order.attributes.datetime_collected_documents_reality'), [], false) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {!! MyForm::text('time_collected_documents_reality', $today->format('H-i') ,
                        ['placeholder'=>trans('models.order.attributes.time_collected_documents_reality'), 'class'=>'timepicker time-input', 'data-field' => 'time'
                        , 'id'=>'time_collected_documents_reality']) !!}
                    </div>
                    <div class="col-md-8">
                        {!! MyForm::text('date_collected_documents_reality',  $today->format('d-m-Y') ,
                        ['placeholder'=>trans('models.order.attributes.date_collected_documents_reality'), 'class'=>'datepicker date-input', 'data-field' => 'date'
                        ,'id'=>'date_collected_documents_reality']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-10 text-right">
                    <a href="#" class="btn " data-dismiss="modal">{{trans('actions.close')}}</a>
                    <button type="button" id="btn-mass-update-documents"
                            class="btn btn-blue" style="width: 120px">
                        <i class="fa fa-save" style="margin-right: 8px"></i>Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>