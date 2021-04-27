<table class="table">
    <thead>
        <tr>
            <th>Mã khách hàng</th>
            <th>Tên khách hàng</th>
            <th>Số điện thoại</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input deduplicate-item" type="radio" name="radios"
                            id="route-{{ $item->id }}" value="{{ $item->id }}">
                        <label class="form-check-label" for="route-{{ $item->id }}">
                            {{ $item->customer_code }}
                        </label>
                    </div>
                </td>
                <td>{{ $item->full_name }}</td>
                <td>{{ $item->mobile_no }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
