<div class="form-info-wrap" data-id="{{$entity->id}}" id="vehicle_team_model" data-quicksave=''
     data-entity='vehicle-team'>
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
                               href="#"> {{trans('models.role.permissions.title')}}</a></li>
                        <li><a class="list-info" data-dest="headingVehicle"
                               href="#"> {{$entity->tA('users')}}</a></li>
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
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'title', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'name', 'isEditable'=>false])

                                </div>
                            </div>
                        </div>
                        <div class="card-header" role="tab"
                             id="headingDriver">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                   aria-controls="collapseInformation" class="">
                                    {{trans('models.role.permissions.title')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body table-responsive">
                                <table id="show-table" class="table">
                                    <thead>
                                    <tr>
                                        <th>
                                            <button type="button" class="btn btn-primary" id="btn-toggle-collapse" data-toggle="tooltip" data-placement="top" title="Mở rộng / Thu gọn"></button>
                                        </th>
                                        <th>{{ trans('models.role.permissions.name') }}</th>
                                        <th>{{ trans('models.role.permissions.view') }}</th>
                                        <th>{{ trans('models.role.permissions.add') }}</th>
                                        <th>{{ trans('models.role.permissions.edit') }}</th>
                                        <th>{{ trans('models.role.permissions.delete') }}</th>
                                        <th>{{ trans('models.role.permissions.import') }}</th>
                                        <th>{{ trans('models.role.permissions.export') }}</th>
                                        <th>{{ trans('models.role.permissions.lock') }}</th>
                                        <th>{{ trans('models.role.permissions.unlock') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($groups as $group)
                                        <tr>
                                            <th colspan="10" data-toggle="collapse" data-target="#{{$group}}" aria-controls="{{$group}}" class="accordion-toggle panel-title">{{trans('common.'.$group)}}</th>
                                        </tr>
                                        @if (isset($permissions[$group]))
                                            <tr>
                                                <td colspan="10"  class="p-0 border-top-0">
                                            @foreach($permissions[$group] as $name => $permissionGroup)
                                                    <div class="accordion-body collapse show" id="{{$group}}">
                                                        <table class="w-100">
                                                            <tr class="no-border">
                                                                <th></th>
                                                                <th class="label-table">{{ trans('models.'.explode(' ', $name)[1].'.name') }}</th>
                                                                @foreach($permissionOrder as $order)
                                                                    @if(isset($permissionGroup[$order]))
                                                                        <td class="text-center">
                                                                            @if($entity->permissionList && in_array($permissionGroup[$order]->name, $entity->permissionList))
                                                                                <span class="text-success font-18"><i
                                                                                            class="fa fa-check-circle-o"></i></span>
                                                                            @else
                                                                                <span class="text-danger font-18"><i
                                                                                            class="fa fa-times-circle-o"></i></span>
                                                                            @endif
                                                                        </td>
                                                                    @else
                                                                        <td></td>
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        </table>
                                                    </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="card-header" role="tab"
                             id="headingVehicle">
                            <h5 class="mb-0 mt-0 font-16">
                                <a data-toggle="collapse" href="#collapseInformation" aria-expanded="true"
                                   aria-controls="collapseInformation" class="">
                                    {{$entity->tA('users')}}
                                    <i class="fa"></i>
                                </a>
                            </h5>
                        </div>
                        <div id="collapseInformation" class="collapse show" role="tabpanel"
                             aria-labelledby="headingOne"
                             style="">
                            <div class="card-body">
                                <table class="table table-bordered table-hover" style="width: 50% !important;">
                                    <thead id="head_content">
                                    <tr class="active">
                                        <th scope="col" style="width: 100px" class="text-center">
                                            STT
                                        </th>
                                        <th scope="col" class="text-center">Tên đăng nhập</th>
                                    </tr>
                                    </thead>
                                    <tbody id="body_content">
                                    @if(isset($userList))
                                        @foreach($userList as $index=>$user)
                                            <tr>
                                                <td class="text-center">
                                                    {{$index +1}}
                                                </td>
                                                <td class="text-center">
                                                    <a class="admin-detail" href="#"
                                                       data-show-url="{{isset($showAdvance) ? route('admin.show', $user->id) :''}}"
                                                       data-id="{{isset($showAdvance) ?  $user->id : ''}}">{{$user->username}}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

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
                        <div id="collapseSystem" class="collapse show"
                             role="tabpanel" aria-labelledby="note_info">
                            <div class="card-body">
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_id', 'value'=> isset($entity->insUser) ? $entity->insUser->username : '-', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'ins_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                                <div class="form-group row">
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_id', 'value'=> isset($entity->updUser) ? $entity->updUser->username : '-', 'isEditable' => false])
                                    @include('layouts.backend.elements.detail_to_edit',['property' => 'upd_date', 'isEditable' => false, 'controlType'=>'datetime'])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<script !src="">
    $('.accordion-body').on('hide.bs.collapse', function () {

        $('#btn-toggle-collapse').addClass('collapsed');

        let total = $('.accordion-toggle').length;

        let accordionCollapsed = ++$('.accordion-toggle.panel-title.collapsed').length;

        if (accordionCollapsed == total) {
            $('#btn-toggle-collapse').addClass('collapsed');
        }
    });

    $('.accordion-body').on('show.bs.collapse', function () {

        let total = $('.accordion-toggle').length;

        let accordionCollapsed = $('.accordion-toggle.panel-title.collapsed').length;

        if (accordionCollapsed == total) {
            $('#btn-toggle-collapse').addClass('collapsed');
        }

        if (accordionCollapsed == 0) {
            $('#btn-toggle-collapse').removeClass('collapsed');
        }

    });

    $('#btn-toggle-collapse').on('click', function () {
        let t = $(this);

        if (t.hasClass('collapsed')) {
            $('.accordion-body').collapse('show');
        } else {
            $('.accordion-body').collapse('hide');
        }
    });

    $('#btn-toggle-collapse').tooltip();
</script>