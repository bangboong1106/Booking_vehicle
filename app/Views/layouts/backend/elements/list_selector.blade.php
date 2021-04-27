@php
    $total = $entities->total();
@endphp
<span>
    <div class="custom-control custom-checkbox">
        <input {{!$total ? 'disabled ' : ''}} type="checkbox" class="custom-control-input check mouse-pointer" id="check_all_mass_destroy">
        <label class="custom-control-label {{!$total ? 'disabled ' : 'mouse-pointer '}}" for="check_all_mass_destroy"></label>
    </div>
</span>