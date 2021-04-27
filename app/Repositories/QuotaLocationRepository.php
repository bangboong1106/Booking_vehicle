<?php

namespace App\Repositories;

use App\Model\Entities\QuotaLocation;
use App\Repositories\Base\CustomRepository;
use App\Validators\QuotaLocationValidator;

class QuotaLocationRepository extends CustomRepository
{
    function model()
    {
        return QuotaLocation::class;
    }

    public function validator()
    {
        return QuotaLocationValidator::class;
    }

    public function getLocations($quoteId)
    {
        if ($quoteId == null)
            return null;
        $locations = $this->search([
            'quota_id_eq' => $quoteId,
            'sort_type' => 'asc',
            'sort_field' => 'location_order'
        ], ['location_id', 'location_title'])->with(['location' => function($query) {
            return $query->with(['group' => function($query) {
                return $query->select(['id','title']);
            }]);
        }])->get();
        return $locations;
    }
}