<div class="row">
    <div class="col-12">
        {!! MyForm::model($entity, ['route' => ['contact.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    {!! MyForm::hidden('id', $entity->id) !!}
                    <div class="row content-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! MyForm::label('contact_name', $entity->tA('contact_name'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('contact_name', $entity->contact_name, ['contact_name'=>$entity->tA('contact_name')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('phone_number', $entity->tA('phone_number'). ' <span class="text-danger">*</span>', [], false) !!}
                                {!! MyForm::text('phone_number', $entity->phone_number, ['phone_number'=>$entity->tA('phone_number')]) !!}
                            </div>
                            <div class="form-group">
                                {!! MyForm::label('email', $entity->tA('email'). '', [], false) !!}
                                {!! MyForm::text('email', $entity->email, ['email'=>$entity->tA('email')]) !!}
                            </div>
                            <div class="form-group">
                                <label for="location_id">{{$entity->tA('location_id')}}</label>
                                <div class="input-group mb-3 {{ empty($formAdvance) ? 'with-button-add' : '' }}">
                                    <select class="select-location" id="location_id"
                                            name="location_id" style="visibility: hidden">
                                        @foreach($locationList as $key => $title)
                                            <option value="{{$key}}"
                                                    {{ $key == $entity->location_id ? 'selected="selected"' : '' }}
                                                    title="{{$title}}">{{$title}}</option>
                                        @endforeach
                                    </select>
                                    @if(empty($formAdvance))
                                        <div class="input-group-append">
                                            <button class="btn btn-primary waves-effect waves-light quick-add"
                                                    type="button" data-model="location"
                                                    data-url="{{route('location.advance')}}">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                {!! MyForm::label('active', $entity->tA('active'), [], false) !!}
                                {!! MyForm::dropDown('active', $entity->active, $actives, false) !!}
                            </div>
                            {!! MyForm::hidden('location_title', $entity->location_title,['id'=>'location_title', 'class' => 'location_title']) !!}
                            {!! MyForm::hidden('full_address', $entity->full_address,['id'=>'full_address', 'class' => 'full_address']) !!}
                            @include('layouts.backend.elements._submit_form_button')
                        </div>

                    </div>

                </div>
            </div>
        </div>
        {!! MyForm::close() !!}
    </div>
</div>
@if(empty($formAdvance))
    <script>
        var urlLocation = '{{route('location.combo-location')}}';
    </script>
@endif
@push('scripts')
    <?php
    $jsFiles = [
        'autoload/object-select2',
        'vendor/lib/locationObject'
    ];
    ?>
    {!! loadFiles($jsFiles, $area, 'js') !!}
@endpush