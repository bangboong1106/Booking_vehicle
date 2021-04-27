<div class="row">
    <div class="col-12">
        <form method="post" id="form_trip">
            <div class="row">
                <div class="card-box">
                    {!! MyForm::hidden('id','',['id'=>'create_trip_id']) !!}
                    {!! MyForm::hidden('vehicle_id','',['id'=>'vehicle_id']) !!}
                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('trip_no', $entity->tA('trip_no')  . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('trip_no', '', ['placeholder'=>$entity->tA('trip_no'),'id'=>'trip_no']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! MyForm::label('name', $entity->tA('name')  . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('trip_name', '', ['placeholder'=>$entity->tA('name'),'id'=>'trip_name']) !!}
                        </div>
                    </div>
                    <div class="advanced form-group row">
                        <div class="col-md-6">
                            <label for="driver">{{ $entity->tA('primary_driver')}}</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select-driver" id="primary_driver_id"
                                        name="primary_driver_id">
                                </select>
                                <span class="input-group-addon">
                                <div class="input-group-text bg-transparent">
                                <i class="fa fa-search driver-search" id="primary-driver-search"></i>
                            </div>
                            </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="driver">{{ $entity->tA('secondary_driver')}}</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <select class="select-driver" id="secondary_driver_id"
                                        name="secondary_driver_id">
                                </select>
                                <span class="input-group-addon">
                                <div class="input-group-text bg-transparent">
                                <i class="fa fa-search driver-search" id="secondary-driver-search"></i>
                            </div>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="advanced form-group">
                        <label for="order">{{ $entity->tA('order')}}</label>
                        <div class="input-group select2-bootstrap-prepend">
                            <select class="select-order" id="order_id"
                                    name="order_ids[]" multiple='multiple'>
                            </select>
                            <span class="input-group-addon">
                                <div class="input-group-text bg-transparent">
                                <i class="fa fa-search" id="order-search"></i>
                            </div>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('ETD', $entity->tA('ETD'), [], false) !!}
                        </div>
                        <div class="col-md-6">
                            <label for="location_destination_id">{{ $entity->tA('location_destination')}}</label>
                        </div>
                    </div>
                    <div class=" form-group row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8">
                                    {!! MyForm::text('ETD_date_trip', '', ['placeholder'=>$entity->tA('ETD_date'), 'class'=>'datepicker']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::text('ETD_time_trip','', [ 'class'=>'timepicker']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="select-location" id="location_destination_id"
                                    name="location_destination_id">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            {!! MyForm::label('ETA', $entity->tA('ETA'), [], false) !!}
                        </div>
                        <div class="col-md-6">
                            <label for="location_arrival_id">{{ $entity->tA('location_arrival')}}</label>
                        </div>
                    </div>
                    <div class=" form-group row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8">
                                    {!! MyForm::text('ETA_date_trip', '', ['placeholder'=>$entity->tA('ETA_date'), 'class'=>'datepicker']) !!}
                                </div>
                                <div class="col-md-4">
                                    {!! MyForm::text('ETA_time_trip', '', ['class'=>'timepicker']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="select-location" id="location_arrival_id"
                                    name="location_arrival_id">
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
