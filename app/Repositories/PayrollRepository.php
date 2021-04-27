<?php

namespace App\Repositories;

use App\Model\Entities\Payroll;
use App\Repositories\Base\CustomRepository;
use App\Validators\PayrollValidator;
use DB;
use Illuminate\Support\Str;

class PayrollRepository extends CustomRepository
{
    protected $_fieldsSearch = ['name', 'code'];

    function model()
    {
        return Payroll::class;
    }

    public function validator()
    {
        return PayrollValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['customerGroups', 'formulas']);
    }

    public function getPayrollWithID($id)
    {
        try {
            if (!$id)
                return null;
            return $this->findFirstOrNew(['id' => $id]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPayrollByCode($code)
    {
        if (!$code)
            return null;
        return $entity = $this->search([
            'code_eq' => $code
        ])->first();
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = ['*',
            DB::raw('group_concat(cg.name SEPARATOR \';\') as customer_groups')];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = ['*'];

        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }


    public function search($query = array(), $columns = [])
    {
        $this->_resetBuilder();
        $this->setQueryParams($query);
        if (empty($query)) {
            $q = $this->getBuilder()->select($this->_buildColumn($columns))
                ->leftJoin('payroll_customer_group as pcg', $this->getTableName() . '.id', '=', 'payroll_id')
                ->leftJoin('customer_group as cg', 'cg.id', '=', 'pcg.customer_group_id');
            $q = $q->groupBy($this->getTableName() . '.id')
                ->orderBy($this->getSortField(), $this->getSortType());
            return $q;
        }

        // set sort
        if (isset($query['sort_field']) && ($query['sort_field'] == 'customer_groups')) {
            $this->setSortField('cg.name');
        } elseif (isset($query['sort_field'])) {
            $this->setSortField($query['sort_field']);
        }

        isset($query['sort_type']) ? $this->setSortType($query['sort_type']) : null;

        // build sql
        foreach ($query as $key => $value) {
            foreach ($this->_fieldsSearch as $search) {
                switch (true) {
                    case Str::contains($key, 'customer_groups'):
                        $key = $this->_getKeyForQuery($key, 'cg.name_');
                        break;
                }
                if (Str::contains($key, $search)) {
                    $key = $this->getTableName() . '.' . $key;
                }
            }

            if (is_array($value)) {
                $this->_needWhereInOrNotIn($key, $value) ? $this->_buildInOrNotInConditions($key, $value) : $this->_buildConditions($key, $value);
                continue;
            }

            if (trim($value) !== '') {
                $this->_buildConditions($key, $value);
            }
        }

        $q = $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('payroll_customer_group as pcg', $this->getTableName() . '.id', '=', 'pcg.payroll_id')
            ->leftJoin('customer_group as cg', 'cg.id', '=', 'pcg.customer_group_id');
        $q = $q->groupBy($this->getTableName() . '.id')
            ->orderBy($this->getSortField(), $this->getSortType());
        return $q;
    }

    public function getPayrolls($request)
    {
        $customerId = $request['customerId'];
        $customerGroupIds = DB::table('customer')
            ->join(
                'customer_group_customer',
                'customer_group_customer.customer_id',
                '=',
                'customer.id'
            )
            ->where([
                ['customer.id', $customerId],
                ['customer_group_customer.del_flag', 0]
            ])
            ->get(['customer_group_customer.customer_group_id'])
            ->pluck('customer_group_id')->toArray();
        $query = Payroll::leftJoin('payroll_customer_group', 'payroll_customer_group.payroll_id', '=', 'payroll.id')
            ->where(function ($query) use ($customerGroupIds) {
                $query->whereIn('payroll_customer_group.customer_group_id', $customerGroupIds)
                    ->orWhere('payroll.isApplyAll', 1);
            })
            ->where(function ($query) {
                $query->where('code', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('name', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('description', 'LIKE', '%' . request('q') . '%');
            })->select(
                "payroll.id",
                "payroll.code as title",
                "payroll.name",
                "payroll.description"
            );
        $query = $query->where('payroll.del_flag', '=', '0')->distinct()
            ->orderBy('payroll.ins_date', 'desc')
            ->paginate(10);
        return $query;
    }

    public function getFormulas($priceQuoteId)
    {
        $query = DB::table('payroll_formula as pq')
            ->leftJoin(
                'location_group as lg1',
                'lg1.id',
                '=',
                'pq.location_group_destination_id'
            )
            ->leftJoin(
                'location_group as lg2',
                'lg2.id',
                '=',
                'pq.location_group_arrival_id'
            )
            ->leftJoin(
                'm_vehicle_group as vg',
                'vg.id',
                '=',
                'pq.vehicle_group_id'
            )
            ->where([
                ['pq.payroll_id', $priceQuoteId],
                ['pq.del_flag', 0]
            ])
            ->get([
                'pq.*',
                'lg1.title as name_of_location_group_destination_id',
                'lg2.title as name_of_location_group_arrival_id',
                'vg.name as name_of_vehicle_group_id'

            ]);
        return $query;
    }

    public function getCustomerGroups($priceQuoteId)
    {
        $query = DB::table('payroll_customer_group as pq')
            ->leftJoin(
                'customer_group as vg',
                'vg.id',
                '=',
                'pq.customer_group_id'
            )
            ->where([
                ['pq.payroll_id', $priceQuoteId],
                ['pq.del_flag', 0]
            ])
            ->get([
                'pq.*',
                'vg.name as name_of_customer_group_id'

            ]);
        return $query;
    }
}
