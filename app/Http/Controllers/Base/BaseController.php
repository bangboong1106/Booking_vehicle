<?php

namespace App\Http\Controllers\Base;

use App\Events\BaseEvent;
use App\Helpers\Url;
use App\Helpers\Facades\CustomStorage;
use App\Http\Controllers\Controller;

use App\Http\Supports\ApiResponse;
use App\Http\Supports\Events;
use App\Model\Base\Base;
use App\Repositories\Base\CustomRepository;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

/**
 * Class BaseController
 * @package App\Http\Controllers\Base
 */
class BaseController extends Controller
{
    use BaseEvent;
    use ApiResponse;
    use Events;

    /**
     * @var string
     */
    protected $_confirmRoute = '';
    /**
     * @var null
     */
    protected $_repository = null;
    /**
     * @var string
     */
    protected $_backUrlDefault = '';
    /**
     * @var bool
     */
    protected $_isHome = false;
    /**
     * @var array
     */
    protected $_statics = array();
    /**
     * @var string
     */
    protected $_title = null;

    protected $_siteDescription = '';

    protected $_siteImage = '';

    protected $_viewData = array();
    /**
     * @var string
     */
    protected $_area = '';

    protected $_menu = '';

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        try {
            $this->setSiteDescription(config('app.description'));
            $this->setSiteImage(config('app.image'));
            Url::setCurrentControllerName($this->getCurrentControllerName());
            $this->setEventPrefix(getConstant('EVENT_CONTROLLER_TYPE', 'controller'));
            $this->setEventSuffix($this->getArea() . '.' . $this->getCurrentControllerName());
        } catch (\Exception $e) {
            logError($e);
        }
    }

    /**
     * @return string
     */
    public function getSiteDescription()
    {
        return $this->_siteDescription;
    }

    /**
     * @param string $siteDescription
     */
    public function setSiteDescription($siteDescription)
    {
        $this->_siteDescription = $siteDescription;
    }

    /**
     * @return string
     */
    public function getSiteImage()
    {
        return $this->_siteImage;
    }

    /**
     * @param string $siteImage
     */
    public function setSiteImage($siteImage)
    {
        $this->_siteImage = $siteImage;
    }


    /**
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * @param string $area
     */
    public function setArea($area)
    {
        $this->_area = $area;
    }


    /**
     * @return array
     */
    public function getViewData($key = null)
    {
        if ($key) {
            return Arr::get($this->_viewData, $key);
        }
        return $this->_viewData;
    }

    /**
     * @param array $viewData
     * @return $this
     */
    public function setViewData($viewData)
    {
        $this->_viewData = array_merge($this->getViewData(), (array)$viewData);
        return $this;
    }


    /**
     * @return string
     */
    public function getConfirmRoute()
    {
        return $this->_confirmRoute;
    }

    /**
     * @param string $confirmRoute
     */
    public function setConfirmRoute($confirmRoute)
    {
        $this->_confirmRoute = $confirmRoute;
    }


    /**
     * @return string
     */
    public function getCurrentRouteName()
    {
        return Route::currentRouteName();
    }

    /**
     * @return \Illuminate\Routing\Route
     */
    public function getCurrentRoute()
    {
        return Route::current();
    }

    /**
     * @return mixed
     */
    public function getCurrentController()
    {
        $cA = $this->_getControllerAction();
        return $cA['controller'];
    }

    /**
     * @return array
     */
    protected function _getControllerAction()
    {
        $currentAction = Route::currentRouteAction();
        list($controller, $action) = is_null($currentAction) ? [null, null] : explode('@', $currentAction);
        return [
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * @param bool $toUnder
     * @return mixed|string
     */
    public function getCurrentControllerName($toUnder = true)
    {
        $controller = explode('\\', $this->getCurrentController());
        $controller = empty($controller[4]) ? '' : str_replace('Controller', '', $controller[4]);
        return $toUnder ? Str::snake($controller) : $controller;
    }

    /**
     * @param bool $toUnder
     * @return mixed|string
     */
    public function getCurrentAction($toUnder = true)
    {
        $cA = $this->_getControllerAction();
        $action = $cA['action'];
        return $toUnder ? toUnderScore($action) : $action;
    }

    /**
     * @return CustomRepository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    public function setRepository($repository)
    {
        $this->_repository = $repository;
        $this->setModel($repository->getModel());
        return $this;
    }

    public function setModel($model)
    {
        $this->setViewData(['model' => $model]);
    }

    /**
     * @return bool
     */
    public function isHome()
    {
        return $this->_isHome;
    }

    /**
     * @param bool $isHome
     */
    public function setIsHome($isHome)
    {
        $this->_isHome = $isHome;
    }

    /**
     * @return string
     */
    public function getBackUrlDefault()
    {
        return $this->_backUrlDefault;
    }

    /**
     * @param string $backUrlDefault
     */
    public function setBackUrlDefault($backUrlDefault)
    {
        $this->_backUrlDefault = $backUrlDefault;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title . $this->getTitle();
    }

    /**
     * @return array
     */

    public function getStatics()
    {
        return $this->_statics;
    }
    //

    /**
     * @param array $statics
     */
    public function setStatics($statics)
    {
        $this->_statics = $statics;
    }

    /**
     * @return string
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * @param string $menu
     */
    public function setMenu($menu)
    {
        $this->_menu = $menu;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->setViewData(array('entity' => $entity));
        return $this;
    }

    /**
     * @return object  | Base
     */
    public function getEntity()
    {
        return $this->getViewData('entity');
    }

    /**
     * @param $entities
     * @return $this
     */
    public function setEntities($entities)
    {
        $this->setViewData(array('entities' => $entities));
        return $this;
    }

    /**
     * @param $file
     * @return BaseController
     */
    protected function _pushCss($file)
    {
        return $this->_pushStaticFile($file, 'css');
    }

    /**
     * @param $file
     * @return BaseController
     */
    protected function _pushJs($file)
    {
        return $this->_pushStaticFile($file, 'js');
    }

    /**
     * @param $file
     * @param string $type
     * @return $this
     */
    protected function _pushStaticFile($file, $type = 'css')
    {
        $statics = $this->getStatics();
        $files = isset($statics[$type]) ? $statics[$type] : array();
        $files[] = $file;
        $statics[$type] = $files;
        $this->setStatics($statics);
        return $this;
    }

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return mixed
     */
    public function render($view = null, $data = [], $mergeData = [])
    {
        $view = $view ? $view : $this->getArea() . '.' . $this->getCurrentControllerName() . '.' . $this->getCurrentAction() . $view;
        $actionName = $this->getCurrentAction();
        $routeName = $this->getCurrentRouteName();
        $routePrefix = str_replace('.' . $this->getCurrentAction(false), '', $routeName);

        $data += array(
            'title' => $this->getTitle(),
            'statics' => $this->getStatics(),
            'isHome' => $this->_isHome,
            '_form' => $this->getArea() . '.' . $this->getCurrentControllerName() . '._form',
            'controllerName' => $this->getCurrentControllerName(true),
            'actionName' => $actionName,
            'routeName' => $routeName,
            'routePrefix' => $routePrefix,
            'area' => $this->getArea(),
            'siteDescription' => $this->getSiteDescription(),
            'siteImage' => $this->getSiteImage(),
            'menu' => $this->getMenu()
        );

        $data += $this->getViewData();
        $this->fireEvent('before_render', $data);
        $response = view($view, $data, $mergeData);
        $this->fireEvent('after_render', $response);
        return $response;

    }

    protected function _getAttributeNames()
    {
        return (array)Lang::get('models.' . $this->getRepository()->getModel()->getAlias() . '.attributes');
    }

    public function getEventName($name)
    {
        return getEventName(getConstant('EVENT_CONTROLLER_TYPE') . '.' . $name);
    }

    /**
     *
     */
    public function detectCurrentPage()
    {
        $currentPage = Request::get('page', getConstant('default_page'));
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
    }

    /**
     * @return bool
     */
    public function getCurrentUser()
    {
        return null;
    }

    /**
     *
     */
    public function index()
    {
    }

    /**
     * @param $id
     */
    public function show($id)
    {
    }

    /**
     *
     */
    public function store()
    {
    }

    /**
     *
     */
    public function valid()
    {

    }

    /**
     *
     */
    public function confirm()
    {
    }

    /**
     *
     */
    public function create()
    {

    }

    /**
     * @param $id
     */
    public function edit($id)
    {

    }

    /**
     * @param $id
     */
    public function update($id)
    {

    }

    /**
     * @param $action
     * @param $id
     */
    public function destroy($id, $action = 'delete')
    {

    }

    /**
     * @param string $action
     * @return mixed
     */
    public function massDestroy($action = 'delete')
    {
        try {
            $ids = array_unique(array_filter(explode(',', Request::get('id', ''))));
            if (empty($ids)) {
                return $this->_backToStart()->withErrors(trans('messages.delete_failed'));
            }
            //Neu doi tuong da duoc su dung thì ko xoa
            // foreach ($ids as $id) {
            //     if (!$this->getRepository()->_hasDelete($id))
            //         return $this->_backToStart()->with('has_delete_modal', "true");

            //     if ($this->getRepository()->_isUsed($id))
            //         return $this->_backToStart()->with('used_message', "true");
            // }
            foreach ($ids as $id) {
                $entity = $this->getRepository()->find($id);
                $entity->delete();
                $this->_deleteRelations($entity);
            }
            // $this->getRepository()->whereIn('id', $id)->$action();
            return $this->_backToStart()->with('success', trans('messages.delete_success'));
        } catch (\Exception $e) {
            logError($e);
        }
        return $this->_backToStart()->withErrors(trans('messages.delete_failed'));
        //@todo destroy relations data
    }

    /**
     * @param null $id
     * @param bool $clean
     * @param bool $getForUpdate
     * @return mixed
     */
    protected function _findOrNewEntity($id = null, $clean = false, $getForUpdate = false)
    {
        $data = $this->_getFormData(false, $clean);

        if (empty($data['id'])) {
            $data['id'] = empty($data['duplicate']) ? $id : null;
        } else {
            !empty($data['duplicate']) ? $data['id'] = null : null;
        }
        return $this->getRepository()->findFirstOrNew($data, $getForUpdate);
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function _findEntityForUpdate($id)
    {
        return $this->_findOrNewEntity($id, true, true);
    }

    /**
     * @return mixed
     */
    protected function _findEntityForStore()
    {
        return $this->_findOrNewEntity(null, true);
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function _prepareShow($id)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    protected function _prepareCreate()
    {
        return $this->_findOrNewEntity(true);
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function _prepareEdit($id = null)
    {
        return $this->_findOrNewEntity($id, true);
    }

    public function exportCsv()
    {

    }

    /**
     * @return RedirectResponse
     */
    protected function _toConfirm()
    {
        Url::collectOldUrl();
        return $this->_toRoute($this->getConfirmRoute(), ['id' => Request::get('id'), Url::QUERY => getBackParams()]);
    }

    /**
     * @param null $errors
     * @return mixed
     */
    protected function _inValid($errors = null)
    {
        $this->fireEvent('before_in_valid', $this);
        Session()->put('validation', ['inValid' => $this->_getListErrorMessage()]);
        $result = $this->_back()->withInput();
        $this->fireEvent('after_in_valid', $result);
        return $result;
    }

    /**
     * @param $controller
     * @param $action
     * @return mixed
     * @throws BindingResolutionException
     */
    public function forward($controller, $action)
    {
        $container = app();
        $route = $this->getCurrentRoute();
        $controllerInstance = $container->make($controller);
        return (new ControllerDispatcher($container))->dispatch($route, $controllerInstance, $action);
    }

    /**
     * @param array $params
     * @return RedirectResponse
     */
    protected function _backToStart($params = array())
    {
        $url = Url::getBackUrl(false, $this->getBackUrlDefault());
        return $this->_to($url, $params);
    }

    /**
     * @return RedirectResponse
     */
    protected function _back()
    {
        return Redirect::back();
    }

    /**
     * @param $route
     * @param $params
     * @return RedirectResponse
     */
    protected function _toRoute($route, $params)
    {
        return redirect()->route($route, $params);
    }

    /**
     * @param $url
     * @param array $params
     * @return RedirectResponse
     */
    protected function _to($url, $params = array())
    {
        $data = ['url' => $url, 'params' => $params];
        $this->fireEvent('before_redirect', $data);
        $url = $data['url'];
        $params = $data['params'];
        if (strpos($url, 'http') !== false) {
            return new RedirectResponse(url($url, $params));
        }
        if (strpos($url, '.') !== false) {
            $url = route($url, $params);
        }
        $r = Redirect::to($url)->with($params);
        $this->fireEvent('after_redirect', $r);
        return $r;
    }

    /**
     * @return RedirectResponse
     */
    protected function _redirectToHome()
    {
        return $this->_to('/');
    }

    /**
     * @return string
     */
    protected function _getFormDataKey()
    {
        return $this->getArea() . '_' . $this->getCurrentControllerName();
    }

    /**
     * @param $data
     * @return $this
     */
    protected function _setFormData($data)
    {
        Session::put([$this->_getFormDataKey() => $data]);
        return $this;
    }

    /**
     * @param bool $toObject
     * @param bool $clean
     * @return mixed
     */
    protected function _getFormData($toObject = true, $clean = false)
    {
        $data = Session::get($this->_getFormDataKey(), array());
        if ($clean) {
            $this->_cleanFormData($data);
        }
        return $toObject ? $this->getRepository()->findFirstOrNew($data) : $data;
    }

    /**
     * @param $data
     */
    protected function _cleanFormData($data = [])
    {
        Session::put([$this->_getFormDataKey() => []]);
        request()->session()->flash('hasClean', !empty($data));
    }

    protected function _hasClean($clean = true)
    {
        $r = Session::get('hasClean');
        $clean ? Session::put('hasClean', null) : null;
        return $r;
    }

    protected function _hasInValid()
    {
        return session()->has('errors') && session()->get('errors')->has('inValid');
    }

    /**
     * @return bool
     */
    protected function _emptyFormData()
    {
        return empty($this->_getFormData(false));
    }

    /**
     * @return bool
     */
    protected function _isRefresh()
    {
        $check = isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' || $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache');

        if ($check && (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== false)) {
            if (request()->session()->has('success') || request()->session()->has('errors')) return false;
            if (empty($_SERVER['HTTP_SEC_FETCH_USER'])) return false;
            return $check;
        }

        return $check;
    }

    protected function _removeMediaFile($entity)
    {
        try {
            if (!$entity) {
                return true;
            }
            $fields = $entity->_file_name;
            foreach ((array)$fields as $field) {
                CustomStorage::delete($entity->$field);
            }
        } catch (Exception $e) {

        }
    }

    protected function _getNewMediaFileName($entity, $field)
    {
        $ext = pathinfo(Arr::get($entity->tmp_file, $field), PATHINFO_EXTENSION);
        return $this->getCurrentControllerName() . DIRECTORY_SEPARATOR . time() . rand(0, 9999) . '_' . $entity->getNextInsertId() . '.' . $ext;
    }

    protected function _moveFileFromTmpToMedia(&$entity)
    {
        $this->fireEvent(getEventName('before_move_file_from_tmp_to_media'), $entity);
        if (($entity->id && !$entity->tmp_file) || !$entity->tmp_file) {
            return;
        }
        $fields = $entity->_file_name;
        foreach ((array)$fields as $field) {
            $entity->$field = CustomStorage::moveFromTmpToMedia(Arr::get($entity->tmp_file, $field), $this->_getNewMediaFileName($entity, $field));
        }
        $this->fireEvent(getEventName('after_move_file_from_tmp_to_media'), $entity);
    }

    protected function _processFile()
    {
        $params = $this->_getParams();
        $this->fireEvent('before_process_file', $params);
        if (empty(Arr::get($params, '_file_name'))) {
            return;
        }
        $fieldName = Arr::get($params, '_file_name');
        foreach ($fieldName as $field) {
            if (!isset($params[$field]) || empty($params[$field]) || is_scalar($params[$field])) {
                continue;
            }
            $originalName = $params[$field]->getClientOriginalName();
            $unique = hash('sha1', uniqid(time(), true));
            $fileName = $unique . '.' . $params[$field]->getClientOriginalExtension();
            $fileName = CustomStorage::uploadToTmp($fileName, $params[$field]);
            $params[$field] = $originalName;
            $params['original_files'] = [$field => $originalName];
            Arr::get($params, $field, $fileName);
        }
        $this->fireEvent('after_process_file', $params);
        $this->_setFormData($params);
    }

    /**
     * @return mixed
     */
    protected function _getParams()
    {
        return Request::all();
    }

    public function setErrorFlash($message)
    {
        $message = new MessageBag(['errors' => [$message]]);
        request()->session()->flash('errors', $message);
    }

    public function setSuccessFlash($message)
    {
        $message = new MessageBag(['success' => [$message]]);
        request()->session()->flash('success', $message);
    }

    public function flashError($message)
    {
        request()->session()->flash('flash_errors', $message);
    }

    public function flashSuccess($message)
    {
        request()->session()->flash('flash_success', $message);
    }

    /**
     * @param Model $entity
     * Xử lý thêm mới node bằng Baum
     */
    protected function _insertNestedSet($entity)
    {
        $model = $this->getRepository()->getModel();
        $attributes = $entity->getAttributes();
        $attributes['parent_id'] = empty($attributes['parent_id']) ? null : $attributes['parent_id'];
        $model::create($attributes);
    }

    /**
     * @param Model $entity
     * Xử lý cập nhật node bằng Baum
     */
    protected function _updateNestedSet($entity)
    {
        $pid = $entity->getParentId();
        if (is_null($pid))
            $entity->makeRoot();
        else if ($pid !== FALSE) {
            $entity->makeChildOf($pid);
        }
        $entity->save();
    }

    /**
     * Xử lý xóa node bằng Baum
     * @param Model $entity
     * @throws Exception
     */
    protected function _deleteNestedSet($entity)
    {
        $entity->delete();
    }

    /**
     * @param Model $entity
     * @param string $action
     * @return bool
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        if (in_array($action, ['delete', 'forceDelete', 'massDelete'])) {
            return true;
        }
        $relations = $entity->getRelations();
        foreach ($relations as $relationName => $relation) {
            if (is_null($relation)) {
                continue;
            }

            if (isCollection($relation)) {
                $relation->map(function ($item) use ($entity, $action, $relationName) {
                    $item->exists = (bool)$item->id;
                    $item->fill([$entity->$relationName()->getForeignKeyName() => $entity->id]);
                    call_user_func_array([$item, $action], []);
                    $this->_saveRelations($item, $action);
                });
                continue;
            }
            $relation->exists = (bool)$relation->id;
            $relation->fill([$entity->getForeignKey() => $entity->id]);
            call_user_func_array([$relation, $action], []);
            $this->_saveRelations($relation);
        }
    }

    /**
     * @return array
     */
    protected function _getListErrorMessage()
    {
        $errorsBag = $this->getRepository()->getValidator()->errorsBag();
        $errorsBag = is_array($errorsBag) ? $errorsBag : $errorsBag->toArray();
        $errorList = [];
        foreach ($errorsBag as $key => $value) {
            $errorList[$key] = Arr::get($value, 0);
        }

        return $errorList;
    }

    protected function _deleteRelations($entity)
    {

    }

    protected function _processQuickSave($id, $field, $value)
    {

    }

    protected function _validExtend($params)
    {
        return [];
    }
}
