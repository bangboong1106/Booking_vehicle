@extends('layouts.backend.layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="form-inline m-b-10 justify-content-between">
                    <div class="row">
                        <div class="col-md-12 text-xs-center">
                            <h4 class="page-title">{{ $title }}</h4>
                            <span class="collapse-bar"><i class="fa fa-bars" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="filter">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control m-b-20"
                                    placeholder="{{ trans('models.common.vehicle_search') }}" id="filter-vehicle" />
                            </div>
                        </div>

                        @if (\Auth::check() && \Auth::user()->role == 'admin')
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <select class="select2 form-control select-partner" id="partner-id">
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value=1>Xe trống, đang chờ việc
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value=2>Xe đang vận chuyển
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value=4>Xe hỏng
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <ul class="vehicle-list">
                                @foreach ($vehicleList as $vehicle)
                                    <li class="journey-vehicle-item" data-plate="{{ $vehicle->vehicle_plate }}"
                                        data-status="{{ $vehicle->status }}"
                                        data-partner-id ="{{ $vehicle->partner_id}}">
                                        <div class="row">
                                            <div class="col-4">
                                                <b><span style="color: red">{{ $vehicle->title }}</span></b>
                                            </div>
                                            <div
                                                class="col-8 text-right">
                                                <span class="status {!!  ($vehicle->status == 4 ? 'fail' : ($vehicle->status == 2 ? 'drive' : '')) !!}">{{ $vehicle->getStatus() }}</span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><b>{{ numberFormat($vehicle->weight) }} </b>(kg)</div>
                                            <div class="col-6 text-right"><b>{{ numberFormat($vehicle->volume) }} </b>(m3)
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                {{ $vehicle->length != null ? numberFormat($vehicle->length) : numberFormat(0) }}
                                                *
                                                {{ $vehicle->width != null ? numberFormat($vehicle->width) : numberFormat(0) }}
                                                *
                                                {{ $vehicle->height != null ? numberFormat($vehicle->height) : numberFormat(0) }}
                                            </div>
                                        </div>
                                        @if (!empty($vehicle->current_location))
                                            <div class="row">
                                                <div class="col-12">
                                                    <i>{{ $vehicle->current_location }}</i>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                    <li class="detail-item-sep"></li>
                                @endforeach
                                <li class="not-found" style={{ $vehicleList->count() > 0 ? 'display:none' : ""}}>
                                    <p>{{ trans('messages.no_data_found') }}</p>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="journey-map">
                        <div id="journey_map"></div>
                        <p class="m-b-5 m-t-5">{{ trans('models.journey.attributes.description') }}</p>
                        <div class="menu" id="menu">
                            <div class="menu-item" id="set_button"><i
                                    class="fa fa-map-pin"></i>{{ trans('models.journey.attributes.set') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.sobekrepository.org/includes/gmaps-markerwithlabel/1.9.1/gmaps-markerwithlabel-1.9.1.min.js">
    </script>
    <script>
        let vehicles = {!! $vehicles !!},
            detailUrl = "{{ route("journey.detail", -1) }}",
            urlPartner = '{{route('partner.combo-partner')}}'
            userPartnerId = @json(\Auth::user()->partner_id),
            userRole = @json(\Auth::user()->role);
    </script>
@endsection
