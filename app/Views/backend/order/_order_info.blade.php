{{--Thông tin chung--}}
<div class="card-header" role="tab" id="headingInformation">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseInformation"
           aria-expanded="true" aria-controls="collapseInformation" class="collapse-expand">
            {{trans('models.order.attributes.information')}}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseInformation" class="collapse show" role="tabpanel"
     aria-labelledby="headingOne" style="">
    @if(!empty($entity->extend_cost) && $entity->extend_cost != 0)
        <div class="stamp">
            <div class="box cost"></div>
        </div>
    @endif
    <div class="card-body m-l-24">
        <div class="form-group row">
            <div class="col-md-4">
                <label>Dạng mã hệ thống</label>
                <br/>
                <div>
                    <div class="code-config input-group select2-bootstrap-prepend">
                        <select class="select2 select-code-config" id="code_config"
                                name="code_config">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('order_code', $entity->tA('order_code'). ' <span class="text-danger">*</span>', [], false) !!}
                {!! MyForm::text('order_code', $entity->id != null ? $entity->order_code: $order_code, ['placeholder'=>$entity->tA('order_code'),'id'=>'order_code','class'=>'order-code']) !!}
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('order_no', $entity->tA('order_no'). ' (Delivery note)', [], false) !!}
                {!! MyForm::text('order_no', $entity->order_no, ['placeholder'=>$entity->tA('order_no'), 'readonly'=> true]) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('bill_no', $entity->tA('bill_no'). ' (Invoice number)', [], false) !!}
                {!! MyForm::text('bill_no', $entity->bill_no, ['placeholder'=>$entity->tA('bill_no')]) !!}
            </div>
        </div>
        @if(env('SUPPORT_CAR_TRANSPORTATION', false))
            <div class="form-group row">
                <div class="col-md-4">
                    {!! MyForm::label('model_no', $entity->tA('model_no'), [], false) !!}
                    {!! MyForm::text('model_no', $entity->model_no, ['placeholder'=>$entity->tA('model_no')]) !!}
                </div>
                <div class="col-md-4">
                    {!! MyForm::label('vin_no', $entity->tA('vin_no'), [], false) !!}
                    {!! MyForm::text('vin_no', $entity->vin_no, ['placeholder'=>$entity->tA('vin_no')]) !!}
                </div>
            </div>
        @endif
        {{--Khách hàng--}}
        <div class="form-group row">
            <div class="col-md-4">
                <label for="customer_id">{{trans('models.customer.name')}}</label>
                <div class="input-group {{empty($formAdvance) && auth()->user()->can('add customer') ? 'with-button-add' : ''}}">
                    <select class="select2 select-customer form-control" id="customer_id"
                            name="customer_id">
                        @foreach($customers as $customer)
                            @if ($customer->id== $entity->customer_id)
                                <option value="{{$customer->id}}" selected="selected"
                                        title="{{$customer->full_name}}">
                                    {{$customer->full_name}}</option>
                            @endif
                        @endforeach
                    </select>
                    @if(empty($formAdvance) && auth()->user()->can('add customer'))
                        <div class="input-group-append">
                            <button class="btn btn-third quick-add" type="button" data-model="customer"
                                    data-url="{{route('customer.advance')}}" data-callback="addCustomerComplete">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 {{empty($entity->customer_id) && empty($entity->customer_name) ? 'hide' : ''}}">
                {!! MyForm::label('customer_name', $entity->tA('customer_name'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
                    </div>
                    {!! MyForm::text('customer_name', $entity->customer_name, ['placeholder'=>$entity->tA('customer_name')]) !!}
                </div>
            </div>
            <div class="col-md-4 {{empty($entity->customer_id) && empty($entity->customer_name) ? 'hide' : ''}}">
                {!! MyForm::label('customer_mobile_no', trans('models.customer.attributes.mobile_no'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-phone" aria-hidden="true"></i></span>
                    </div>
                    {!! MyForm::text('customer_mobile_no', $entity->customer_mobile_no, ['placeholder'=>trans('models.customer.attributes.mobile_no')]) !!}
                </div>
            </div>
        </div>

        {{--Ngày đặt hàng--}}
        <div class="form-group row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-8">
                        {!! MyForm::label('order_date', $entity->tA('order_date'), [], false) !!}
                        {!! MyForm::text('order_date', (empty($entity->id) && empty($entity->order_date)) ? $today->format('d-m-Y') : format($entity->order_date, 'd-m-Y'),
                        ['placeholder'=>$entity->tA('order_date'), 'class' => 'datepicker date-input']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('is_merge_item', $entity->tA('is_merge_item'), [], false) !!}
                <input hidden="hidden" name="is_merge_item" id="is_merge_item"
                       value="{{ $entity->is_merge_item }}"/>
                <div>
                    {!! MyForm::checkbox('switchery_is_merge_item', $entity->is_merge_item, $entity->is_merge_item  == 1 ? true : false
                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_merge_item']) !!}
                    <span>Có</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{--Thông tin trạng thái đơn hàng --}}
<div class="card-header" role="tab" id="headingCustomer">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseCustomer"
           aria-expanded="true" aria-controls="collapseCustomer" class="collapse-expand">
            {{trans('models.order.attributes.customer_and_status')}}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseCustomer" class="collapse show" role="tabpanel">
    <div class="card-body m-l-24">
        {{--Độ ưu tiên--}}
        <div class="form-group row">
            <div class="col-md-12">
                <div class="row">
                    {!! MyForm::hidden('precedence',  empty($entity->precedence) ? config('constant.ORDER_PRECEDENCE_NORMAL') : $entity->precedence, ['id'=>'precedence']) !!}
                    <div class="col-md-3">
                        {!! MyForm::label('precedence', $entity->tA('precedence'), [], false) !!}
                    </div>
                    <div class="col-md-3">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio" id="order_precedence_special" value="3"
                                   name="order_precedence"
                                    {!!  ($entity->precedence == config('constant.ORDER_PRECEDENCE_SPECIAL') ? 'checked' : '') !!}>
                            <label for="order_precedence_special">
                                Đặc biệt (
                                <span class="fa fa-star text-warning"></span>
                                <span class="fa fa-star text-warning"></span>
                                <span class="fa fa-star text-warning"></span>
                                )
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio"
                                   id="order_precedence_normal"
                                   value="4"
                                   name="order_precedence" {!! empty($entity->id) ? 'checked': ($entity->precedence == config('constant.ORDER_PRECEDENCE_NORMAL') ? 'checked' : '') !!}>
                            <label for="order_precedence_normal">
                                Bình thường (
                                <span class="fa fa-star text-warning"></span>
                                <span class="fa fa-star text-warning"></span>
                                )
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="radio radio-info form-check-inline">
                            <input type="radio"
                                   id="order_precedence_low"
                                   value="5"
                                   name="order_precedence" {!! ($entity->precedence == config('constant.ORDER_PRECEDENCE_LOW') ? 'checked' : '') !!}>
                            <label for="order_precedence_low">
                                Thấp (
                                <span class="fa fa-star text-warning"></span>
                                )
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--Trạng thái đơn hàng--}}
        <div class="row">
            <div class="col-md-6">
                {!! MyForm::label('status', $entity->tA('status')) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-primary text-primary order-status-select">
                    {{ trans('common.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI') }}
                    <input type="radio" name="status"
                           value="8"
                            {!! empty($entity->id) || strpos($routeName, 'duplicate') ? 'checked' :
                            ($entity->status == config('constant.KHOI_TAO') && $entity->status_partner == config('constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-primary text-primary order-status-select">
                    {{ trans('common.PARTNER_CHO_XAC_NHAN') }}
                    <input type="radio" name="status"
                           value="9"
                            {!!$entity->status == config('constant.KHOI_TAO') && $entity->status_partner == config('constant.PARTNER_CHO_XAC_NHAN') ? 'checked' : '' !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-primary text-primary order-status-select">
                    {{ trans('common.PARTNER_YEU_CAU_SUA') }}
                    <input type="radio" name="status"
                           value="10"
                            {!! $entity->status == config('constant.KHOI_TAO') && $entity->status_partner == config('constant.PARTNER_YEU_CAU_SUA') ? 'checked' : '' !!}>
                    <span></span>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-secondary text-secondary order-status-select">
                    {{ trans('common.SAN_SANG') }}
                    <input type="radio" name="status"
                           value="{{config("constant.SAN_SANG")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.SAN_SANG') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-stpink text-stpink order-status-select">
                    {{ trans('common.TAI_XE_XAC_NHAN') }}
                    <input type="radio" name="status"
                           value="{{config("constant.TAI_XE_XAC_NHAN")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.TAI_XE_XAC_NHAN') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-brown text-brown order-status-select">
                    {{ trans('common.CHO_NHAN_HANG') }}
                    <input type="radio" name="status"
                           value="{{config("constant.CHO_NHAN_HANG")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.CHO_NHAN_HANG') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-blue text-blue order-status-select">
                    {{ trans('common.DANG_VAN_CHUYEN') }}
                    <input type="radio" name="status"
                           value="{{config("constant.DANG_VAN_CHUYEN")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.DANG_VAN_CHUYEN') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-success text-success order-status-select">
                    {{ trans('common.HOAN_THANH') }}
                    <input type="radio" name="status"
                           value="{{config("constant.HOAN_THANH")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.HOAN_THANH') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
            <div class="col-sm-4 col-xl-1">
                <label class="card-box border-dark text-dark order-status-select">
                    {{ trans('common.HUY') }}
                    <input type="radio" name="status" value="{{config("constant.HUY")}}"
                            {!! strpos($routeName, 'duplicate') ? '' : ($entity->status == config('constant.HUY') ? 'checked' : '') !!}>
                    <span></span>
                </label>
            </div>
        </div>
        @if(session()->has('status'))
            <div id="order_code-error"
                 class="help-block error-help-block">{{session()->get('status')}}</div>
        @endif

        <div class="form-group row">
            <div class="col-md-4">
                {!! MyForm::label('status_collected_documents', $entity->tA('status_collected_documents')) !!}
                {!! MyForm::dropDown('status_collected_documents', $entity->status_collected_documents, $collected_documents_status_list, false, [ 'class' => ' minimal']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('datetime_collected_documents', trans('models.order.attributes.datetime_collected_documents')) !!}
                <div class="row">
                    <div class="col-md-5">
                        {!! MyForm::text('time_collected_documents', $entity->time_collected_documents,
                        ['placeholder'=>$entity->tA('time_collected_documents'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! MyForm::text('date_collected_documents',$entity->getDateTime('date_collected_documents', 'd-m-Y'), ['placeholder'=>$entity->tA('date_collected_documents'),
                        'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {!! MyForm::label('is_collected_documents', $entity->tA('is_collected_documents'), [], false) !!}
                <input hidden="hidden" name="is_collected_documents" id="form_is_collected_documents"
                       value="{{ $entity->is_collected_documents }}"/>
                <div>
                    {!! MyForm::checkbox('switchery_is_collected_documents', $entity->is_collected_documents, $entity->is_collected_documents  == 1 ? true : false
                    , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_collected_documents']) !!}
                    <span> Bắt buộc</span>
                </div>
            </div>
            @if(session()->has('status_collected_documents'))
                <div id="order_code-error"
                     class="help-block error-help-block">{{session()->get('status_collected_documents')}}</div>
            @endif
        </div>
        <div class="form-group row Document_reality {{$isETAHide ? 'hide' : ''}}">
            <div class="col-md-4">
                {!! MyForm::label('num_of_document_page', $entity->tA('num_of_document_page')) !!}
                {!! MyForm::text('num_of_document_page', numberFormat($entity->num_of_document_page),
                    ['placeholder'=>$entity->tA('num_of_document_page'),'class' => 'number-input','id'=>'num_of_document_page']) !!}
            </div>
            <div class="col-md-4">
                {!! MyForm::label('datetime_collected_documents_reality', trans('models.order.attributes.datetime_collected_documents_reality')) !!}
                <div class="row">
                    <div class="col-md-5">
                        {!! MyForm::text('time_collected_documents_reality', $entity->time_collected_documents_reality,
                        ['placeholder'=>$entity->tA('time_collected_documents_reality'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! MyForm::text('date_collected_documents_reality',$entity->getDateTime('date_collected_documents_reality', 'd-m-Y'), ['placeholder'=>$entity->tA('date_collected_documents_reality'),
                        'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-5">
                        {!! MyForm::label('document_type', trans('models.order.attributes.document_type')) !!}
                        {!! MyForm::text('document_type', $entity->document_type, ['placeholder'=>$entity->tA('document_type')]) !!}
                    </div>
                    <div class="col-md-7">
                        {!! MyForm::label('document_note', trans('models.order.attributes.document_note')) !!}
                        {!! MyForm::text('document_note', $entity->document_note, ['placeholder'=>$entity->tA('document_note')]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
