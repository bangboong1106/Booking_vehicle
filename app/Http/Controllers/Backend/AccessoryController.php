<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\AccessoryRepository;
use App\Repositories\ColumnConfigRepository;

use Illuminate\Support\Facades\Auth;

/**
 * Class ContractController
 * @package App\Http\Controllers\Backend
 */
class AccessoryController extends BackendController
{

    protected $_columnConfigRepository;

    /**
     * @return ColumnConfigRepository
     */
    public function getColumnConfigRepository()
    {
        return $this->_columnConfigRepository;
    }

    /**
     * @param $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->_columnConfigRepository = $columnConfigRepository;
    }

    public function __construct(
        AccessoryRepository $accessoryRepository,
        ColumnConfigRepository $columnConfigRepository
    ) {
        parent::__construct();
        $this->setRepository($accessoryRepository);
        $this->setColumnConfigRepository($columnConfigRepository);


        $this->setBackUrlDefault('accessory.index');
        $this->setConfirmRoute('accessory.confirm');
        $this->setMenu('vehicles');
        $this->setTitle(trans('models.accessory.name'));
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_accessory'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }
}
