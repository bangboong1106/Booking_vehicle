<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ContractRepository;
use App\Repositories\ContractTypeRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\FileRepository;
use Carbon\Carbon;

/**
 * Class ContractController
 * @package App\Http\Controllers\Backend
 */
class ContractController extends BackendController
{
    protected $_customerRepository;
    protected $_fileRepository;
    protected $_contractTypeRepository;

    /**
     * @return mixed
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

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getContractTypeRepository()
    {
        return $this->_contractTypeRepository;
    }

    /**
     * @param mixed $contractTypeRepository
     */
    public function setContractTypeRepository($contractTypeRepository): void
    {
        $this->_contractTypeRepository = $contractTypeRepository;
    }

    /**
     * ContractController constructor.
     * @param ContractRepository $contractRepository
     * @param CustomerRepository $customerRepository
     * @param FileRepository $fileRepository
     * @param ContractTypeRepository $contractTypeRepository
     */
    public function __construct(ContractRepository $contractRepository, CustomerRepository $customerRepository,
                                FileRepository $fileRepository, ContractTypeRepository $contractTypeRepository)
    {
        parent::__construct();
        $this->setRepository($contractRepository);
        $this->setBackUrlDefault('contract.index');
        $this->setConfirmRoute('contract.confirm');
        $this->setMenu('customer');
        $this->setTitle(trans('models.contract.name'));

        $this->setCustomerRepository($customerRepository);
        $this->setFileRepository($fileRepository);
        $this->setContractTypeRepository($contractTypeRepository);
    }

    public function _prepareForm()
    {
        $customers = $this->getCustomerRepository()->getForSelect();
        $contractTypes = $this->getContractTypeRepository()->getAll();
        $this->setViewData([
            'customers' => $customers,
            'contractTypes' => $contractTypes
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData()->getAttributes();
        $str_files = null;
        if (array_key_exists('file_id', $attributes)) {
            $str_files = $attributes['file_id'];
        } else {
            $entity = $this->getRepository()->getContractWithID($id);
            if ($entity != null)
                $str_files = $entity->file_id;
        }
        $this->setFileListForFormData($str_files);
    }

    protected function _prepareConfirm()
    {
        parent::_prepareConfirm();
        $attributes = $this->_getFormData()->getAttributes();
        $this->setFileListForFormData($attributes['file_id']);
    }

    protected function _prepareShow($id)
    {
        parent::_prepareShow($id);
        $entity = $this->getRepository()->getContractWithID($id);
        $str_files = null;
        if ($entity != null)
            $str_files = $entity->file_id;
        $this->setFileListForFormData($str_files);
    }

    public function setFileListForFormData($str_files)
    {
        $file_list = [];
        if (!empty($str_files)) {
            $file_id_list = explode(';', $str_files);
            if (!empty($file_id_list)) {
                foreach ($file_id_list as $file_id) {
                    $file = $this->getFileRepository()->getFileWithID($file_id);
                    if ($file != null)
                        $file_list[] = $file;
                }
            }
        }
        $this->setViewData([
            'file_list' => $file_list,
        ]);
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = parent::_findEntityForUpdate($id);
        if (!empty($entity->file_id)) {
            $file_id_list = explode(';', $entity->file_id);
            if (!empty($file_id_list)) {
                foreach ($file_id_list as $file_id) {
                    app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($file_id, 'contracts');
                }
            }
        }
        return $this->_processInputData($entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        if (!empty($entity->file_id)) {
            $file_id_list = explode(';', $entity->file_id);
            if (!empty($file_id_list)) {
                foreach ($file_id_list as $file_id) {
                    app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($file_id, 'contracts');
                }
            }
        }
        return $this->_processInputData($entity);
    }

    protected function _processInputData($entity)
    {
        empty($entity->issue_date) ? null : $entity->issue_date = Carbon::createFromFormat('d-m-Y', $entity->issue_date)->format('Y-m-d');
        empty($entity->expired_date) ? null : $entity->expired_date = Carbon::createFromFormat('d-m-Y', $entity->expired_date)->format('Y-m-d');

        return $entity;
    }

}
