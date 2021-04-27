<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\GoodsGroup;
use App\Repositories\Base\CustomRepository;
use App\Validators\GoodsGroupValidator;
use Illuminate\Support\Facades\DB;

class GoodsGroupRepository extends CustomRepository
{
    function model()
    {
        return GoodsGroup::class;
    }

    public function validator()
    {
        return GoodsGroupValidator::class;
    }

}
