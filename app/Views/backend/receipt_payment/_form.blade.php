<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['receipt-payment.valid', $entity->id]]) !!}

        <div class="row">
            <div class="col-md-12" id="customer_model">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="content content-body">
                        <div class="form-group">
                            @if ($type == 1)
                                {!! MyForm::label('name_thu', $entity->tA('name_thu') . ' <span
                                    class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('name', $entity->name, ['placeholder' => $entity->tA('name_thu')]) !!}
                            @else
                                {!! MyForm::label('name_chi', $entity->tA('name_chi') . ' <span
                                    class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('name', $entity->name, ['placeholder' => $entity->tA('name_chi')]) !!}
                            @endif
                        </div>
                        <div class="form-group">
                            {!! MyForm::label('is_display_driver', $entity->tA('is_display_driver'), [], false) !!}
                            <input hidden="hidden" name="is_display_driver" id="is_display_driver"
                                value="{{ $entity->is_display_driver }}"/>
                            <div>
                                {!! MyForm::checkbox('switchery_is_display_driver', $entity->is_display_driver, $entity->is_display_driver  == "1" ? true : false
                                , ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'switchery_is_display_driver']) !!}
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered table-hover table-cost">
                                <thead id="head_content">
                                    <tr class="active">
                                        <td class="header text-center">Chi phí mặc định (VND)
                                        </td>
                                        <td style="width: 80px" class="text-center"></td>
                                    </tr>
                                <tbody id="body_content">
                                    @if (isset($amount_list) && count($amount_list) > 0)
                                        @foreach ($amount_list as $index => $amount_item)
                                            <tr>
                                                <td>
                                                    <div class="input-group">
                                                        <input placeholder="Số tiền"
                                                            class="number-input form-control mapping"
                                                            name="{{ 'amount_list[' . $index . ']' }}" type="text"
                                                            id="{{ 'amount_list[' . $index . ']' }}" aria-invalid="false"
                                                            value={{ numberFormat($amount_item) }} />
                                                    </div>
                                                </td>
                                                <td class="text-center text-middle">
                                                    <a class="delete-cost" href="#" style="display:inline-block"
                                                        title="Xóa">
                                                        <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <input placeholder="Số tiền"
                                                        class="number-input form-control mapping" name="amount_list[0]"
                                                        type="text" id="amount_list[0]" aria-invalid="false"
                                                        value="0" />
                                                </div>
                                            </td>
                                            <td class="text-center text-middle">
                                                <a class="delete-cost" href="#" style="display:inline-block"
                                                    title="Xóa">
                                                    <i class="fa fa-trash" aria-hidden="true" title="Xóa"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <div>
                                                <button class="btn" id="btn-plus">
                                                    <i class="fa fa-plus" style="margin-right: 8px"></i>Thêm chi phí
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <input type="hidden" name="type" value="{{ $type }}" />
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>

@section('extra-scripts')
    {!! $validator !!}
@endsection
