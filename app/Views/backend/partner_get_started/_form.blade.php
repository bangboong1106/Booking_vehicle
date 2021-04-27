<form action="{{route('app.send-link')}}" method="POST" class="mb-3">
    @csrf
    <label for="email">{{ trans('models.partner_get_started.attributes.app.label_input_send_mail') }}</label>
    <div class="input-group mycustom mb-1">
        <div class="input-group-prepend">
            <span class="input-group-text" id="contain-icon-email"><i class="fa fa-envelope" aria-hidden="true"></i></span>
        </div>
        <input type="email" name="email" class="form-control input-email" placeholder="Email" value="{{\Auth::user()->email}}"/>
        <div class="btn-inline">
            <button class="btn" type="submit" id="submit-email">
                Gửi <i class="fa fa-paper-plane" aria-hidden="true"></i></button>
        </div>
    </div>
</form>