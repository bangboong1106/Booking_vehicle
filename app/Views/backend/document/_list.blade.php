@include('layouts.backend.elements.column_config._list',[
                                    'entity'=>'document',
                                    'is_action' => false,
                                    'is_add' => false,
                                    'dbclick' => false,
                                    'attributes' => getColumnConfig("document"), 
                                    'configList'=> isset($configList) ? $configList : []])
