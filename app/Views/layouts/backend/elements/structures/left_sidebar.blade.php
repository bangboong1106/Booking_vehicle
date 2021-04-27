<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                @if (\Auth::check() && \Auth::user()->role == 'admin')
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'board' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            <span> {{ trans('models.board.name') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('board.index') }}" class="waves-effect waves-primary">
                                    {{ trans('models.board.name') }}
                                </a>
                            </li>
                            @can('view dashboard')
                                <li>
                                    <a href="{{ route('order-board.index') }}" class="waves-effect waves-primary">
                                        {{ trans('models.order_board.name') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('order-customer-board.index') }}"
                                       class="waves-effect waves-primary">
                                        {{ trans('models.order_customer_board.name') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('route-board.index') }}" class="waves-effect waves-primary">
                                        {{ trans('models.route_board.name') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @canany(['view partner_dashboard'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'board' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            <span> {{ trans('models.board.name') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('partner-board.index') }}" class="waves-effect waves-primary">
                                    {{ trans('models.board.name') }}
                                </a>
                            </li>
                            @can('view partner_dashboard')
                                <li>
                                    <a href="{{ route('order-board.index') }}" class="waves-effect waves-primary">
                                        {{ trans('models.order_board.name') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('route-board.index') }}" class="waves-effect waves-primary">
                                        {{ trans('models.route_board.name') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['view order', 'view order_customer', 'view document', 'view route'])
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'order' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-barcode" aria-hidden="true"></i>
                            <span> {{ trans('common.order') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view order_customer')
                                <li {!! $controllerName=='order_customer' ? 'class="active"' : '' !!}><a
                                            href="{{ route('order-customer.index') }}"
                                            class="{!!  $controllerName == 'order_customer' ? 'active' : '' !!}">{{ trans('models.order_customer.name') }}</a>
                                </li>
                            @endcan
                            @can('view order')
                                <li {!! $controllerName=='order' ? 'class="active"' : '' !!}><a
                                            href="{{ route('order.index') }}"
                                            class="{!!  $controllerName == 'order' ? 'active' : '' !!}">{{ trans('models.order.name') }}</a>
                                </li>
                            @endcan
                            {{-- @can('view merge_order')
                                 <li {!! $controllerName=='merge_order' ? 'class="active"' : '' !!}><a
                                             href="{{ route('merge-order.index') }}"
                                             class="{!!  $controllerName == 'merge_order' ? 'active' : '' !!}">{{ trans('models.merge_order.name') }}</a>
                                 </li>
                             @endcan--}}
                            @can('view document')
                                <li {!! $controllerName=='document' ? 'class="active"' : '' !!}><a
                                            href="{{ route('document.index') }}"
                                            class="{!!  $controllerName == 'document' ? 'active' : '' !!}">{{ trans('models.document.name') }}</a>
                                </li>
                            @endcan
                            @can('view route')
                                <li {!! $controllerName=='route' ? 'class="active"' : '' !!}><a
                                            href="{{ route('route.index') }}"
                                            class="{!!  $controllerName == 'route' ? 'active' : '' !!}">{{ trans('models.route.name') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @endcanany
                @canany(['view report', 'view report_schedule'])
                @if(Auth::check() && Auth::user()->role == 'admin')
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'report' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-line-chart" aria-hidden="true"></i>
                            <span> {{ trans('common.report') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view report')
                                <li {!! $controllerName=='report' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report.index') }}"
                                            class="{!!  $controllerName == 'report' ? 'active' : '' !!}">{{ trans('models.report.attributes.operator_report') }}</a>
                                </li>
                            @endcan
                            @can('view report')
                                <li {!! $controllerName=='report_journey' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-journey.index') }}"
                                            class="{!!  $controllerName == 'report_journey' ? 'active' : '' !!}">{{ trans('models.report.attributes.journey_report') }}</a>
                                </li>
                            @endcan
                            @can('view report')
                                <li {!! $controllerName=='report_customer' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-customer.index') }}"
                                            class="{!!  $controllerName == 'report_customer' ? 'active' : '' !!}">{{ trans('models.report.attributes.customer_report') }}</a>
                                </li>
                            @endcan
                            @can('view report')
                                <li {!! $controllerName=='report_vehicle' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-vehicle.index') }}"
                                            class="{!!  $controllerName == 'report_vehicle' ? 'active' : '' !!}">{{ trans('models.report.attributes.vehicle_report') }}</a>
                                </li>
                            @endcan
                            @can('view report')
                                <li {!! $controllerName=='report_vehicle_team' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-vehicle-team.index') }}"
                                            class="{!!  $controllerName == 'report_vehicle_team' ? 'active' : '' !!}">{{ trans('models.report.attributes.vehicle_team_report') }}</a>
                                </li>
                            @endcan
                            <li class="separator">
                                <hr/>
                            </li>
                            @can('view report_schedule')
                                <li {!! $controllerName=='report_schedule' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-schedule.index') }}"
                                            class="{!!  $controllerName == 'report-schedule' ? 'active' : '' !!}">{{ trans('models.report_schedule.name') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @endcanany
                @canany(['view quota' , 'view price_quote', 'view payroll'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'quota' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-money" aria-hidden="true"></i>
                            <span> {{ trans('common.quota') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view quota')
                                <li {!! $controllerName=='quota' ? 'class="active"' : '' !!}><a
                                            href="{{ route('quota.index') }}"
                                            class="{!!  $controllerName == 'quota' ? 'active' : '' !!}">{{ trans('models.quota.name') }}</a>
                                </li>
                            @endcan
                            @can('view price_quote')
                                <li {!! $controllerName=='price_quote' ? 'class="active"' : '' !!}><a
                                            href="{{ route('price-quote.index') }}"
                                            class="{!!  $controllerName == 'price_quote' ? 'active' : '' !!}">{{ trans('models.price_quote.name') }}</a>
                                </li>
                            @endcan
                            @can('view payroll')
                                <li {!! $controllerName=='payroll' ? 'class="active"' : '' !!}><a
                                            href="{{ route('payroll.index') }}"
                                            class="{!!  $controllerName == 'payroll' ? 'active' : '' !!}">{{ trans('models.payroll.name') }}</a>
                                </li>
                            @endcan
                            @can('view order_price')
                                <hr/>
                                <li {!! $controllerName=='order_price' ? 'class="active"' : '' !!}><a
                                            href="{{ route('order-price.index') }}"
                                            class="{!!  $controllerName == 'order_price' ? 'active' : '' !!}">{{ trans('models.order_price.name') }}</a>
                                    @endcan
                                </li>
                        </ul>
                    </li>
                @endcanany
                @canany(['view customer', 'view contact', 'view contract', 'view customer_group'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'customer' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-user-circle" aria-hidden="true"></i>
                            <span> {{ trans('common.customer') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view customer')
                                <li {!! $controllerName=='customer' ? 'class="active"' : '' !!}><a
                                            href="{{ route('customer.index') }}"
                                            class="{!!  $controllerName == 'customer' ? 'active' : '' !!}">{{ trans('models.customer.name') }}</a>
                                </li>
                            @endcan
                                @can('view client')
                                    <li {!! $controllerName=='client' ? 'class="active"' : '' !!}><a
                                                href="{{ route('client.index') }}"
                                                class="{!!  $controllerName == 'client' ? 'active' : '' !!}">{{ trans('models.client.name') }}</a>
                                    </li>
                                @endcan
                            @can('view customer_default_data')
                                <li {!! $controllerName=='customer_default_data' ? 'class="active"' : '' !!}><a
                                            href="{{ route('customer-default-data.index') }}"
                                            class="{!!  $controllerName == 'customer_default_data' ? 'active' : '' !!}">{{ trans('models.customer_default_data.name') }}</a>
                                </li>
                            @endcan
                          {{--  @can('view customer_group')
                                <li {!! $controllerName=='customer_group' ? 'class="active"' : '' !!}><a
                                            href="{{ route('customer-group.index') }}"
                                            class="{!!  $controllerName == 'customer_group' ? 'active' : '' !!}">{{ trans('models.customer_group.name') }}</a>
                                </li>
                            @endcan--}}
                            <li class="separator">
                                <hr/>
                            </li>
                            @can('view contract')
                                <li {!! $controllerName=='contract' ? 'class="active"' : '' !!}><a
                                            href="{{ route('contract.index') }}"
                                            class="{!!  $controllerName == 'contract' ? 'active' : '' !!}">{{ trans('models.contract.name') }}</a>
                                </li>
                            @endcan
                            @can('view contact')
                                <li {!! $controllerName=='contact' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('contact.index') }}"
                                       class="{!!  $controllerName == 'contact' ? 'active' : '' !!}">{{ trans('models.contact.name') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['view driver', 'view vehicle_team'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'driver' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-id-card" aria-hidden="true"></i>
                            <span> {{ trans('common.driver') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view driver')
                                <li {!! $controllerName=='driver' ? 'class="active"' : '' !!}><a
                                            href="{{ route('driver.index') }}"
                                            class="{!!  $controllerName == 'driver' ? 'active' : '' !!}">{{ trans('models.driver.name') }}</a>
                                </li>
                            @endcan
                            @can('view vehicle_team')
                                <li {!! $controllerName=='vehicle_team' ? 'class="active"' : '' !!}><a
                                            href="{{ route('vehicle-team.index') }}"
                                            class="{!!  $controllerName == 'vehicle_team' ? 'active' : '' !!}">{{ trans('models.vehicle_team.name') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['view vehicle', 'view vehicle_group', 'view accessory', 'view repair_ticket'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'vehicle' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-truck" aria-hidden="true"></i>
                            <span> {{ trans('common.vehicle') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view vehicle')
                                <li {!! $controllerName=='vehicle' ? 'class="active"' : '' !!}><a
                                            href="{{ route('vehicle.index') }}"
                                            class="{!!  $controllerName == 'vehicle' ? 'active' : '' !!}">{{ trans('models.vehicle.name') }}</a>
                                </li>
                            @endcan
                            @can('view vehicle_group')
                                <li {!! $controllerName=='vehicle_group' ? 'class="active"' : '' !!}><a
                                            href="{{ route('vehicle-group.index') }}"
                                            class="{!!  $controllerName == 'vehicle_group' ? 'active' : '' !!}">{{ trans('models.vehicle_group.name') }}</a>
                            @endcan
                            @can('view accessory')
                                <li {!! $controllerName=='accessory' ? 'class="active"' : '' !!}><a
                                            href="{{ route('accessory.index') }}"
                                            class="{!!  $controllerName == 'accessory' ? 'active' : '' !!}">{{ trans('models.accessory.name') }}</a>
                            @endcan
                            @can('view repair_ticket')
                                <li {!! $controllerName=='repair_ticket' ? 'class="active"' : '' !!}><a
                                            href="{{ route('repair-ticket.index') }}"
                                            class="{!!  $controllerName == 'repair_ticket' ? 'active' : '' !!}">{{ trans('models.repair_ticket.name') }}</a>
                            @endcan
                            <li {!! $controllerName=='journey' ? 'class="active"' : '' !!}>
                                <a href="{{ route('journey.index') }}"
                                   class="{!!  $controllerName == 'journey' ? 'active' : '' !!}">
                                    {{ trans('models.journey.attributes.map') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany

                @canany(['view admin', 'view role', 'view template', 'view template_payment', 'view import_history',
                'view activity_log', 'view partner'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'management' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            <span> {{ trans('common.management') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view admin')
                                <li {!! $controllerName=='admin' ? 'class="active"' : '' !!}><a
                                            href="{{ route('admin.index') }}"
                                            class="{!!  $controllerName == 'admin' ? 'active' : '' !!}">{{ trans('models.admin.name') }}</a>
                                </li>
                            @endcan
                            @can('view partner')
                                <li {!! $controllerName=='partner' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('partner.index') }}"
                                       class="{!!  $controllerName == 'partner' ? 'active' : '' !!}">{{ trans('models.partner.name') }}</a>
                                </li>
                            @endcan
                            @can('view role')
                                <li {!! $controllerName=='role' ? 'class="active"' : '' !!}><a
                                            href="{{ route('role.index') }}"
                                            class="{!!  $controllerName == 'role' ? 'active' : '' !!}">{{ trans('models.role.name') }}</a>
                                </li>
                            @endcan
                            {{-- @can('view client_role')
                                <li {!! $controllerName=='role' ? 'class="active"' : '' !!}><a
                                            href="{{ route('customer-role.index') }}"
                                            class="{!!  $controllerName == 'customer_role' ? 'active' : '' !!}">{{ trans('models.customer_role.name_in_admin') }}</a>
                                </li>
                            @endcan --}}
                            @can('view template')
                                <li {!! $controllerName=='template' ? 'class="active"' : '' !!}><a
                                            href="{{ route('template.index') }}"
                                            class="{!!  $controllerName == 'template' ? 'active' : '' !!}">{{ trans('models.template.name') }}</a>
                                </li>
                            @endcan
                            @can('view template_payment')
                                <li {!! $controllerName=='template_payment' ? 'class="active"' : '' !!}><a
                                            href="{{ route('template-payment.index') }}"
                                            class="{!!  $controllerName == 'template_payment' ? 'active' : '' !!}">{{ trans('models.template_payment.name') }}</a>
                                </li>
                            @endcan
                            @can('view template_excel_converter')
                                <li {!! $controllerName=='template_excel_converter' ? 'class="active"' : '' !!}><a
                                            href="{{ route('template-excel-converter.index') }}"
                                            class="{!!  $controllerName == 'template_excel_converter' ? 'active' : '' !!}">{{ trans('models.template_excel_converter.name') }}</a>
                                </li>
                            @endcan
                            @can('view import_history')
                                <li {!! $controllerName=='import_history' ? 'class="active"' : '' !!}><a
                                            href="{{ route('import-history.index') }}"
                                            class="{!!  $controllerName == 'import_history' ? 'active' : '' !!}">{{ trans('models.import_history.name') }}</a>
                                </li>
                            @endcan
                            @can('view activity_log')
                                <li {!! $controllerName=='activity_log' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('activity-log.index') }}"
                                       class="{!!  $controllerName == 'activity_log' ? 'active' : '' !!}">
                                        {{ trans('models.activity_log.name') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['view receipt_payment', 'view contract_type', 'view goods_type',
                'view goods_unit', 'view currency', 'view location_type', 'view location', 'view location_group', 'view goods_group'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'category' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                            <span> {{ trans('common.category') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view location')
                                <li {!! $controllerName=='location' ? 'class="active"' : '' !!}><a
                                            href="{{ route('location.index') }}"
                                            class="{!!  $controllerName == 'location' ? 'active' : '' !!}">{{ trans('models.location.name') }}</a>
                                </li>
                            @endcan
                            @can('view location_type')
                                <li {!! $controllerName=='location_type' ? 'class="active"' : '' !!}><a
                                            href="{{ route('location-type.index') }}"
                                            class="{!!  $controllerName == 'location_type' ? 'active' : '' !!}">{{ trans('models.location_type.name') }}</a>
                                </li>
                            @endcan
                            @can('view location_group')
                                <li {!! $controllerName=='location_group' ? 'class="active"' : '' !!}><a
                                            href="{{ route('location-group.index') }}"
                                            class="{!!  $controllerName == 'location' ? 'active' : '' !!}">{{ trans('models.location_group.name') }}</a>
                                </li>
                            @endcan

                            <hr/>

                            @can('view goods_type')
                                <li {!! $controllerName=='goods_type' ? 'class="active"' : '' !!}><a
                                            href="{{ route('goods-type.index') }}"
                                            class="{!!  $controllerName == 'goods_type' ? 'active' : '' !!}">{{ trans('models.goods_type.name') }}</a>
                                </li>
                            @endcan
                            @can('view goods_group')
                                <li {!! $controllerName=='goods_group' ? 'class="active"' : '' !!}><a
                                            href="{{ route('goods-group.index') }}"
                                            class="{!!  $controllerName == 'goods_group' ? 'active' : '' !!}">{{ trans('models.goods_group.name') }}</a>
                                </li>
                            @endcan
                            @can('view receipt_payment')
                                <li {!! $controllerName=='receipt_payment' ? 'class="active"' : '' !!}><a
                                            href="{{ route('receipt-payment.index') }}"
                                            class="{!!  $controllerName == 'receipt_payment' ? 'active' : '' !!}">{{ trans('models.receipt_payment.name') }}</a>
                                </li>
                            @endcan
                            @can('view currency')
                                <li {!! $controllerName=='currency' ? 'class="active"' : '' !!}><a
                                            href="{{ route('currency.index') }}"
                                            class="{!!  $controllerName == 'currency' ? 'active' : '' !!}">{{ trans('models.currency.name') }}</a>
                                </li>
                            @endcan
                            @can('view contract_type')
                                <li {!! $controllerName=='contract-type' ? 'class="active"' : '' !!}><a
                                            href="{{ route('contract-type.index') }}"
                                            class="{!!  $controllerName == 'contract-type' ? 'active' : '' !!}">{{ trans('models.contract_type.name') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @canany(['view alert_log', 'view driver_config_file', 'view vehicle_config_file', 'view
                vehicle_config_specification', 'view system_code_config'])
                    <li class="has_sub ms-hover">
                        <a href="javascript:void(0);"
                           class="waves-effect waves-primary {!!  $menu == 'setting' ? 'active subdrop' : '' !!}">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            <span> {{ trans('common.setting') }} </span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('view system_config')
                                <li {!! $controllerName=='company_info' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('company-info.index') }}"
                                       class="{!!  $controllerName == 'company_info' ? 'active' : '' !!}">{{ trans('models.company_info.name') }}</a>
                                </li>
                                <li {!! $controllerName=='system_config' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('system-config.index') }}"
                                       class="{!!  $controllerName == 'system_config' ? 'active' : '' !!}">{{ trans('models.system_config.name') }}</a>
                                </li>
                            @endcan
                            @can('view system_code_config')
                                <li {!! $controllerName=='system_code_config' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('system-code-config.index') }}"
                                       class="{!!  $controllerName == 'system_code_config' ? 'active' : '' !!}">{{ trans('models.system_code_config.name') }}</a>
                                </li>
                            @endcan
                            <li class="separator">
                                <hr/>
                            </li>
                            @can('view driver_config_file')
                                <li {!! $controllerName=='driver_config_file' ? 'class="active"' : '' !!}><a
                                            href="{{ route('driver-config-file.index') }}"
                                            class="{!!  $controllerName == 'driver_config_file' ? 'active' : '' !!}">{{ trans('models.driver_config_file.name') }}</a>
                                </li>
                            @endcan
                            @can('view vehicle_config_file')
                                <li {!! $controllerName=='vehicle_config_file' ? 'class="active"' : '' !!}><a
                                            href="{{ route('vehicle-config-file.index') }}"
                                            class="{!!  $controllerName == 'vehicle_config_file' ? 'active' : '' !!}">{{ trans('models.vehicle_config_file.name') }}</a>
                                </li>
                            @endcan
                            @can('view vehicle_config_specification')
                                <li {!! $controllerName=='vehicle_config_specification' ? 'class="active"' : '' !!}>
                                    <a href="{{ route('vehicle-config-specification.index') }}"
                                       class="{!!  $controllerName == 'vehicle_config_specification' ? 'active' : '' !!}">{{ trans('models.vehicle_config_specification.name') }}</a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @if(Auth::check() && Auth::user()->role == 'partner')
                    @canany(['view partner_order', 'view route', 'view document'])
                        <li class="has_sub ms-hover">
                            <a href="javascript:void(0);"
                               class="waves-effect waves-primary {!!  $menu == 'partner_order' ? 'active subdrop' : '' !!}">
                                <i class="fa fa-barcode" aria-hidden="true"></i>
                                <span> {{ trans('common.order') }} </span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('view partner_order')
                                    <li {!! $controllerName=='partner_order' ? 'class="active"' : '' !!}>
                                        <a href="{{ route('partner-order.index') }}"
                                           class="{!!  $controllerName == 'partner_order' ? 'active' : '' !!}">{{ trans('models.partner_order.name') }}</a>
                                    </li>
                                @endcan
                                @can('view route')
                                    <li {!! $controllerName=='route' ? 'class="active"' : '' !!}><a
                                                href="{{ route('route.index') }}"
                                                class="{!!  $controllerName == 'route' ? 'active' : '' !!}">{{ trans('models.route.name') }}</a>
                                    </li>
                                @endcan
                                @can('view document')
                                    <li {!! $controllerName=='document' ? 'class="active"' : '' !!}><a
                                                href="{{ route('document.index') }}"
                                                class="{!!  $controllerName == 'document' ? 'active' : '' !!}">{{ trans('models.document.name') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(['view report', 'view report_schedule'])
                    @if(Auth::check() && Auth::user()->role == 'partner')
                        <li class="has_sub ms-hover">
                            <a href="javascript:void(0);"
                            class="waves-effect waves-primary {!!  $menu == 'report' ? 'active subdrop' : '' !!}">
                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                                <span> {{ trans('common.report') }} </span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('view report')
                                    <li {!! $controllerName=='report' ? 'class="active"' : '' !!}><a
                                                href="{{ route('report.index') }}"
                                                class="{!!  $controllerName == 'report' ? 'active' : '' !!}">{{ trans('models.report.attributes.operator_report') }}</a>
                                    </li>
                                @endcan
                                @can('view report')
                                    <li {!! $controllerName=='report_journey' ? 'class="active"' : '' !!}><a
                                                href="{{ route('report-journey.index') }}"
                                                class="{!!  $controllerName == 'report_journey' ? 'active' : '' !!}">{{ trans('models.report.attributes.journey_report') }}</a>
                                    </li>
                                @endcan
                                @can('view report')
                                    <li {!! $controllerName=='report_vehicle' ? 'class="active"' : '' !!}><a
                                                href="{{ route('report-vehicle.index') }}"
                                                class="{!!  $controllerName == 'report_vehicle' ? 'active' : '' !!}">{{ trans('models.report.attributes.vehicle_report') }}</a>
                                    </li>
                                @endcan
                                @can('view report')
                                    <li {!! $controllerName=='report_vehicle_team' ? 'class="active"' : '' !!}><a
                                                href="{{ route('report-vehicle-team.index') }}"
                                                class="{!!  $controllerName == 'report_vehicle_team' ? 'active' : '' !!}">{{ trans('models.report.attributes.vehicle_team_report') }}</a>
                                    </li>
                                @endcan

                                <hr/>
                                @can('view report_schedule')
                                <li {!! $controllerName=='report_schedule' ? 'class="active"' : '' !!}><a
                                            href="{{ route('report-schedule.index') }}"
                                            class="{!!  $controllerName == 'report-schedule' ? 'active' : '' !!}">{{ trans('models.report_schedule.name') }}</a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                    @endif
                    @endcanany

                    @canany(['view partner_vehicle_team', 'view partner_driver'])
                        <li class="has_sub ms-hover">
                            <a href="javascript:void(0);"
                               class="waves-effect waves-primary {!!  $menu == 'partner_driver' ? 'active subdrop' : '' !!}">
                                <i class="fa fa-id-card" aria-hidden="true"></i>
                                <span> {{ trans('common.driver') }} </span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('view partner_driver')
                                    <li {!! $controllerName=='partner_driver' ? 'class="active"' : '' !!}><a
                                                href="{{ route('partner-driver.index') }}"
                                                class="{!!  $controllerName == 'partner_driver' ? 'active' : '' !!}">{{ trans('models.partner_driver.name') }}</a>
                                    </li>
                                @endcan
                                @can('view partner_vehicle_team')
                                    <li {!! $controllerName=='partner_vehicle_team' ? 'class="active"' : '' !!}><a
                                                href="{{ route('partner-vehicle-team.index') }}"
                                                class="{!!  $controllerName == 'partner_vehicle_team' ? 'active' : '' !!}">{{ trans('models.partner_vehicle_team.name') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(['view partner_vehicle','view partner_vehicle_group'])
                        <li class="has_sub ms-hover">
                            <a href="javascript:void(0);"
                               class="waves-effect waves-primary {!!  $menu == 'partner_vehicle' ? 'active subdrop' : '' !!}">
                                <i class="fa fa-truck" aria-hidden="true"></i>
                                <span> {{ trans('common.vehicle') }} </span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('view partner_vehicle')
                                    <li {!! $controllerName=='partner_vehicle' ? 'class="active"' : '' !!}>
                                        <a href="{{ route('partner-vehicle.index') }}"
                                           class="{!!  $controllerName == 'partner_vehicle' ? 'active' : '' !!}">{{ trans('models.partner_vehicle.name') }}</a>
                                    </li>
                                @endcan
                                @can('view partner_vehicle_group')
                                    <li {!! $controllerName=='partner_vehicle_group' ? 'class="active"' : '' !!}><a
                                                href="{{ route('partner-vehicle-group.index') }}"
                                                class="{!!  $controllerName == 'partner_vehicle_group' ? 'active' : '' !!}">{{ trans('models.vehicle_group.name') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(['view partner_admin'])
                        <li class="has_sub ms-hover">
                            <a href="javascript:void(0);"
                               class="waves-effect waves-primary {!!  $menu == 'partner_admin' ? 'active subdrop' : '' !!}">
                                <i class="fa fa-users" aria-hidden="true"></i>
                                <span> {{ trans('common.management') }} </span><span class="menu-arrow"></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('view partner_admin')
                                    <li {!! $controllerName=='partner_admin' ? 'class="active"' : '' !!}><a
                                                href="{{ route('partner-admin.index') }}"
                                                class="{!!  $controllerName == 'partner-admin' ? 'active' : '' !!}">{{ trans('models.partner_admin.name') }}</a>
                                    </li>
                                @endcan
                                @can('view partner_template')
                                <li {!! $controllerName=='partner_template' ? 'class="active"' : '' !!}><a
                                            href="{{ route('partner-template.index') }}"
                                            class="{!!  $controllerName == 'partner_template' ? 'active' : '' !!}">{{ trans('models.partner_template.name') }}</a>
                                </li>
                            @endcan
                            </ul>
                        </li>
                    @endcanany
                @endif
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
