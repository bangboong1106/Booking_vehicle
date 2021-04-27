<div class="modal" id="trip_confirm" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label">{{trans('messages.confirm_change_range_date_trip')}}</h4>
            </div>
            <div class="modal-body">
                <form method="post" id="form_resize_date_trip">
                    {{trans('messages.are_you_sure_change_range_date_trip')}}
                    <hr/>
                    {!! MyForm::hidden('id','',['id'=>'trip_id']) !!}
                    <div class="form-group">
                        <label for="p-in"
                               class="label-heading">{{trans('models.order.attributes.ETD')}}</label>
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" class="form-control datepicker" name="trip_start_date"
                                       id="trip_start_date">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control timepicker" name="trip_start_time"
                                       id="trip_start_time">
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="p-in"
                               class="label-heading">{{trans('models.order.attributes.ETA')}}</label>
                        <div class="row">
                            <div class="col-md-7">
                                <input type="text" class="form-control datepicker" name="trip_end_date"
                                       id="trip_end_date">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control timepicker" name="trip_end_time"
                                       id="trip_end_time">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">{{trans('actions.cancel')}}
                </button>
                <button type="submit" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
</div>