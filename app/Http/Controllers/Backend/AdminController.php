<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\File;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerGroupRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\RoleRepository;
use App\Repositories\VehicleTeamRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class AdminController
 * @package App\Http\Controllers\Backend
 */
class AdminController extends BackendController
{
    protected $_fileRepository;
    protected $_driverRepository;
    protected $_roleRepository;
    protected $_vehicleTeamRepository;
    protected $_customerGroupRepository;
    protected $_partnerRepository;

    /**
     * @return VehicleTeamRepository
     */
    public function getVehicleTeamRepository()
    {
        return $this->_vehicleTeamRepository;
    }

    /**
     * @param mixed $vehicleTeamRepository
     */
    public function setVehicleTeamRepository($vehicleTeamRepository): void
    {
        $this->_vehicleTeamRepository = $vehicleTeamRepository;
    }

    /**
     * @return FileRepository
     */
    public function getFileRepository()
    {
        return $this->_fileRepository;
    }

    /**
     * @param mixed $fileRepository
     */
    public function setFileRepository($fileRepository): void
    {
        $this->_fileRepository = $fileRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param mixed $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return RoleRepository
     */
    public function getRoleRepository()
    {
        return $this->_roleRepository;
    }

    /**
     * @param mixed $roleRepository
     */
    public function setRoleRepository($roleRepository): void
    {
        $this->_roleRepository = $roleRepository;
    }

    /**
     * @return CustomerGroupRepository
     */
    public function getCustomerGroupRepository()
    {
        return $this->_customerGroupRepository;
    }

    /**
     * @param mixed $customerGroupRepository
     */
    public function setCustomerGroupRepository($customerGroupRepository): void
    {
        $this->_customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->_partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->_partnerRepository = $partnerRepository;
    }


    /**
     * AdminController constructor.
     * @param AdminUserInfoRepository $adminUserInfoRepository
     * @param FileRepository $fileRepository
     * @param DriverRepository $driverRepository
     * @param RoleRepository $roleRepository
     * @param VehicleTeamRepository $vehicleTeamRepository
     * @param CustomerGroupRepository $customerGroupRepository
     * @param PartnerRepository $partnerRepository
     */
    public function __construct(AdminUserInfoRepository $adminUserInfoRepository,
                                FileRepository $fileRepository, DriverRepository $driverRepository,
                                RoleRepository $roleRepository, VehicleTeamRepository $vehicleTeamRepository,
                                CustomerGroupRepository $customerGroupRepository,PartnerRepository $partnerRepository)
    {
        parent::__construct();
        $this->setRepository($adminUserInfoRepository);
        $this->setBackUrlDefault('admin.index');
        $this->setConfirmRoute('admin.confirm');
        $this->setMenu('management');
        $this->setTitle('Quản trị');

        $this->setFileRepository($fileRepository);
        $this->setDriverRepository($driverRepository);
        $this->setRoleRepository($roleRepository);
        $this->setVehicleTeamRepository($vehicleTeamRepository);
        $this->setCustomerGroupRepository($customerGroupRepository);
        $this->setPartnerRepository($partnerRepository);

        $this->setAuditing(true);
        $this->setDeleted(true);
    }

    public function index()
    {
        $this->_prepareForm();
        return parent::index();
    }

    public function profile()
    {
        $currentUser = $this->getCurrentUser();
        $params = $this->_getParams();
        $this->_setFormData($params);
        $currentUser->listRole = $currentUser->getRoleNames()->toArray();
        $currentUser->listVehicleTeam = $currentUser->vehicleTeams->pluck('id')->toArray();
        $currentUser->listCustomerGroup = $currentUser->customerGroups->pluck('id')->toArray();
        $vehicleTeamList = $this->getVehicleTeamRepository()->getListForSelect();
        $customerGroupList = $this->getCustomerGroupRepository()->getListForSelect();

        if (request()->isMethod('post')) {
            $params['username'] = $currentUser->username;
            $backUrl = request()->get('backUrl', null);
            if ($this->_emptyFormData()) {
                return $this->_to('admin.profile')->withErrors(trans('messages.update_failed'));
            }
            $isValid = $this->getRepository()->getValidator()->validateUpdate($params);
            if (!$isValid) {
                return $this->_to('admin.profile')->withErrors($this->getRepository()->getValidator()->errors());
            }

            DB::beginTransaction();
            try {
                $entity = $this->_findEntityForUpdate($currentUser->id);
                $this->fireEvent('before_update', $entity);
                $this->_moveFileFromTmpToMedia($entity);

                $entity->save();
                DB::commit();
                return $this->_to('admin.profile', ['_o' => $backUrl])->with('success', trans('messages.update_success'));
            } catch (Exception $e) {
                logError($e);
                $this->_removeMediaFile(isset($entity) ? $entity : null);
                DB::rollBack();
            }
            return $this->_to('admin.profile')->withErrors(trans('messages.update_failed'));
        }
        $this->setViewData([
            "vehicleTeamList" => $vehicleTeamList,
            "customerGroupList" => $customerGroupList,
            "entity" => $currentUser
        ]);
        return $this->render();
    }

    public function edit($id)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser->can('edit admin') && $currentUser->id !== (int)$id) {
            return $this->_redirectToHome()->send();
        }
        $prepare = $this->_prepareEdit($id);
        $this->_prepareAfterSetEntity($prepare);
        return $prepare instanceof RedirectResponse ? $prepare : $this->render();
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    protected function _findEntityForUpdate($id)
    {
        $entity = parent::_findEntityForUpdate($id);
        empty($entity->password) ? $entity->setPasswordAttribute($entity->getOriginal('password')) : null;

        if (!empty($entity->avatar_id)) {
            return $entity;
        }

        $originalAvatarId = $entity->getOriginal('avatar_id');
        if (empty($originalAvatarId)) {
            return $entity;
        }

        /** @var File $file */
        $file = $this->getFileRepository()->getFileWithID($originalAvatarId);
        if (!empty($file)) {
            $file->delete();
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        }

        return $entity;
    }

    protected function _prepareForm()
    {
        $this->setViewData([
            'roles' => $this->getRoleRepository()->getAllRole(),
            'rolePartners' => $this->getRoleRepository()->getAllRolePartner(),
            'vehicleTeamList' => $this->getVehicleTeamRepository()->getListForSelect(),
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'partnerList' => $this->getPartnerRepository()->getListForSelect()
        ]);
    }

    protected function _moveFileFromTmpToMedia(&$entity)
    {
        if (empty($entity->avatar_id)) {
            return;
        }
        app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($entity->avatar_id, 'avatars');
    }

    /**
     * @param $entity AdminUserInfo
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);

        if ($this->_checkPermission('edit')) {
            $roles = $entity->listRole;
            $entity->syncRoles($roles);
        }
        $entity->vehicleTeams()->sync($entity->listVehicleTeam);
        $entity->customerGroups()->sync($entity->listCustomerGroup);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $this->setViewData([
            'show_history' => false,
        ]);
        $this->_prepareForm();
    }

    protected function _prepareShow($id)
    {
        $this->_prepareForm();
        $this->setViewData([
            'show_history' => true,
        ]);
        $parent = parent::_prepareShow($id);
        /** @var AdminUserInfo $entity */
        $entity = $this->getEntity();
        $entity->listRole = $entity->getRoleNames()->toArray();
        $entity->listVehicleTeam = $entity->vehicleTeams->pluck('id');
        $entity->listCustomerGroup = $entity->customerGroups->pluck('id');
        return $parent;
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $currentListVehicleTeam = $entity->vehicleTeams->pluck('id')->toArray();
        $entity->listVehicleTeam = empty($entity->listVehicleTeam) ? $currentListVehicleTeam : $entity->listVehicleTeam;

        $currentListCustomerGroup = $entity->customerGroups->pluck('id')->toArray();
        $entity->listCustomerGroup = empty($entity->listCustomerGroup) ? $currentListCustomerGroup : $entity->listCustomerGroup;

        $this->setViewData([
            'currentListVehicleTeam' => $currentListVehicleTeam,
            'currentListCustomerGroup' => $currentListCustomerGroup
        ]);
        $this->setEntity($entity);
        return $parent;
    }
}
