<!--    flash - notification-->
@if(Session()->has('success'))
    <div id="success_msg_main">
        <div class="row">
            <div class="col-md-12">
                <ul class="col-md-12 alert alert-success">
                    @foreach((array)Session()->get('success') as $msg)
                        <li>
                            <i class="fa fa-check"></i>
                            <strong>{{$msg}}</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
@if (!empty($errors) && count($errors) > 0)
    <div id="error_msg_main">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>
                        <i class="fa fa-exclamation-circle"></i>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif