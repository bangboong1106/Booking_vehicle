<div class="row">
    <div class="col-12">
    {!! MyForm::model($entity, ['route' => ['customer-role.valid', $entity->id]])!!}
        <div class="row">
            <div class="col-md-12">
                <div class="card-box form-display">
                    @include('layouts.backend.elements._form_label')
                    <div class="row">
                        <div class="form-group col-md-6">
                            {!! MyForm::label('name', $entity->tA('name') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('name', $entity->name, ['placeholder'=>$entity->tA('name'), isset($isDuplicate) ? '' : ($entity->id ? 'disabled' : '')]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! MyForm::label('title', $entity->tA('title') . ' <span class="text-danger">*</span>', [], false) !!}
                            {!! MyForm::text('title', $entity->title, ['placeholder'=>$entity->tA('title')]) !!}
                        </div>
                    </div>
                    <input type="hidden" name="group" value="customer">
                    <div class="form-inline m-b-10 justify-content-between">
                        <h4 class="m-t-0 header-title">{{trans('models.role.permissions.title')}}</h4>
                        <div class="flex-wrap" role="group">
                            <a class="btn btn-checkbox" href="#" id="check-all">
                                <i class="fa fa-check-circle-o"></i> {{trans('models.role.permissions.choose_all')}}
                            </a>
                            <a class="btn btn-checkbox ml-3" href="#" id="uncheck-all">
                                <i class="fa fa-times-circle-o"></i> {{trans('models.role.permissions.uncheck_all')}}
                            </a>
                        </div>
                    </div>
                    <div class="table-rep-plugin">
                        <div class="table-responsive">
                            <table id="form-table" class="table">
                                <thead>
                                <tr>
                                    <th>
                                        <button type="button" class="btn btn-primary" id="btn-toggle-collapse" data-toggle="tooltip" data-placement="top" title="Mở rộng / Thu gọn"></button>
                                    </th>
                                    <th class="label-table" >{{ trans('models.role.permissions.name') }}</th>
                                    <th>{{ trans('models.role.permissions.view') }}</th>
                                    <th>{{ trans('models.role.permissions.add') }}</th>
                                    <th>{{ trans('models.role.permissions.edit') }}</th>
                                    <th>{{ trans('models.role.permissions.delete') }}</th>
                                    <th>{{ trans('models.role.permissions.import') }}</th>
                                    <th>{{ trans('models.role.permissions.export') }}</th>
                                    <th>{{ trans('models.role.permissions.lock') }}</th>
                                    <th>{{ trans('models.role.permissions.unlock') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($groups as $group)
                                    <tr>
                                        <th colspan="10" data-toggle="collapse" data-target="#{{$group}}" aria-controls="{{$group}}" class="accordion-toggle panel-title">{{trans('common.'.$group)}}</th>
                                    </tr>
                                    @if (isset($permissions[$group]))
                                        <tr class="p-0">
                                            <td colspan="10" class="p-0 border-top-0">
                                                @foreach($permissions[$group] as $name => $permissionGroup)
                                                    <div class="accordion-body collapse show" id="{{$group}}">
                                                        <table class="w-100">
                                                            <tr class="no-border">
                                                                <th></th>
                                                                <th class="label-table">{{ trans('models.'.explode(' ', $name)[1].'.name') }}</th>
                                                                @foreach($permissionOrder as $order)
                                                                    @if(isset($permissionGroup[$order]))
                                                                        <td class="border-top-0">
                                                                            <div class="checkbox checkbox-success checkbox-circle">
                                                                                <input id="checkbox-{{$permissionGroup[$order]->id}}" type="checkbox"
                                                                                       {{in_array($permissionGroup[$order]->name, $entity->permissionList) ? 'checked' : ''}}
                                                                                       value="{{$permissionGroup[$order]->name}}" name="permissionList[]">
                                                                                <label for="checkbox-{{$permissionGroup[$order]->id}}"></label>
                                                                            </div>
                                                                        </td>
                                                                    @else
                                                                        <td></td>
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @include('layouts.backend.elements._submit_form_button')
                </div>
            </div>

        </div>
    {!! MyForm::close() !!}
    </div>
</div>