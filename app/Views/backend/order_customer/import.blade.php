@component('layouts.backend.elements.excel._import_list', [
    'entities' => $entities, 
    'excelColumnMappingConfigs' =>$excelColumnMappingConfigs,
    'nest_property' => 'vehicle_groups',
     ])
@endcomponent