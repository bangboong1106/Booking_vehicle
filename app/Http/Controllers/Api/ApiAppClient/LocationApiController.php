<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Model\Entities\CustomerDefaultData;
use App\Model\Entities\Location;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CustomerRepository;
use App\Repositories\Client\LocationClientRepository;
use Input;
use function Complex\add;
use Illuminate\Http\Request;

class LocationApiController extends ClientApiController
{
    protected $locationRepos;
    protected $customerRepos;

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function __construct(CustomerRepository $customerRepository, LocationClientRepository $locationRepos)
    {
        parent::__construct($customerRepository);

        $this->setCustomerRepos($customerRepository);
        $this->setRepository($locationRepos);
    }

    public function delete(Request $request)
    {
        $id = Request::get('id');

        if (is_string($id)) {
            $id = explode(',', $id);
        }

        $isset = CustomerDefaultData::where('del_flag', 0)
                    ->whereIn('location_destination_ids', $id)
                    ->orWhereIn('location_arrival_ids', $id)
                    ->get()->toArray();

        if (count($isset) > 0) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => trans('messages.delete_location_failed')
            ]);
        }


        $location = Location::whereIn('id', $id);

        if (null == $location || empty($location)) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }

        $location->delete();

        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => null
        ]);
    }
}
