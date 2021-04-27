<div>
    <div class="modal-header">
        <button type="button" class="maximize"><i class="fa fa-window-maximize"></i></button>
        <button type="button" class="minimize" style="display: none"><i class="fa fa-window-minimize"></i>
        </button>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="modal-label">Cập nhật số model và số khung</h4>
    </div>
    <div class="modal-body">
        <table class="table table-bordered table-hover table-revenue view" style="min-height: 300px">
            <thead id="head_content">
                <tr class="active">
                    <th scope="col" class="text-left" style="width: 300px;">
                        Mã đơn hàng
                    </th>
                    <th scope="col" class="text-left" style="width: 200px;">Khách hàng
                    </th>
                    <th scope="col" class="text-left" style="width: 200px;">Địa điểm nhận hàng
                    </th>
                    <th scope="col" class="text-left" style="width: 200px;">Địa điểm trả hàng
                    </th>
                    <th scope="col" class="text-left" style="width: 200px;">Số model
                    </th>
                    <th scope="col" class="text-left" style="width: 200px;">Số khung
                    </th>
                </tr>
            </thead>
            <tbody id="body_content">
                @if (isset($items) && count($items) > 0)
                    @foreach ($items as $item)
                        <tr class="container-vin-no" data-id="{{$item->id}}">
                            <td class="text-left">
                                {{ $item->order_code }}
                            </td>
                            <td class="text-left">
                                {{ isset($item->customer) ? $item->customer->full_name : "-" }}
                            </td>
                            <td class="text-left">
                                {{  isset($item->locationDestination) ? $item->locationDestination->title : "-" }}
                            </td>
                            <td class="text-left">
                                {{  isset($item->locationArrival) ? $item->locationArrival->title : "-" }}
                            </td>
                            <td>
                                <input class="model_no form-control" type="text"
                                       value="{{ $item->model_no }}">
                            </td>
                            <td>
                                <input class="vin_no form-control" type="text"
                                       value="{{ $item->vin_no }}">
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="empty-data">
                        <td colspan="4" class="text-center">
                            <div class="empty-box">
                                <span style="left: 0">
                                    <i>Không thể tìm thấy dữ liệu trên chương trình</i>
                                </span>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <div class="col-md-10 text-right">
            <a href="#" class="btn " data-dismiss="modal">{{ trans('actions.close') }}</a>
            <button type="button" id="btn-mass-update-vin-no" class="btn btn-blue" style="width: 120px" data-url="{{route('order.mass-update-vin-no')}}">
                <i class="fa fa-save" style="margin-right: 8px"></i>Cập nhật
            </button>
        </div>
    </div>
</div>
