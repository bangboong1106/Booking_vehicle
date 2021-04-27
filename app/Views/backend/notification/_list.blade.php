@if(!empty($datas) && 0 < sizeof($datas))
    @foreach($datas as $entity)
        @if($entity->action_screen == 1)
            <div class="order-detail notify-item"
                 data-show-url="{{$entity->action_id ? route('order.show', $entity->action_id ): ''}}"
                 data-id="{{$entity->action_id}}" data-notify-id="{{$entity->id}}"
                 data-read-status="{{$entity->read_status}}">
                <div class="{{$entity->read_status == 1 ? 'notification-item-read' : 'notification-item'}}">
                    <div class="float-left notification-icon">
                        <i class="fa fa-barcode img-notification-item"></i>
                    </div>
                    <div class="float-right">
                                    <span class="notify-details">
                                        {{ $entity->title }}
                                    </span>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ $entity->content }}
                            </small>
                        </div>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse( $entity->ins_date)->format('H:i:s d-m-Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($entity->action_screen == 2 || $entity->action_screen == 6)
            <div class="vehicle-notify-item notify-item" data-action-screen="{{$entity->action_screen}}"
                 data-id="{{$entity->action_id}}" data-notify-id="{{$entity->id}}"
                 data-read-status="{{$entity->read_status}}">
                <div class="{{$entity->read_status > 0 ? 'notification-item-read' : 'notification-item'}}">
                    <div class="float-left notification-icon">
                        <i class="fa fa-truck img-notification-item"></i>
                    </div>
                    <div class="float-right">
                                    <span class="notify-details">
                                        {{ $entity->title }}
                                    </span>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ $entity->content }}
                            </small>
                        </div>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse( $entity->ins_date)->format('H:i:s d-m-Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($entity->action_screen == 7)
            <div class="route-detail notify-item"
                 data-show-url="{{$entity->action_id ? route('route.show', $entity->action_id ): ''}}"
                 data-id="{{$entity->action_id}}" data-notify-id="{{$entity->id}}"
                 data-read-status="{{$entity->read_status}}">
                <div class="{{$entity->read_status == 1 ? 'notification-item-read' : 'notification-item'}}">
                    <div class="float-left notification-icon">
                        <i class="fa fa-barcode img-notification-item"></i>
                    </div>
                    <div class="float-right">
                                    <span class="notify-details">
                                        {{ $entity->title }}
                                    </span>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ $entity->content }}
                            </small>
                        </div>
                        <div class="clearfix">
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse( $entity->ins_date)->format('H:i:s d-m-Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

