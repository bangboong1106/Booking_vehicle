@if(isset($showAdvance) && isset($showAuditing) && auth()->user()->can('view auditing'))
    @php
        $auditing_route = isset($auditing_route) ? $auditing_route : $routePrefix.'.auditing';
    @endphp
    <li class="list-group-item detail-info">
        <div class="row">
            <div class="col-md-12">
                <div class="card-header" role="tab" id="headingHistory">
                    <h5 class="mb-0 mt-0 font-16">
                        <a data-toggle="collapse" href="#collapseAuditing" aria-expanded="false"
                           aria-controls="collapseAuditing" id="showAuditing"
                           data-url="{{ route($auditing_route, $entity->id) }}">
                            {{ trans('models.auditing.name') }}
                            <i class="fa"></i>
                        </a>
                    </h5>
                </div>
                <div class="collapse" id="collapseAuditing">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </li>
@endif