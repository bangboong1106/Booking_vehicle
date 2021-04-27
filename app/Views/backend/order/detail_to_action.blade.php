@php
    $edit_route = isset($edit_route) ? $edit_route : $routePrefix.'.edit';
    $duplicate_route = isset($duplicate_route) ? $duplicate_route : $routePrefix.'.duplicate';
    $deleteRoute = isset($deleteRoute) ? $deleteRoute : $routePrefix.'.destroy';
    $permission = str_replace('-', '_', $routePrefix);
@endphp
<div class="row" style="text-align: right;
    position: absolute;
    right: 12px;">
    <div class="toolbar btn-group" role="group" aria-label="Basic example">
        @can('export '. $permission)
            @if(!empty($entity->bill_print_url))
                <button id="btn-print-bill" class="btn-print-bill" data-type="single"
                        style="border-color: #c5c5c5;
                        border-width: 1px; background: white; color: white; width: 180px; border-radius: 4px; border: 1px solid #d8d8d8;height: 36px;  margin: 0;">
                    <a href="#"><i class="fa fa-print"
                                   style="margin-right: 8px;"></i> In vận đơn giao
                    </a>
                </button>
            @endif
        @endcan
        @can('edit '. $permission)
            @if($entity->is_lock == 0 && $entity->status_collected_documents != 2)
                <button id="btn-update-documents"
                        style="background: white; color: white; width: 180px; border-radius: 4px; border: 1px solid #d8d8d8;height: 36px;  margin: 0 8px;">
                    <a href="#"><i class="fa fa-toggle-up"
                                   style="margin-right: 8px;"></i> Đã thu đủ chứng từ
                    </a>
                </button>
            @endif
            @if($entity->is_lock == 0)
                <button style="background: white; color: white; width: 80px; border-radius: 4px; border: 1px solid #d8d8d8;height: 36px">
                    <a href="{{backUrl($edit_route, ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null,'shown'=>isset($showAdvance) ? true : false])}}"
                    data-toggle="tooltip" data-placement="top" title="" data-original-title="{{trans('actions.edit')}}">
                        <i class="fa fa-pencil" style="margin-right: 8px;"></i>Sửa
                    </a>
                </button>
            @endif
        @endcan
    </div>
    <div class="toolbar dropdown action" style="    background: white;
    width: 48px;
    margin: 0 8px;
    height: 36px;">
        <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false">
            ...
        </a>

        <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuLink">
            <div>
                <a class="print-action" href="#" style="padding: 6px;">
                    <i class="fa fa-print" aria-hidden="true"></i>In
                </a>
            </div>
            @can('export '. $permission)
                <div>
                    <a class="print-custom-action" href="#" style="padding: 6px;"
                       data-url="{{route('template.printCustom')}}" data-type="{{config('constant.ORDER')}}">
                        <i class="fa fa-print print-custom" aria-hidden="true"></i>In mẫu
                    </a>
                </div>
            @endcan
            @can('delete '. $permission)
                @if($entity->is_lock == 0)
                    <div>
                        <a class="delete-action" href="#del-confirm" data-toggle="modal"
                            data-placement="top" data-original-title="{{trans('actions.destroy')}}"
                            data-action="{{backUrl($deleteRoute,['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>Xóa
                        </a>
                    </div>
                @endif
            @endcan
            <div>
                <a target="_blank" href="{{getenv('HELP_DOMAIN','').trans('helps.'.$routeName)}}" data-toggle="tooltip"
                   data-placement="top" title="" data-original-title="{{trans('actions.help')}}" style="padding: 6px;">
                    <i class="fa fa-question-circle"></i>Hướng dẫn
                </a>
            </div>

        </div>
    </div>
    @if(isset($showAdvance))

        <script>
            $(document).ready(function () {
                //delete
                $('.delete-action').on('click', function () {
                    deleteItem($(this));
                });
            });

            function deleteItem(item) {
                var href = item.data('action'),
                    name = item.parents('tr').children('td[data-name=true]'),
                    spanDelete = $('#del-confirm .modal-body span');
                if (typeof name !== "undefined") {
                    spanDelete.html('');
                    spanDelete.append(name.map(function (index, value) {
                        return $(value).text().trim();
                    }).get().join('-'));
                }
                $('#del_form').attr('action', href);
            }
        </script>

    @endif
</div>