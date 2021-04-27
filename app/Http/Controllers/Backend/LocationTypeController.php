<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\LocationType;
use App\Repositories\CustomerRepository;
use App\Repositories\LocationTypeRepository;
use Illuminate\Support\Facades\Request;

/**
 * Class LocationTypeController
 * @package App\Http\Controllers\Backend
 */
class LocationTypeController extends BackendController
{
    protected $customerRepository;

    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    public function setCustomerRepository($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function __construct(LocationTypeRepository $locationTypeRepository, CustomerRepository $customerRepository) {
        parent::__construct();
        $this->setRepository($locationTypeRepository);
        $this->setBackUrlDefault('location-type.index');
        $this->setConfirmRoute('location-type.confirm');
        $this->setMenu('location-type');
        $this->setTitle(trans('models.location_type.name'));
        $this->setMenu('category');
        $this->setCustomerRepository($customerRepository);
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'customers' => $this->getCustomerRepository()->search()->get()
        ]);
    }

    public function getDataForComboBox()
    {
        $customerId = Request::get('c_id', -1);

        $query = LocationType::select('*')
            ->where(function ($query) {
                $query->where('code', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('title', 'LIKE', '%' . request('q') . '%');
            });

        if ($customerId > 0) {
            $query = $query->where('customer_id', $customerId);
        }

        $query = $query->orderBy('code', 'asc')->paginate(10);

        $data = $query->toArray();
        $items = [];
        foreach ($query as $location) {
            $items[] = [
                'id' => $location->id,
                'title' => $location->title,
                'code' => $location->code
            ];
        }

        $response = [
            'items' => $items,
            'pagination' => $query->nextPageUrl() ? true : false
        ];

        return response()->json($response);
    }

    public function getDataForSelect()
    {
        $customerId = Request::get('customer_id', -1);

        if ($customerId > 0) {
            $locationType =  $this->getRepository()->search([
                'customer_id_eq' => $customerId
            ])->get()->pluck('title', 'id');

            $this->setViewData(['locationType' => $locationType]);
            $html = [
                'content' => $this->render('backend.location._location_type_list')->render()
            ];

            $this->setData($html);
            return $this->renderJson();
        }

        return $this->getRepository()->search()->get()->pluck('title', 'id');
    }
}