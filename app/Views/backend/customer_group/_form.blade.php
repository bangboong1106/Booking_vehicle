<?php
$selectedCustomers = isset($customers) ? $customers : [];
?>
<script>
    var urlCustomer = '{{route('customer.combo-customer')}}?all=1',
        backendUri = '{{getBackendDomain()}}';
    var obj = JSON.parse('{{ $selectedCustomers == null ? '[]' : json_encode($selectedCustomers) }}');
    var searchCustomerExceptIds = [];
    for (var prop in obj) {
        searchCustomerExceptIds.push(prop);
    }
</script>
<div class="row">
    <div class="col-12 offset-3">
        {!! MyForm::model($entity, ['route' => ['customer-group.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-6">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="form-group">
                        {!! MyForm::label('code', $entity->tA('code') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('code', $entity->id != null ? $entity->code : $code , ['placeholder'=>$entity->tA('code')]) !!}
                    </div>
                    <div class="form-group">
                        {!! MyForm::label('name', $entity->tA('name') . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name')]) !!}
                    </div>
                    <div class="form-group">
                        <div class="advanced form-group row">
                            <div class="col-md-12">
                                {!! MyForm::label('customer_ids', $entity->tA('customer_ids')) !!}
                                <div class="input-group select2-bootstrap-prepend">
                                    <select class="select2 select-customer" id="customer_ids"
                                            name="customer_ids[]" multiple='multiple'>
                                        @foreach($entity->customers as $customer)
                                            <option value="{{$customer->id}}" selected="selected"
                                                    title="{{$customer->full_name}}">{{$customer->full_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-addon customer-search" data-type="multiple">
                                          <div class="input-group-text bg-transparent">
                                              <i class="fa fa-id-card {{ empty($entity->id) ? 'pointer' : ''  }}"
                                                 id="customer-search"></i>
                                          </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
<?php
$jsFiles = [
    'autoload/object-select2'
];
?>
{!! loadFiles($jsFiles, $area, 'js') !!}
@include('layouts.backend.elements.search._customer_search',
 ['modal' => 'customer_modal',
 'table'=>'table_customer',
 'button'=> 'btn-customer'])

