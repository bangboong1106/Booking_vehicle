<?php

namespace App\Http\Supports\Supports;

/**
 * Trait Ajax
 * @package App\Http\Controllers\Backend\Concerns;
 */

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Trait HasAppType
 * @package App\Http\Controllers\Backend\Concerns
 */
trait AjaxHandle
{
    protected $_hasGetParams = false;

    /**
     * @return bool
     */
    public function isHasGetParams()
    {
        return $this->_hasGetParams;
    }

    /**
     * @param bool $hasGetParams
     */
    public function setHasGetParams($hasGetParams)
    {
        $this->_hasGetParams = $hasGetParams;
    }

    public function create()
    {
        $this->setEntity($this->_findOrNewEntity(null, true));
        $html = [
            'modalForm'=> $this->render('backend.'.$this->getCurrentControllerName().'._modal_form')->render(),
        ];
        $this->setData($html);
        return $this->renderJson();
    }

    public function store()
    {
        $params = $this->_getParams();
        $valid = $this->getRepository()->getValidator()->validateCreate($params);
        if (!$valid) {
            $this->setMessage($this->getRepository()->getValidator()->errors());
            return $this->renderErrorJson();
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $entity->save();
            $this->_saveRelations($entity);
            // add new
            DB::commit();
            $this->fireEvent('after_store', $entity);
            $this->setSuccessFlash(trans('messages.create_success'));
            $this->setMessage(trans('messages.create_success'));
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
        $this->setMessage(trans('messages.create_failed'));
        return $this->renderErrorJson();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id) {
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            $this->setMessage($this->getRepository()->getValidator()->errors());
            return $this->renderErrorJson();
        }
        $entity = $this->_findEntityForUpdate($id);
        $this->setEntity($entity);
        $html = [
            'modalForm'=> $this->render('backend.'.$this->getCurrentControllerName().'._modal_form')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }


    /**
     * @param $id
     * @return $this
     */
    public function update($id)
    {
        $params = $this->_getParams();
        $isValid = $this->getRepository()->getValidator()->validateUpdate($params);
        if (!$isValid) {
            $this->setMessage($this->getRepository()->getValidator()->errors());
            return $this->renderErrorJson();
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $entity->save();
            // fire after save
            // fire before save relation
            $this->_saveRelations($entity);
            // fire after save relation
            // add new
            DB::commit();
            $this->fireEvent('after_update', $entity);
            $this->setSuccessFlash(trans('messages.update_success'));
            $this->setMessage(trans('messages.update_success'));
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
        $this->setMessage(trans('messages.update_failed'));
        return $this->renderErrorJson();
    }

    /**
     * @param $id
     * @param $action
     * @return $this
     */
    public function destroy($id, $action = 'delete')
    {
        $isValid = $this->getRepository()->getValidator()->validateDestroy($id);
        if (!$isValid) {
            $this->setMessage($this->getRepository()->getValidator()->errors());
            return $this->renderErrorJson();
        }
        DB::beginTransaction();
        try {
            $entity = $this->getRepository()->find($id);
            $this->fireEvent('before_destroy', $entity);
            call_user_func_array([$entity, $action], []);
            $this->_saveRelations($entity, $action);
            DB::commit();
            $this->fireEvent('after_destroy', $entity);
            $this->setMessage(trans('messages.delete_success'));
            return $this->renderJson();
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
        $this->setMessage(trans('messages.delete_failed'));
        return $this->renderErrorJson();
    }


    /**
     * @param bool $toObject
     * @param bool $clean
     * @return mixed
     */
    protected function _getFormData($toObject = true, $clean = false)
    {
        $data = $this->_getParams();
        return $toObject ? $this->getRepository()->findFirstOrNew($data) : $data;
    }

    /**
     * @return mixed
     */
    protected function _getParams()
    {
        $params = parent::_getParams();
        if ($this->isHasGetParams() || App::environment('production') || request()->isXmlHttpRequest()) {
            return $params;
        }
        $this->setHasGetParams(true);
        return $params;
    }

    public function getList()
    {
        $query = Request::all();
        $entities = $this->getRepository()->getListForBackend($query);
        $entities->setPath(route($this->getCurrentControllerName() . '.index'));
        $this->setEntities($entities);
        $html = [
            'data'=> $this->render('backend.'.$this->getCurrentControllerName().'._list')->render(),
            'paginator'=> $this->render('layouts.backend.elements.paginator')->render(),
            'paginatorInfo'=> $this->render('layouts.backend.elements.pagination_info')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }
}