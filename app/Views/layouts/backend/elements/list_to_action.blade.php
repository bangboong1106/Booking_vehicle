@php
    $edit_route = isset($edit_route) ? $edit_route : $routePrefix.'.edit';
    $duplicate_route = isset($duplicate_route) ? $duplicate_route : $routePrefix.'.duplicate';
    $deleteRoute = isset($deleteRoute) ? $deleteRoute : $routePrefix.'.destroy';
    $permission = str_replace('-', '_', $routePrefix);
@endphp
<td class="text-center text-middle list-action">
    @can('edit ' . $permission)
        @if (!isset($entity->is_lock) || (isset($entity->is_lock) && $entity->is_lock == 0))
            {{--  @if (isset($routePrefix) && ($routePrefix != 'order' || ($routePrefix == 'order' && $entity->status_partner == config('constant.PARTNER_YEU_CAU_SUA'))))  --}}
            <a href="{{ route($edit_route, $entity->id) . '?' . (isset($backUrlKey) ? 'backUrlKey=' . $backUrlKey : '') . '&shown=' . (isset($showAdvance) ? true : false) }}"
                data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ trans('actions.edit') }}">
                <i class="fa fa-pencil"></i>
            </a>
        @endif
    @endcan
    @can('add ' . $permission)
        @if (isset($routePrefix) && $routePrefix != 'order')
            <a href="{{ route($duplicate_route, $entity->id) . '?' . (isset($backUrlKey) ? 'backUrlKey=' . $backUrlKey : '') . '&shown=' . (isset($showAdvance) ? true : false) }}"
                data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ trans('actions.duplicate') }}">
                <i class="fa fa-copy" aria-hidden="true"></i>
            </a>
        @endif
    @endcan
    @can('delete ' . $permission)
        @if (!isset($entity->is_lock) || (isset($entity->is_lock) && $entity->is_lock == 0))
            @if (isset($entity->haveStaff) && $entity->haveStaff()->exists())
            <a class="delete-action" href="#del-confirm" data-toggle="modal" data-placement="top"
                    data-original-title="{{ trans('actions.destroy') }}"
                    data-action="{{ route($deleteRoute, $entity->id) . '?' . (isset($backUrlKey) ? 'backUrlKey=' . $backUrlKey : '') }}">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>
            @elseif (isset($routePrefix) && $routePrefix == 'order')

            @else
            <a class="delete-action" href="#del-confirm" data-toggle="modal" data-placement="top"
            data-original-title="{{ trans('actions.destroy') }}"
            data-action="{{ route($deleteRoute, $entity->id) . '?' . (isset($backUrlKey) ? 'backUrlKey=' . $backUrlKey : '') }}">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </a>
            @endif
        @endif
    @endcan
    @if (isset($history))
        <a class="order-history" data-id="{{ $entity->id }}" data-name="{{ $entity->$history }}" data-toggle="tooltip"
           data-placement="top" title="" data-original-title="{{ trans('actions.history') }}">
            <i class="fa fa-history" aria-hidden="true"></i>
        </a>
    @endif
    @if (isset($gps) && !empty($gps))
        <a class="gps-history" data-id="{{ $entity->id }}" data-name="{{ $entity->$gps }}" data-toggle="tooltip"
           data-placement="top" title="" data-original-title="{{ trans('actions.gps_history') }}">
            <i class="fa fa-location-arrow" title="{{ trans('actions.gps_history') }}" aria-hidden="true"></i>
        </a>
    @endif
    @if (isset($showAdvance))
        <a target="_blank" href="{{ getenv('HELP_DOMAIN', '') . trans('helps.' . $routeName) }}" data-toggle="tooltip"
           data-placement="top" title="" data-original-title="{{ trans('actions.help') }}">
            <i class="fa fa-question-circle"></i>
        </a>

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
    @if (isset($split_order))
        @if ($entity->status == config('constant.KHOI_TAO')
            && ($entity->status_partner == config('constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI')
            || $entity->status_partner == config('constant.PARTNER_CHO_XAC_NHAN')))
            <a class="split-action" href="#split-order" data-toggle="modal" data-placement="top"
               data-url="{{route('order.showSplitOrder', $entity->id)}}"
               data-original-title="{{ trans('actions.split_order') }}"
               title="">
                <i class="fa fa-columns" aria-hidden="true"></i>
            </a>

            <script>
                $('.split-action').on('click', function () {
                    let t = $(this);
                    $('#submit-quantity-order').attr('data-url', t.data('url'));
                });
            </script>
        {{-- @else
            <a class="split-action" href="#order_prevent" data-toggle="modal" data-placement="top"
               data-original-title="{{ trans('actions.split_order') }}">
                <i class="fa fa-columns" aria-hidden="true"></i>
            </a> --}}
        @endif
    @endif
</td>
