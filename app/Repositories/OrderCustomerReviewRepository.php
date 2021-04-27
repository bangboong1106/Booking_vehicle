<?php

namespace App\Repositories;

use App\Model\Entities\OrderCustomerReview;
use App\Repositories\Base\CustomRepository;
use App\Validators\OrderCustomerReviewValidator;

class OrderCustomerReviewRepository extends CustomRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return OrderCustomerReview::class;
    }

    public function validator()
    {
        return OrderCustomerReviewValidator::class;
    }
}