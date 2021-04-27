@foreach($entities as $entity)
    <div class="fc-event order-item" data-start="{{strtotime("$entity->ETD_date $entity->ETD_time")}}"
         data-id="{{$entity->id}}" data-color="orange">
        <input type="hidden" value="{{$entity->id}}">
        <div class="row title {{strtotime("$entity->ETD_date $entity->ETD_time") < strtotime((new DateTime())->format('Y-m-d H:i:s')) ? 'title-expire-date' : ""}}">
            <div class="choose-vehicle col-md-1">
                <input data-id="{{$entity->id}}" data-text="{{$entity->order_code}}" type="checkbox"
                       class="check-order checkbox-single"
                       style="display: {{strtotime("$entity->ETD_date $entity->ETD_time") < strtotime((new DateTime())->format('Y-m-d H:i:s')) ? 'none' : "inline-block"}}"/>
            </div>
            <div class="col-md-8">
                <h6 class="text-middle">
                    <i class="fa fa-plus toggle-order-list-item" title="Click để mở rộng"></i>
                    <a class="order-detail" href="#"
                       data-show-url="{{route('order.show', $entity->id)}}"
                       data-id="{{$entity->id}}">
                        <span class="order-no" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="{{$entity->order_code}}{!! isset($entity->order_no) ? '' : '/'.$entity->order_no !!}">
                            {{$entity->order_code}}{!! isset($entity->order_no) ? '' : '/'.$entity->order_no !!}
                        </span>
                    </a>
                </h6>
            </div>
            <div class="col-md-3">
                <div class="pull-right-stars">
                    @if($entity->precedence == config('constant.ORDER_PRECEDENCE_SPECIAL'))
                        <span class="fa fa-star text-warning"></span>
                        <span class="fa fa-star text-warning"></span>
                        <span class="fa fa-star text-warning"></span>
                    @endif
                    @if($entity->precedence == config('constant.ORDER_PRECEDENCE_NORMAL'))
                        <span class="fa fa-star text-warning"></span>
                        <span class="fa fa-star text-warning"></span>
                    @endif
                    @if($entity->precedence == config('constant.ORDER_PRECEDENCE_LOW'))
                        <span class="fa fa-star text-warning"></span>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: none;"
             class="body {{strtotime("$entity->ETD_date $entity->ETD_time") < strtotime((new DateTime())->format('Y-m-d H:i:s')) ? 'expire-date' : ""}}">
            <div class="row">
                <div class="col-md-6">

                    <a target="_blank"
                       href="https://www.google.com/maps/search/?api=1&query={!! empty($entity->locationDestination) ? '' : $entity->locationDestination->title !!}">
                        {!! empty($entity->locationDestination) ? '' : '<i class="fa fa-map-marker"
                                                                                                      aria-hidden="true"></i>'.$entity->locationDestination->title !!}</a>
                </div>

                <div class="col-md-6 pull-right text-right">{{\Carbon\Carbon::parse($entity->ETD_date)->format('d-m-Y')}}
                    {{\Carbon\Carbon::parse($entity->ETD_time)->format('H:i')}}</div>
            </div>
            <div class="row">
                <div class="col-md-6">

                    <a target="_blank"
                       href="https://www.google.com/maps/search/?api=1&query={!! empty($entity->locationArrival) ? '' : $entity->locationArrival->title !!}">
                        {!! empty($entity->locationArrival) ? '' : '<i class="fa fa-map-marker"
                                                                                                      aria-hidden="true"></i>'.$entity->locationArrival->title !!}</a>
                </div>
                <div class="col-md-6 pull-right text-right">{{\Carbon\Carbon::parse($entity->ETA_date)->format('d-m-Y')}}
                    {{ \Carbon\Carbon::parse($entity->ETA_time)->format('H:i')}}</div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-6">{{trans('models.order.attributes.amount')}}</div>
                <div class="col-md-6 pull-right amount text-right">{{numberFormat($entity->amount)}} vnd</div>
            </div>
            <div class="row">
                <div class="col-md-6">{{trans('models.order.attributes.volume')}}</div>
                <div class="col-md-6 pull-right volume text-right">{{numberFormat($entity->volume)}} m³</div>
            </div>
            <div class="row">
                <div class="col-md-6">{{trans('models.order.attributes.quantity')}}</div>
                <div class="col-md-6 pull-right quantity text-right">{{numberFormat($entity->quantity)}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">{{trans('models.order.attributes.weight')}}</div>
                <div class="col-md-6 pull-right weight text-right">{{numberFormat($entity->weight)}} kg</div>
            </div>
            <div class="row">
                <div class="col-md-12">{{$entity->note}}</div>
            </div>
        </div>
    </div>
@endforeach