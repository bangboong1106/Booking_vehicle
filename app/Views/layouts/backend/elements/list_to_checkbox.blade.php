<td class="text-center text-middle header-sticky list-checkbox-item">
    <label class="custom-control-label">
        <input type="checkbox" class="mass-destroy checkbox-single custom-control-input"
            {{ isset($selectedItem) && isset($id) && in_array($id, $selectedItem) ? 'checked' : '' }}>
        <span class="list-checkbox-item-check-mark">
            <i class="fa fa-check" aria-hidden="true"></i>
        </span>
    </label>
</td>