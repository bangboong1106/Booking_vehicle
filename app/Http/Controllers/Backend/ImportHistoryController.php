<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ImportHistoryRepository;

/**
 * Class ImportHistoryController
 * @package App\Http\Controllers\Backend
 */
class ImportHistoryController extends BackendController
{
    public function __construct(ImportHistoryRepository $importHistoryRepository)
    {
        parent::__construct();
        $this->setRepository($importHistoryRepository);
        $this->setBackUrlDefault('import-history.index');
        $this->setConfirmRoute('import-history.confirm');
        $this->setMenu('management');
        $this->setTitle(trans('models.import_history.name'));
    }
}
