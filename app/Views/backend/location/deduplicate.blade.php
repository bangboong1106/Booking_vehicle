<table class="table">
    <thead>
        <tr>
            <th>Mã địa điểm</th>
            <th>Tên địa điểm</th>
            <th>Địa chỉ</th>
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
                            {{ $item->code }}
                        </label>
                    </div>
                </td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->address }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
