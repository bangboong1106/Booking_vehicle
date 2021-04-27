<?php

namespace App\Repositories;

use App\Model\Entities\VersionReview;
use App\Repositories\Base\CustomRepository;
use DB;

class VersionReviewRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return VersionReview::class;
    }

    public function getCurrentVersionReview()
    {
        return $this->search([
            'reviewed_eq' => '0',
            'del_flag_eq' => '0'
        ])->first();
    }
}