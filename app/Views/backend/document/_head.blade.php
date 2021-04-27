@include('layouts.backend.elements.column_config._head',[
    'entity'=>'document',
    'is_action' => false,
    'attributes' => getColumnConfig("document"), 
    'configList'=> isset($configList) ? $configList : []])