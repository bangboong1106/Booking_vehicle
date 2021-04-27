<div class="col-12 col-md-5 pt-2 pb-2 block-app">
    <h5 class="text-center">{{trans('models.get_started.attributes.name_app.'.$id_app)}}</h5>
    <div class="img-container d-block">
        <div class="row">
            <div class="col-6 col-md-5">
                <div class="float-left store-badge">
                    <a href="{{$play_store_url}}" target="_blank">
                        <img src="{{asset('/css/backend/images/white-google-play.jpg')}}" alt="google-play"/>
                    </a>

                    <a href="{{$app_store_url}}" target="_blank">
                        <img src="{{asset('/css/backend/images/app-store.png')}}" alt="apple-store"/>
                    </a>
                </div>
            </div>
            <div class="col-6 col-md-7">
                <div class="float-right qr-img">
                    <img src="data:image/png;base64, {{ base64_encode(\QrCode::format('png')->size(100)->margin(1)
                                                            ->encoding('UTF-8')
                                                            ->errorCorrection('H')->generate(route('redirect-to-store.redirect', $id_app))) }} ">
                </div>
            </div>
        </div>
    </div>
</div>