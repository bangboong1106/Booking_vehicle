<ul class="list-group">
    <li class="list-group-item detail-info">

        @if(isset($showAdvance))
            <div class="toolbar-detail col-md-12">
                @include('layouts.backend.elements.detail_to_action')
            </div>
        @endif
    </li>
    <li class="{{isset($showAdvance) ? 'first' : ''}} list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'code', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'title', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'name_of_customer_id', 'isEditable' => false, 'value' => isset($entity->customer) ? $entity->customer->full_name : "-"])
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row">
            @include('layouts.backend.elements.detail_to_edit',['property' => 'description', 'isEditable' => false])
        </div>
    </li>
    <li class="list-group-item">
        <table class="table table-bordered table-hover" style="width: 100% !important;">
            <thead id="head_content">
            <tr class="active">
                <th scope="col" style="width: 50px" class="text-center">
                    STT
                </th>
                <th scope="col" class="text-left">{{ trans('models.location.attributes.title') }}</th>
                <th scope="col" class="text-left">{{ trans('models.location.attributes.province_id') }}</th>
                <th scope="col" class="text-left">{{ trans('models.location.attributes.district_id') }}</th>
                <th scope="col" class="text-left">{{ trans('models.location.attributes.ward_id') }}</th>
                <th scope="col" class="text-left">{{ trans('models.location.attributes.address') }}</th>
            </tr>
            </thead>
            <tbody id="body_content">
            @if(isset($entity->locations))
                @foreach($entity->locations as $index => $location)
                    <tr>
                        <td class="text-center">{{$index +1}}</td>
                        <td class="">
                            <a class="driver-left" href="#"
                               data-show-url="{{isset($showAdvance) ? route('location.show', $location->id) : ''}}"
                               data-id="{{ isset($showAdvance) ? $location->id : ''}} ">{{$location->title}}</a>
                        </td>
                        <td>{{ $location->p_title }}</td>
                        <td>{{ $location->d_title }}</td>
                        <td>{{ $location->w_title }}</td>
                        <td>{{ $location->address }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">Không có dữ liệu</td>
                </tr>
            @endif
            </tbody>
        </table>
    </li>
</ul>