<div class="form-info-wrap" data-id="{{$entity->id}}" id="customer_group_model" data-quicksave=''
     data-entity='customer-group'>
    @if($show_history)
        <div class="related-list">
            <span class="collapse-view dIB detailViewCollapse dvLeftPanel_show"
                  onclick="showHideDetailViewLeftPanel(this);" id="dv_leftPanel_showHide" style="">
                <span class="svgIcons dIB fCollapseIn"></span>
            </span>
            <ul class="list-related-list">
                <li>
                    <span class="title">Thông tin</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingInformation"
                               href="#">{{trans('models.order.attributes.information')}}</a></li>
                    </ul>
                </li>
                <li>
                    <span class="title">Thông tin liên quan</span>
                    <ul>
                        <li><a class="list-info" data-dest="headingDriver"
                               href="#">Thông tin khách hàng</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    @endif
    <div class="{{ $show_history ? "width-related-list" : "" }}">
        <ul class="list-group" style="width: 100%">
            <li class="list-group-item detail-info">
                <div class="row">
                    @if(isset($showAdvance))
                        <div class="toolbar-detail col-md-12">
                            @include('layouts.backend.elements.detail_to_action')
                        </div>
                    @endif
                    <div class="col-md-12 content-detail">
                        <div class="{{isset($showAdvance) ? 'first' : ''}} card-header" role="tab"
                             id="headingInformation">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                   aria-controls="collapseInformation" class="">
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name', 'isEditable'=>false])

                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab"
                             id="headingCustomer">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseCustomer" aria-expanded="true"
                                   aria-controls="collapseCustomer" class="">
                                    Thông tin khách hàng
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseCustomer" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 100% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 50px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-left">Khách hàng</th>
                                        <th scope="col" class="text-left">Số điện thoại</th>
                                        <th scope="col" class="text-left">Loại khách hàng</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($entity->customers))
                                        @foreach($entity->customers as $index=>$customer)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-left">
                                                    <a class="driver-detail" href="#"
                                                       data-show-url="{{isset($showAdvance) ? route('customer.show', $customer->id) : ''}}"
                                                       data-id="{{ isset($showAdvance) ? $customer->id : ''}} ">{{$customer->full_name}}</a>
                                                </td>
                                                <td class="text-left">
                                                    <span><i class="fa fa-phone"></i></span> {{$customer->mobile_no}}
                                                </td>
                                                <td class="text-left">
                                                    {{$customer->getCustomerType()}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">Không có dữ liệu</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>