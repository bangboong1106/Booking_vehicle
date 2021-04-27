{{--Thông tin giao hàng--}}
<div class="card-header" role="tab" id="headingDestination">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseDestination"
           aria-expanded="true" aria-controls="collapseDestination" class="collapse-expand">
            {{trans('models.order.attributes.destination_info')}}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<div id="collapseDestination" class="collapse show location-container" role="tabpanel" aria-labelledby="Destination">
    <div class="card-body">
        {{--        Địa điểm nhận hàng--}}
        <div class="location-order location-order-destination">
            <div class="form-group row label-info">
                <div class="delete-location disabled"></div>
                <div class="col-5">
                    {!! MyForm::label('etd', $entity->tA('etd'), [], false) !!}
                </div>
                <div class="col-3">
                    {!! MyForm::label('ETD', $entity->tA('ETD'), [], false) !!}
                </div>
                <div class="col-3 ETD_reality  {{$isETDHide ? 'hide' : ''}}">
                    {!! MyForm::label('ETD_date_reality', $entity->tA('ETD_date_reality'), [], false) !!}
                </div>
            </div>
            @if(count($locationDestinations) > 0)
                @foreach($locationDestinations as $index => $locationDestination)
                    <div class="form-group row location-item">
                        <div class="delete-location {{ $index === 0 ? 'disabled' : '' }}">{{ $index === 0 ? '' : 'X' }}</div>
                        <div class="col-md-5 lc-item">
                            <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                                <select class="select-location form-control"
                                        name="{{'locationDestinations['.$index.'][location_id]'}}"
                                        data-field="location_id">
                                    @foreach($locationList as $key => $title)
                                        @if (isset($locationDestination['location_id']) && $key == $locationDestination['location_id'])
                                            <option value="{{$key}}" selected="selected" title="{{$title}}">
                                                {{$title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                     <span class="input-group-addon location-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                     </span>
                                    @if(empty($formAdvance) && auth()->user()->can('add location'))
                                        <button class="btn btn-third quick-add" type="button" data-model="location"
                                                data-url="{{route('location.advance')}}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </button>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! MyForm::text('locationDestinations['.$index.'][time]', $locationDestination['time'],
                                    ['placeholder'=>$entity->tA('ETD_time'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::text('locationDestinations['.$index.'][date]',
                                    format($locationDestination['date'], 'd-m-Y'), ['placeholder'=>$entity->tA('ETD'),
                                    'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ETD_reality {{$isETDHide ? 'hide' : ''}}">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! MyForm::text('locationDestinations['.$index.'][time_reality]',
                                   strpos($routeName, 'duplicate') ? '': $entity->ETD_time_reality, ['placeholder'=>$entity->tA('time_reality'),
                                    'class' => 'timepicker time-input', 'data-field' => 'time_reality']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::text('locationDestinations['.$index.'][date_reality]',
                                    strpos($routeName, 'duplicate') ? '': format($entity->ETD_date_reality, 'd-m-Y'), ['placeholder'=>$entity->tA('date_reality'),
                                    'class'=>'datepicker date-input', 'data-field' => 'date_reality']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="form-group row location-item">
                    <div class="delete-location disabled"></div>
                    <div class="col-md-5 lc-item">
                        <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                            <select class="select-location form-control"
                                    name="locationDestinations[0][location_id]" data-field="location_id"></select>
                            <div class="input-group-append">
                                     <span class="input-group-addon location-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                     </span>
                                @if(empty($formAdvance) && auth()->user()->can('add location'))
                                    <button class="btn btn-third quick-add" type="button" data-model="location"
                                            data-url="{{route('location.advance')}}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                {!! MyForm::text('locationDestinations[0][time]', isset($entity->ETD_time) ?
                                $entity->ETD_time : $today->format('H-i'),
                                ['placeholder'=>$entity->tA('ETD_time'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::text('locationDestinations[0][date]', isset($entity->ETD_date) ?
                                $entity->ETD_date : $today->format('d-m-Y'),
                                ['placeholder'=>$entity->tA('ETD'), 'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 ETD_reality {{$isETDHide ? 'hide' : ''}}">
                        <div class="row">
                            <div class="col-md-6">
                                {!! MyForm::text('locationDestinations[0][time_reality]',
                                null, ['placeholder'=>$entity->tA('time_reality'),
                                'class' => 'timepicker time-input', 'data-field' => 'time_reality']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::text('locationDestinations[0][date_reality]',
                                null, ['placeholder'=>$entity->tA('date_reality'),
                                'class'=>'datepicker date-input', 'data-field' => 'date_reality']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-group row add-block">
            <div class="col-md-4 m-l-24">
                <button id="destination-plus-button" class="btn btn-secondary2" tabindex="0" data-type="multiple">
                    <div class="crm-flex crm-align-items-center">
                        <i class="fa fa-plus"></i> Thêm địa điểm
                    </div>
                </button>
            </div>
            <div class="form-group row location-item location-item-default location-destination">
                <div class="delete-location">X</div>
                <div class="col-md-5 lc-item">
                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                        <select class="select-lc select-location-add form-control" data-field="location_id"></select>
                        <div class="input-group-append">
                                 <span class="input-group-addon location-search">
                                    <div class="input-group-text bg-transparent">
                                        <i class="fa fa-search"></i>
                                    </div>
                                 </span>
                            @if(empty($formAdvance) && auth()->user()->can('add location'))
                                <button class="btn btn-third quick-add" type="button" data-model="location"
                                        data-url="{{route('location.advance')}}">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-6">
                            {!! MyForm::text(null, $today->format('H:i'),['placeholder'=>$entity->tA('ETD_time'),
                            'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::text('ETD_date', $today->format('d-m-Y'), ['placeholder'=>$entity->tA('ETD'),
                            'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                        </div>
                    </div>
                </div>
                {{--<div class="col-md-3 ETD_reality {{$isETDHide ? 'hide' : ''}}">
                    <div class="row">
                        <div class="col-md-6">
                            {!! MyForm::text(null, null,['placeholder'=>$entity->tA('time_reality'),
                            'class' => 'timepicker time-input', 'data-field' => 'time_reality']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::text(null, null, ['placeholder'=>$entity->tA('date_reality'),
                            'class'=>'datepicker date-input', 'data-field' => 'date_reality']) !!}
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-5">
                {!! MyForm::label('number_of_delivery_points', $entity->tA('number_of_delivery_points'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('number_of_delivery_points', numberFormat($entity->number_of_delivery_points), [
                        'placeholder' => $entity->tA('number_of_delivery_points'),
                        'class' => 'number-input'
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-5">
                {!! MyForm::label('contact_name_destination', $entity->tA('contact_name_destination'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
                    </div>
                    {!! MyForm::text('contact_name_destination', $entity->contact_name_destination, [
                        'placeholder' => $entity->tA('contact_name_destination'),
                        'class' => 'contact-name'
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                {!! MyForm::label('contact_mobile_no_destination', $entity->tA('contact_mobile_no_destination'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-phone" aria-hidden="true"></i></span>
                    </div>
                    {!! MyForm::text('contact_mobile_no_destination', $entity->contact_mobile_no_destination,
                     ['placeholder'=>$entity->tA('contact_mobile_no_destination'), 'class' =>'phone-input contact-phone']) !!}
                </div>
            </div>
            <div class="col-md-4 text-right">
                <span class="contact-search" type-contact="destination"
                      title="Click để chọn danh bạ">Chọn từ danh bạ</span>
                <div>
                    {!! MyForm::checkbox('auto-create-template_destination', 1, true, ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'auto-create-template_destination']) !!}
                    <span>Tự động lưu thông tin liên hệ</span>
                </div>
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-4">
                <a href="#" class="show-detail" data-type="dest">HIỆN CHI TIẾT</a>
            </div>
        </div>
        <div class="form-group row dest detail hide m-l-12">
            <div class="col-md-4">
                {!! MyForm::label('contact_email_destination', $entity->tA('contact_email_destination'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                    </div>
                    {!! MyForm::email('contact_email_destination', $entity->contact_email_destination, [
                        'placeholder'=>$entity->tA('contact_email_destination'),
                        'type'=>'email',
                        'class' => 'contact-email'
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="form-group row loading-container dest detail hide m-l-12">
            <div class="col-md-4">
                {!! MyForm::label('loading_destination_fee', $entity->tA('loading_destination_fee'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('loading_destination_fee', empty($entity->loading_destination_fee) ? '' : numberFormat($entity->loading_destination_fee),
                    ['placeholder'=>$entity->tA('loading_destination_fee'),'class' => 'number-input']) !!}
                    <div class="input-group-prepend">
                        <span class="input-group-text form-group-right">VND</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row dest detail hide m-l-12">
            <div class="col-md-12">
                {!! MyForm::label('informative_destination', $entity->tA('informative_destination'), [], false) !!}
                {!! MyForm::textarea('informative_destination', $entity->informative_destination,['placeholder'=>$entity->tA('informative_destination'),'rows'=>2]) !!}
            </div>
        </div>
    </div>
</div>

{{--Thông tin trả hàng--}}
<div class="card-header" role="tab" id="headingArrival">
    <h5 class="mb-0 mt-0 font-20">
        <a data-toggle="collapse" href="#collapseArrival"
           aria-expanded="true" aria-controls="collapseArrival" class="collapse-expand">
            {{trans('models.order.attributes.arrival_info')}}
            <i class="fa"></i>
        </a>
    </h5>
</div>
<input type="hidden" id="hdfDestinationLocationId"/>
<input type="hidden" id="hdfArrivalLocationId"/>
<div id="collapseArrival" class="collapse show location-container" role="tabpanel" aria-labelledby="Arrival">
    <div class="card-body">
        <div class="location-order location-order-arrival">
            <div class="form-group row label-info">
                <div class="delete-location disabled"></div>
                <div class="col-5">
                    {!! MyForm::label('eta', $entity->tA('eta'), [], false) !!}
                </div>
                <div class="col-3">
                    {!! MyForm::label('ETA', $entity->tA('ETA'), [], false) !!}
                </div>
                <div class="col-3 ETA_reality {{$isETAHide ? 'hide' : ''}}">
                    {!! MyForm::label('ETA_date_reality', $entity->tA('ETA_date_reality'), [], false) !!}
                </div>
            </div>
            @if(count($locationArrivals) > 0)
                @foreach($locationArrivals as $index => $locationArrival)
                    <div class="form-group row location-item">
                        <div class="delete-location {{ $index === 0 ? 'disabled' : '' }}">{{ $index === 0 ? '' : 'X' }}</div>
                        <div class="col-md-5 lc-item">
                            <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                                <select class="select-location form-control"
                                        name="{{'locationArrivals['.$index.'][location_id]'}}" data-field="location_id">
                                    @foreach($locationList as $key => $title)
                                        @if (isset($locationArrival['location_id']) && $key == $locationArrival['location_id'])
                                            <option value="{{$key}}" selected="selected" title="{{$title}}">
                                                {{$title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                     <span class="input-group-addon location-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                     </span>
                                    @if(empty($formAdvance) && auth()->user()->can('add location'))

                                        <button class="btn btn-third quick-add" type="button" data-model="location"
                                                data-url="{{route('location.advance')}}">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! MyForm::text('locationArrivals['.$index.'][time]', $locationArrival['time'],
                                    ['placeholder'=>$entity->tA('ETD_time'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::text('locationArrivals['.$index.'][date]',
                                    format($locationArrival['date'], 'd-m-Y'), ['placeholder'=>$entity->tA('ETD'),
                                    'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 ETA_reality {{$isETAHide ? 'hide' : ''}}">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! MyForm::text('locationArrivals['.$index.'][time_reality]',
                                   strpos($routeName, 'duplicate') ? '': $entity->ETA_time_reality, ['placeholder'=>$entity->tA('time_reality'),
                                    'class' => 'timepicker time-input', 'data-field' => 'time_reality']) !!}
                                </div>
                                <div class="col-md-6">
                                    {!! MyForm::text('locationArrivals['.$index.'][date_reality]',
                                   strpos($routeName, 'duplicate') ? '': format($entity->ETA_date_reality, 'd-m-Y'), ['placeholder'=>$entity->tA('date_reality'),
                                    'class'=>'datepicker date-input', 'data-field' => 'date_reality']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="form-group row location-item">
                    <div class="delete-location disabled"></div>
                    <div class="col-md-5 lc-item">
                        <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                            <select class="select-location form-control"
                                    name="locationArrivals[0][location_id]" data-field="location_id"></select>
                            @if(empty($formAdvance) && auth()->user()->can('add location'))
                                <div class="input-group-append">
                                     <span class="input-group-addon location-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                     </span>
                                    <button class="btn btn-third quick-add" type="button" data-model="location"
                                            data-url="{{route('location.advance')}}">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                {!! MyForm::text('locationArrivals[0][time]', isset($entity->ETA_time) ?
                                $entity->ETA_time : $today->modify('+1 hours')->format('H-i'),
                                ['placeholder'=>$entity->tA('ETA_time'), 'class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::text('locationArrivals[0][date]', isset($entity->ETA_date) ?
                                $entity->ETA_date : $today->format('d-m-Y'),
                                ['placeholder'=>$entity->tA('ETA'), 'class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 ETA_reality {{$isETAHide ? 'hide' : ''}}">
                        <div class="row">
                            <div class="col-md-6">
                                {!! MyForm::text('locationArrivals[0][time_reality]',
                                null, ['placeholder'=>$entity->tA('time_reality'),
                                'class' => 'timepicker time-input', 'data-field' => 'time_reality']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! MyForm::text('locationArrivals[0][date_reality]',
                                null, ['placeholder'=>$entity->tA('date_reality'),
                                'class'=>'datepicker date-input', 'data-field' => 'date_reality']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="form-group row add-block">
            <div class="col-md-4 m-l-24">
                <button id="arrival-plus-button" class="btn btn-secondary2" tabindex="0" data-type="multiple">
                    <div class="crm-flex crm-align-items-center">
                        <i class="fa fa-plus"></i>Thêm địa điểm
                    </div>
                </button>
            </div>
            <div class="form-group row location-item location-item-default">
                <div class="delete-location">X</div>

                <div class="col-md-5 lc-item">
                    {{--                    <label for="locationArrival">{{trans('models.order.attributes.etd')}}</label>--}}
                    <div class="input-group {{ empty($formAdvance) && auth()->user()->can('add location') ? 'with-button-add' : '' }}">
                        <select class="select-lc select-location-add form-control" data-field="location_id"></select>
                        <div class="input-group-append">
                                     <span class="input-group-addon location-search">
                                        <div class="input-group-text bg-transparent">
                                            <i class="fa fa-search"></i>
                                        </div>
                                     </span>
                            @if(empty($formAdvance) && auth()->user()->can('add location'))

                                <button class="btn btn-third quick-add" type="button" data-model="location"
                                        data-url="{{route('location.advance')}}">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="row">
                        <div class="col-12">
                            {{--                            {!! MyForm::label('ETA', $entity->tA('ETD'), [], false) !!}--}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {!! MyForm::text(null, $today->format('H:i'),
                            ['class'=>'timepicker time-input', 'data-field' => 'time']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::text('ETD_date', $today->format('d-m-Y'),
                            ['class'=>'datepicker date-input', 'data-field' => 'date']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-5">
                {!! MyForm::label('number_of_arrival_points', $entity->tA('number_of_arrival_points'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('number_of_arrival_points', numberFormat($entity->number_of_arrival_points), [
                        'placeholder' => $entity->tA('number_of_arrival_points'),
                        'class' => 'number-input'
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-5">
                {!! MyForm::label('contact_name_arrival', $entity->tA('contact_name_arrival'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                           <i class="fa fa-user" aria-hidden="true"></i>
                        </span>
                    </div>
                    {!! MyForm::text('contact_name_arrival', $entity->contact_name_arrival, [
                        'placeholder'=>$entity->tA('contact_name_arrival'),
                        'class' => 'contact-name'
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                {!! MyForm::label('contact_mobile_no_arrival', $entity->tA('contact_mobile_no_arrival'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </span>
                    </div>
                    {!! MyForm::text('contact_mobile_no_arrival', $entity->contact_mobile_no_arrival,
                     ['placeholder'=>$entity->tA('contact_mobile_no_arrival'), 'class'=>'contact-phone phone-input']) !!}
                </div>
            </div>
            <div class="col-md-4 text-right">
                <span class="contact-search" type-contact="arrival" title="Click để chọn danh bạ">Chọn từ danh bạ</span>
                <div>
                    {!! MyForm::checkbox('auto-create-template_arrival', 1, true, ['data-plugin' => "switchery", 'data-color' => "#11509b", 'class' => 'switchery', 'id' => 'auto-create-template_arrival']) !!}
                    <span>Tự động lưu thông tin liên hệ</span>
                </div>
            </div>
        </div>
        <div class="form-group row m-l-12">
            <div class="col-md-4">
                <a href="#" class="show-detail" data-type="arrival">
                    HIỆN CHI TIẾT
                </a>
            </div>
        </div>
        <div class="form-group row arrival detail hide m-l-12">
            <div class="col-md-4">
                {!! MyForm::label('contact_email_arrival', $entity->tA('contact_email_arrival'), [], false) !!}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                        </span>
                    </div>
                    {!! MyForm::email('contact_email_arrival', $entity->contact_email_arrival,
                    ['placeholder'=>$entity->tA('contact_email_arrival'),
                     'type'=>'email',
                     'class' => 'contact-email',
                     'pattern'=>"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"]) !!}
                </div>
            </div>
        </div>
        <div class="form-group row loading-container arrival detail hide m-l-12">
            <div class="col-md-4">
                {!! MyForm::label('loading_arrival_fee', $entity->tA('loading_arrival_fee'), [], false) !!}
                <div class="input-group">
                    {!! MyForm::text('loading_arrival_fee', empty($entity->loading_arrival_fee) ? '' : numberFormat($entity->loading_arrival_fee),
                    ['placeholder'=>$entity->tA('loading_arrival_fee'),'class' => 'number-input']) !!}
                    <div class="input-group-prepend">
                        <span class="input-group-text form-group-right">VND</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row arrival detail hide m-l-12">
            <div class="col-md-12">
                {!! MyForm::label('informative_arrival', $entity->tA('informative_arrival'), [], false) !!}
                {!! MyForm::textarea('informative_arrival', $entity->informative_arrival,['placeholder'=>$entity->tA('informative_arrival'),'rows'=>2]) !!}
            </div>
        </div>
    </div>
</div>