<?php

namespace App\Repositories;

use App\Model\Entities\GoodsUnit;
use App\Repositories\Base\CustomRepository;
use App\Validators\GoodsUnitValidator;
use Illuminate\Support\Collection;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\DB;

class GoodsUnitRepository extends CustomRepository
{
    protected $_fieldsSearch = ['title', 'note'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return GoodsUnit::class;
    }

    public function validator()
    {
        return GoodsUnitValidator::class;
    }

    /**
     * @return Collection
     */
    public function getListForSelect()
    {
        /*  return $this->search([
              'sort_type' => 'asc',
              'sort_field' => 'title'
          ])->pluck('title', 'id');*/

        return GoodsUnit::select(DB::raw('CONCAT(code, "|", title) AS title, id'))
            ->where('del_flag', '=', 0)
            ->pluck('title', 'id');
    }

    public function getData()
    {
        return GoodsUnit::All();
    }

    public function getTitleByIds($ids = [])
    {
        $result = "";
        if (empty($ids)) {
            return '';
        }
        $result = $this->search(['id_in' => $ids], ['title'])->implode(',', 'title');

        return $result;
    }

    public function getListByTitle()
    {
        return GoodsUnit::select(DB::raw('CONCAT(code, "|", title) AS title, id'))
            ->where('del_flag', '=', 0)
            ->pluck('id', 'title');
    }

    public function getItemsByUserID($all, $q, $customerId)
    {
        $query = GoodsUnit::select("id", "title", "code", 'customer_id')
                ->where(function ($query) use ($q) {
                    $query->where('code', 'LIKE', '%' . $q . '%')
                        ->orWhere('title', 'LIKE', '%' . $q . '%');
                });

        if ($customerId > 0) {
            $query = $query->where('customer_id', '=', $customerId);
        }

        $query = $query->orderBy('code', 'asc')
                ->paginate(10);
                                
        return $query;
    }
}
