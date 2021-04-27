<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:10
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\CustomerGroup;
use App\Repositories\CustomerGroupRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Request;


class CustomerGroupController extends BackendController
{
    protected $_fieldsSearch = ['name'];
    private $_customerRepository;

    /**
     * @return CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param mixed $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    public function __construct(CustomerGroupRepository $customerGroupRepository, CustomerRepository $customerRepository)
    {
        parent::__construct();
        $this->setRepository($customerGroupRepository);
        $this->setBackUrlDefault('customer-group.index');
        $this->setConfirmRoute('customer-group.confirm');
        $this->setMenu('customer');
        $this->setTitle(trans('models.customer_group.name'));
        $this->setCustomerRepository($customerRepository);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $this->_prepareEntity($this->getEntity());
        $this->setViewData([
            'show_history' => false
        ]);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $this->setViewData([
            'show_history' => true
        ]);
        $this->_prepareEntity($this->getEntity());
    }

    /**
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $entity->customers()->sync($entity->customer_ids);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);

        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_customer_group'));
            }
        }

        $customers = null;
        if (array_key_exists('customer_ids', $attributes)) {
            $customers = $this->getCustomerRepository()->getCustomerCodeByIds($attributes['customer_ids'])->pluck('id');
        }

        $this->setViewData([
            'code' => $code,
            'customers' => $customers
        ]);
    }

    /**
     * @return BackendController
     */
    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $this->_prepareEntity($this->getEntity());
        return $parent;
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $this->_prepareEntity($this->getEntity());
        return $parent;
    }

    protected function _prepareEntity($entity)
    {
        if (empty($entity->customer_ids)) {
            return;
        }
        $customers = $this->getCustomerRepository()->search(['id_in' => $entity->customer_ids])->get();
        $entity->setRelation('customers', $customers);
        $this->setEntity($entity);
    }

    protected function _deleteRelations($entity)
    {
        $adminUsersCustomerGroups = $this->getRepository()->getAdminUsersCustomerGroups($entity->id);
        if ($adminUsersCustomerGroups) {
            foreach ($adminUsersCustomerGroups as $item) {
                $item->delete();
            }
        }

        $customerGroupCustomers = $this->getRepository()->getCustomerGroupCustomers($entity->id);
        if ($customerGroupCustomers) {
            foreach ($customerGroupCustomers as $item) {
                $item->delete();
            }
        }
    }

    public function getDataForComboBox()
    {
        $all = Request::get('all');
        $q = Request::get('q');
        $currentUser = $this->getCurrentUser();
        $query = $this->getRepository()->getItemsByUserID($all, $q, $currentUser->id);
        return response()->json(['items' => $query->toArray()['data'], 'pagination' => $query->nextPageUrl() ? true : false]);

    }
}