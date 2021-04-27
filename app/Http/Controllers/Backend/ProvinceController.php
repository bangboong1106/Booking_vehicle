<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Base\BackendController;
use App\Repositories\ProvinceRepository;


class ProvinceController extends BackendController
{

    protected $_fieldsSearch = ['province_id', 'title'];

    public function __construct(ProvinceRepository $provinceRepository) {
        parent::__construct();
        $this->setRepository($provinceRepository);
        $this->setBackUrlDefault('province.index');
        $this->setConfirmRoute('province.confirm');
        $this->setMenu('location');
        $this->setTitle(trans('models.province.name'));
    }
}