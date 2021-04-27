<?php

namespace App\Repositories;

use App\Model\Entities\Location;
use App\Model\Entities\Order;
use App\Model\Entities\Routes;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\OrderLocation;
use App\Model\Entities\RouteLocation;


use App\Repositories\Base\CustomRepository;
use App\Validators\LocationValidator;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LocationRepository extends CustomRepository
{
    /**
     * @return string
     */
    function model()
    {
        return Location::class;
    }

    /**
     * @return null|string
     */
    public function validator()
    {
        return LocationValidator::class;
    }

    public function getListForSelect()
    {
        return Location::select(DB::raw('CONCAT(code, "|", title) AS title, id'))
            ->where('del_flag', '=', 0)
            ->pluck('title', 'id');
    }

    /**
     * @param $q
     * @return array
     */
    public function getLocationsByCustomer($q, $customerID)
    {
        $params = ['title', 'address', 'address_auto_code'];

        $tableName = $this->getTableName();
        $query = $this->search([
            'sort_type' => 'desc',
            'sort_field' => 'is_allow_update'
        ]);

        foreach ($params as $field) {
            $query->orWhere($tableName . '.' . $field, 'LIKE', '%' . $q . '%');
        }
        $query = $query
            ->leftJoin('admin_users', 'locations.ins_id', '=', 'admin_users.id')
            ->select(
                'locations.*',
                DB::raw("IF(locations.ins_id = $customerID ,TRUE,FALSE) as is_allow_update")
            )
            ->with(['group'])
            ->paginate(10);

        $response = [
            'total' => $query->total(),
            'data' => $query
        ];
        return $response;
    }

    public function getLocationsByIds($ids = [])
    {
        if (empty($ids)) {
            return [];
        }

        return $this->search(['id_in' => $ids])->pluck('title', 'id');
    }

    public function getLocationsById($ids)
    {
        if ($ids) {
            return DB::table('locations')
                ->leftJoin('m_province', 'locations.province_id', '=', 'm_province.province_id')
                ->leftJoin('m_district', 'locations.district_id', '=', 'm_district.district_id')
                ->leftJoin('m_ward', 'locations.ward_id', '=', 'm_ward.ward_id')
                ->select(
                    'locations.*',
                    DB::raw('m_province.title as province_title'),
                    DB::raw('m_district.title as district_title'),
                    DB::raw('m_ward.title as ward_title'),
                    DB::raw('m_ward.location as ward_location'),
                    DB::raw('m_district.location as district_location')
                )
                ->where('locations.id', '=', $ids)->first();
        }
        return null;
    }

    public function getLocationsByCodes($codes = [])
    {
        if (empty($codes)) {
            return [];
        }

        return $this->search(['code_in' => $codes])->pluck('title', 'code');
    }

    public function getLocationByCode($code)
    {
        if ($code) {
            return $this->search([
                'code_eq' => $code,
                'del_flag' => '0'
            ])->first();
        }
        return null;
    }

    public function getLocation($address_auto_code, $addressTitle)
    {
        if ($address_auto_code && $addressTitle) {
            return $this->search([
                'address_auto_code_eq' => $address_auto_code,
                'address_eq' => $addressTitle
            ])->first();
        }

        return null;
    }

    /**
     * @param $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with(['ward', 'province', 'district', 'customer']);
    }

    public function existLocationName($title)
    {
        if ($title) {
            $entity = $this->search([
                'title_eq' => $title
            ])->first();
            if ($entity != null) {
                return true;
            }
        }
        return false;
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 03/09/2020
    protected function getKeyValue()
    {
        return [
            'province_name' => [
                'filter_field' => 'p.title',
            ],
            'district_name' => [
                'filter_field' => 'd.title',
            ],
            'ward_name' => [
                'filter_field' => 'w.title',
            ],
            'name_of_customer' => [
                'filter_field' => 'c.full_name',
            ]
        ];
    }

    // Hàm build câu lệnh địa điểm
    // CreatedBy nlhoang 03/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('m_province as p', $this->getTableName() . '.province_id', '=', 'p.province_id')
            ->leftJoin('m_district as d', $this->getTableName() . '.district_id', '=', 'd.district_id')
            ->leftJoin('m_ward as w', $this->getTableName() . '.ward_id', '=', 'w.ward_id')
            ->leftJoin('customer as c', $this->getTableName() . '.customer_id', '=', 'c.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function findByFullAddress($location)
    {
        return $this->search([
            'full_address_eq' => $location,
            'del_flag' => '0'
        ])->get()->first();
    }

    public function findAddress($location)
    {
        $query = Location:: where('locations.del_flag', '=', '0')
            ->where(function ($q) use ($location) {
                $q->where('locations.title', '=', $location)
                    ->orWhere('locations.address', '=', $location)
                    ->orWhere('locations.full_address', '=', $location);
            });

        return $query->first();
    }

    public function _isUsed($id)
    {
        $orderCustomer = DB::table('order_customer')
            ->where('order_customer.location_destination_id', '=', $id)
            ->orWhere('order_customer.location_arrival_id', '=', $id)
            ->where("order_customer.del_flag", '=', '0')
            ->first();
        if ($orderCustomer) {
            return true;
        }

        $customerDefaultData = DB::table('customer_default_data')
            ->where('customer_default_data.location_destination_id', '=', $id)
            ->orWhere('customer_default_data.location_arrival_id', '=', $id)
            ->where("customer_default_data.del_flag", '=', '0')
            ->first();
        if ($customerDefaultData) {
            return true;
        }

        $query = DB::table('order_locations')
            ->leftJoin("orders", "order_locations.order_id", "=", "orders.id")
            ->where('order_locations.location_id', '=', $id)
            ->where("orders.del_flag", '=', '0');
        $orderLocation = $query->first();

        if ($orderLocation) {
            return true;
        }

        return false;
    }

    /**
     * @param $query
     * @return LengthAwarePaginator
     */
    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->with(['district', 'ward', 'province'])->paginate($limit, ['*'], 'page', 1);
    }

    public function getItemsForSheet($userID)
    {
        return Location::where('del_flag', '=', 0)
            ->orderBy('title')
            ->get([
                DB::raw('CONCAT(code," ", title) as name'),
                'id'
            ]);
    }

    // Xử lý gộp trùng địa điểm
    //CreatedBy nlhoang 30/09/2020
    public function processDeduplicate($sourceID, $destinationIDs)
    {
        Order::whereIn('location_destination_id', $destinationIDs)
            ->update([
                'location_destination_id' => $sourceID
            ]);

        Order::whereIn('location_arrival_id', $destinationIDs)
            ->update([
                'location_arrival_id' => $sourceID
            ]);

        DB::table('order_locations')->whereIn('location_id', $destinationIDs)
            ->update([
                'location_id' => $sourceID
            ]);

        Routes::whereIn('location_destination_id', $destinationIDs)
            ->update([
                'location_destination_id' => $sourceID
            ]);

        Routes::whereIn('location_arrival_id', $destinationIDs)
            ->update([
                'location_arrival_id' => $sourceID
            ]);

        OrderLocation::whereIn('location_id', $destinationIDs)
            ->update([
                'location_id' => $sourceID
            ]);
        OrderCustomer::whereIn('location_destination_id', $destinationIDs)
            ->update([
                'location_destination_id' => $sourceID
            ]);

        OrderCustomer::whereIn('location_arrival_id', $destinationIDs)
            ->update([
                'location_arrival_id' => $sourceID
            ]);
        Location::whereIn('id', $destinationIDs)->delete();
    }
}
