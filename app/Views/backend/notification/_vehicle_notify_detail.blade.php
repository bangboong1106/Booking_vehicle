@if(!empty($data) && sizeof($data) > 0)
    <h5>Danh sách xe hết hạn giấy phép</h5>
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 150px">Xe</th>
            <th style="width: 150px">Loại giấy tờ</th>
            <th style="width: 150px">Ngày hết hạn</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $index=>$item)
            <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
                <td class="text-center">
                    <a class="vehicle-detail" href="#"
                       data-show-url="{{route('vehicle.show', $item->id )}}"
                       data-id="{{$item->id}}">{{$item->reg_no}}</a>
                </td>
                <td class="text-center">{{$item->file_name}}</td>
                <td class="text-center">{{$item->expire_date}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@if(!empty($dataRepair) && sizeof($dataRepair) > 0)
    <h5>Danh sách xe đến hạn bảo dưỡng</h5>
    <table class="table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th style="width: 150px">Xe</th>
            <th style="width: 150px">Số km bảo dưỡng</th>
            <th style="width: 150px">Ngày bảo dưỡng gần nhất</th>
            <th style="width: 220px">Số km đã chạy <br/>(Tính từ ngày bảo dưỡng gần nhất)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($dataRepair as $index=>$item)
            <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
                <td class="text-center">
                    <a class="vehicle-detail" href="#"
                       data-show-url="{{route('vehicle.show', $item->id )}}"
                       data-id="{{$item->id}}">{{$item->reg_no}}</a>
                </td>
                <td class="text-center">{{numberFormat($item->repair_distance)}}</td>
                <td class="text-center">{{\Carbon\Carbon::parse($item->repair_date)->format('d-m-Y')}}</td>
                <td class="text-center">{{numberFormat($item->distance)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif