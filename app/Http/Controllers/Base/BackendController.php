<?php

namespace App\Http\Controllers\Base;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Helpers\Url;
use App\Model\Base\NestedSetBase;
use App\Model\Entities\AdminUserInfo;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Input;
use JsValidator;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use Proengsoft\JsValidation\Javascript\JavascriptValidator;
use Validator;

/**
 * Class BackendController
 * @package App\Http\Controllers\Base
 */
class BackendController extends BaseController
{
    /**
     * @var string
     */
    protected $_area = 'backend';

    /**
     * @var bool
     */
    protected $_isValidator = true;

    /**
     * @var string
     */
    const SESSION_INDEX = 'backend_index';

    /**
     * @var string
     */
    const SESSION_EXCEL = 'backend_excel';

    protected $_fieldsSearch = ['id'];

    protected $_map = false;

    protected $_draft = false;

    protected $_excel = false;

    protected $_auditing = false;

    protected $_deleted = false;

    protected $_excel_update = false;

    /**
     * @return bool
     */
    public function isMap(): bool
    {
        return $this->_map;
    }

    /**
     * @param bool $map
     */
    public function setMap(bool $map): void
    {
        $this->_map = $map;
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->_draft;
    }

    /**
     * @param bool $draft
     */
    public function setDraft(bool $draft): void
    {
        $this->_draft = $draft;
    }

    /**
     * @return bool
     */
    public function isValidator(): bool
    {
        return $this->_isValidator;
    }

    /**
     * @param bool $isValidator
     */
    public function setIsValidator(bool $isValidator): void
    {
        $this->_isValidator = $isValidator;
    }

    /**
     * @return bool
     */
    public function isExcel(): bool
    {
        return $this->_excel;
    }

    /**
     * @param bool $excel
     */
    public function setExcel(bool $excel): void
    {
        $this->_excel = $excel;
    }

    /**
     * @return bool
     */
    public function isAuditing(): bool
    {
        return $this->_auditing;
    }

    /**
     * @param bool $auditing
     */
    public function setAuditing(bool $auditing): void
    {
        $this->_auditing = $auditing;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->_deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->_deleted = $deleted;
    }

    /**
     * @return bool
     */
    public function isExcelUpdate(): bool
    {
        return $this->_excel_update;
    }

    /**
     * @param bool $excel_update
     */
    public function setExcelUpdate(bool $excel_update): void
    {
        $this->_excel_update = $excel_update;
    }

    /**
     * @return mixed
     */
    public function __construct()
    {
        $this->setViewData([
            'hideLeftSidebar' => request()->cookie('left_sidebar') === 'hide',
        ]);
        return parent::__construct();
    }

    public function index()
    {
        $this->_checkPermission();
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $data = $this->_getDataIndex();
        $this->_prepareIndex();
        if (!array_key_exists('sort_field', $data)) {
            $config = $this->getViewData();
            if (array_key_exists('sort_field', $config)) {
                $data['sort_field'] = $config['sort_field'];
            }
            if (array_key_exists('sort_type', $config)) {
                $data['sort_type'] = $config['sort_type'];
            }
            if (array_key_exists('page_size', $config)) {
                $data['per_page'] = $config['page_size'];
            }
        }
        $entities = $this->getRepository()->getListForBackend($data);

        if ($entities->isEmpty() && Request::get('page', 1) != 1) {
            $params = Request::all();
            $params['page'] = 1;
            return $this->_to($this->getCurrentRouteName(), $params);
        }
        $this->setViewData([
            'backUrlKey' => Url::getBackUrlKey(),
            'dataIndex' => $data,
            'showDeleted' => $this->isDeleted(),
        ]);
        $groupColumn = $this->getRepository()->getGroupColumn();
        if (!empty($groupColumn)) {
            $groupData = $this->getRepository()->getQueryGroup($data);
            $this->setViewData([
                'groupData' => $groupData,
            ]);
        }
        return $this->setEntities($entities)->render();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        if (!request()->ajax()) {
            return $this->_backToIndex();
        }
        $backUrlKey = Request::get('back_url_key', null);
        $grid = Request::get('grid', false);

        $prepare = $this->_prepareShow($id);
        if ($prepare instanceof RedirectResponse) {
            $this->setData(['deleted' => true]);
            return $this->renderJson();
        } else if (is_numeric($prepare) && $prepare == HttpCode::EC_UNAUTHENTICATED) {
            $this->setData(['auth' => true]);
            return $this->renderJson();
        }

        $routeName = $this->getCurrentRouteName();
        $routePrefix = str_replace('.' . $this->getCurrentAction(false), '', $routeName);
        $url = $grid ? $url = route($routePrefix . '.index') . '#' . $id : '';
        $this->setViewData([
            'showAdvance' => true,
            'showAuditing' => $this->isAuditing() ? true : null,
            'backUrlKey' => $grid ? Url::generateBackUrlKey($url) : $backUrlKey,
        ]);

        $entity = $this->getEntity();
        $html = [
            'content' => $this->render('backend.' . $this->getCurrentControllerName() . '._show')->render(),
            'title' => trans('actions.show_detail') . ' <b>' . $entity->getDetailNameField() . '</b>',
            'backUrlKey' => $grid ? Url::generateBackUrlKey($url) : $backUrlKey,
        ];

        $this->setData($html);

        return $this->renderJson();
    }

    protected function _prepareShow($id)
    {
        return $this->setEntity($this->getRepository()->findWithRelation($id));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $this->_checkPermission('add');
        $this->fireEvent('before_create', $this);
        $result = $this->_prepareCreate()->render();
        $this->fireEvent('after_create', $result);
        return $result;
    }

    /**
     * @return RedirectResponse|mixed
     * @throws BindingResolutionException
     */
    public function valid()
    {
        $params = $this->_getParams();
        $this->fireEvent('before_valid', $params);
        if (!Request::has('_jsvalidation') && !Request::has('_jsvalidation_validate_all')) {
            $this->_setFormData($params);
        }
        $this->_processFile();

        if ($this->isDraft() && isset($params['draft'])) {
            if (!$this->getRepository()->getValidator()->validateDraft($params)) {
                return $this->_inValid();
            }
        } else {
            if (Request::has('id') && Request::get('id')) {
                if (!$this->getRepository()->getValidator()->validateUpdate($params)) {
                    return $this->_inValid();
                }
            } else {
                // case create
                if (!$this->getRepository()->getValidator()->validateCreate($params)) {
                    return $this->_inValid();
                }
            }
        }

        $validResult = $this->_validExtend($params);
        if (!empty($validResult)) {
            return $this->_back()->withInput()->with($validResult);
        }

        $result = $this->getConfirmRoute() ? $this->_toConfirm() : $this->forward($this->getCurrentController(), 'store');
        $this->fireEvent('after_valid', $result);
        return $result;
    }

    /**
     * @return $this|mixed
     */
    public function confirm()
    {
        $this->fireEvent('before_confirm', $this);
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(array(
                trans('validation.not_empty', ['attribute' => 'data'])
            ));
        }
        $this->_prepareConfirm();

        $this->fireEvent('after_confirm', $this);
        return $this->render();
    }

    /**
     *
     */
    protected function _prepareConfirm()
    {
        $this->setEntity($this->_getFormData());
    }

    /**
     * @return RedirectResponse|void
     * @throws \Exception
     */
    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);

            //Nếu là danh mục dạng cha con thì xử lý lưu bằng Baum
            if ($entity instanceof NestedSetBase) {
                $this->_insertNestedSet($entity);
            } else {
                $entity->save();
            }
            $this->_saveRelations($entity);
            // add new
            $this->fireEvent('after_store', $entity);
            DB::commit();

            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (\Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $this->_checkPermission('edit');
        $prepare = $this->_prepareEdit($id);
        $this->_prepareAfterSetEntity($prepare);
        return $prepare instanceof RedirectResponse ? $prepare : $this->render();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function duplicate($id)
    {
        $prepare = $this->_prepareDuplicate($id);
        $this->_prepareAfterSetEntity($prepare);
        return $prepare instanceof RedirectResponse ? $prepare : $this->render();
    }

    /**
     * @param null $id
     * @return BackendController|RedirectResponse
     */
    protected function _prepareDuplicate($id = null)
    {
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        $this->_prepareForm();
        $this->_prepareFormWithID($id);
        $this->setViewData([
            'isDuplicate' => true
        ]);
        return $this->setEntity($this->_findOrNewEntity($id, true));
    }

    /**
     * @param $id
     * @return BackendController|RedirectResponse
     * @throws \Exception
     */
    public function update($id)
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.update_failed'));
        }

        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);

            if ($entity instanceof NestedSetBase) {
                $this->_updateNestedSet($entity);
            } else {
                $entity->save();
            }
            // fire after save
            // fire before save relation
            $this->_saveRelations($entity, 'update');
            // fire after save relation
            // add new
            DB::commit();
            $this->fireEvent('after_update', $entity);
            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (\Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }

    public function destroy($id, $action = 'delete')
    {
        $this->_checkPermission('delete');

        if (!$this->getRepository()->_hasDelete($id))
            return $this->_backToStart()->with('has_delete_modal', "true");

        //Neu doi tuong da duoc su dung thì ko xoa
        if ($this->getRepository()->_isUsed($id))
            return $this->_backToStart()->with('used_message', "true");

        $isValid = $this->getRepository()->getValidator()->validateDestroy($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        DB::beginTransaction();
        try {
            $entity = $this->getRepository()->find($id);
            $this->fireEvent('before_destroy', $entity);
            if ($entity instanceof NestedSetBase) {
                $this->_deleteNestedSet($entity);
            } else {
                call_user_func_array([$entity, $action], []);
            }
            $this->_saveRelations($entity, $action);
            $this->_deleteRelations($entity);
            DB::commit();
            $this->fireEvent('after_destroy', $entity);
            return $this->_backToIndex()->withSuccess(trans('messages.delete_success'));
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.delete_failed'));
    }

    public function deleted()
    {
        if (!$this->isDeleted() || !request()->ajax()) {
            return $this->_backToStart();
        }
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $data = [
            'sort_type' => Request::get('sort_type'),
            'sort_field' => Request::get('sort_field'),
        ];
        $entities = $this->getRepository()->getListDeletedForBackend($data);

        if ($entities->isEmpty() && Request::get('page', 1) != 1) {
            $params = Request::all();
            $params['page'] = 1;
            return $this->_to($this->getCurrentRouteName(), $params);
        }
        $this->setViewData([
            'entities' => $entities,
        ]);
        $this->_prepareIndex();

        $html = [
            'content' => $this->render('backend.' . $this->getCurrentControllerName() . '.deleted')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    /**
     * @return $this
     */
    protected function _prepareCreate()
    {
        $this->_prepareForm();
        $this->_prepareFormWithID($id = -1);
        return $this->setEntity($this->_findOrNewEntity(null, true));
    }

    /**
     * @param null $id
     * @return BackendController|RedirectResponse
     */
    protected function _prepareEdit($id = null)
    {
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        $this->_prepareForm();
        $this->_prepareFormWithID($id);
        return $this->setEntity($this->_findOrNewEntity($id, true));
    }

    public function exportCsv()
    {
        $header = getConfig('csv.export.' . $this->getCurrentControllerName() . '.header');
        $data = [
            array_values($header)
        ];
        $entities = $this->getRepository()->getListForExport(Request::all());
        foreach ($entities as $entity) {
            $newData = [];
            foreach (array_keys($header) as $item) {
                $newData[] = $entity->{$item};
            }
            $data[] = $newData;
        }
        try {
            $filename = getConfig('csv.export.' . $this->getCurrentControllerName() . '.filename_prefix') . '_' . date('Ymd');
            // Generate and return the spreadsheet
            Excel::create($filename, function ($excel) use ($data) {
                // Build the spreadsheet, passing in the payments array
                $excel->sheet('sheet1', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true, false);
                });
            })->download('csv');
        } catch (\Exception $e) {
            logError($e->getMessage());
            return $this->_backToStart()->withErrors(trans('messages.failed'));
        }
    }

    /**
     * @return AdminUserInfo|null
     */
    public function getCurrentUser()
    {
        return backendGuard()->user();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxSearch()
    {
        $viewFile = Request::get('view_file', '_list');
        $backUrlKey = Request::get('back_url_key', null);
        $isGenerateHeader = Request::get('is_generate_header', false);
        $data = Request::all();
        $data['per_page'] = Request::get('per_page', config('pagination.backend.per_page.default'));


        $this->detectCurrentPage();
        $this->_pushDataIndex($data);
        $this->_prepareIndex();

        if (!array_key_exists('sort_field', $data)) {
            $config = $this->getViewData();
            if (array_key_exists('sort_field', $config)) {
                $data['sort_field'] = $config['sort_field'];
            }
            if (array_key_exists('sort_type', $config)) {
                $data['sort_type'] = $config['sort_type'];
            }
            if (array_key_exists('page_size', $config)) {
                $data['per_page'] = $config['page_size'] == null ? $data['per_page'] : $config['page_size'];
            }
        }
        $entities = $this->getRepository()->getListForBackend($data);
        $this->setEntities($entities);

        $groupColumn = $this->getRepository()->getGroupColumn();
        $groupData = [];
        if (!empty($groupColumn)) {
            $groupData = $this->getRepository()->getQueryGroup($data);
        }

        $selectedItem = Request::get('selected_item', []);

        $this->setViewData([
            'backUrlKey' => $backUrlKey,
            'dataIndex' => $this->_getDataIndex(),
            'selectedItem' => explode(',', $selectedItem),
        ]);

        $head = '';
        if ($isGenerateHeader === 'true' && view()->exists('backend.' . $this->getCurrentControllerName() . '._head')) {
            $head = $this->render('backend.' . $this->getCurrentControllerName() . '._head')->render();
        }
        $html = [
            'head' => $head,
            'content' => $this->render('backend.' . $this->getCurrentControllerName() . '.' . $viewFile)->render(),
            'paginator' => $this->render('layouts.backend.elements.pagination')->render(),
            'paginator_info' => $this->render('layouts.backend.elements.pagination_info')->render(),
            'groupData' => $groupData
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    public function advance(Request $request)
    {
        $data = array(
            'statics' => $this->getStatics(),
            'controllerName' => $this->getCurrentControllerName(),
            'area' => $this->getArea(),
        );
        //        $autoload = View('layouts.backend.elements.autoload.footer_autoload', $data)->render();
        $autoload = '';


        if ($request->isMethod('get')) {
            $this->_getFormData(false, true);
            $this->_prepareCreate();
            $model = Request::get('model', '');

            $this->setViewData([
                'formAdvance' => true
            ]);

            $content_view = $this->render('backend.' . $this->getCurrentControllerName() . '._form')->render();

            $html = [
                'content' => $content_view . $autoload,
                'title' => empty($model) ? '' : transb($model . '.create'),
            ];

            $this->setData($html);
        }

        if ($request->isMethod('post')) {
            $params = $this->_getParams();
            $model = Request::get('model', '');
            $this->_setFormData($params);

            $this->setViewData([
                'formAdvance' => true
            ]);

            if (!$this->getRepository()->getValidator()->validateCreate($params)) {
                $this->_prepareCreate();
                $this->setViewData([
                    'validation' => ['inValid' => $this->_getListErrorMessage()]
                ]);

                $content_view = $this->render('backend.' . $this->getCurrentControllerName() . '._form')->render();

                $html = [
                    'content' => $content_view . $autoload,
                    'title' => empty($model) ? '' : transb($model . '.create'),
                    'validation' => true
                ];
            } else {
                $entity = $this->_findEntityForStore();
                try {
                    $entity->save();
                    $this->_saveRelations($entity);
                } catch (\Exception $e) {
                    logError($e);
                }

                $html = [
                    'title' => empty($model) ? '' : transb($model . '.create'),
                    'entity' => $entity->toArray(),
                    'model' => $model
                ];
            }

            $this->setData($html);
        }

        return $this->renderJson();
    }

    public function import()
    {
        if (!request()->isMethod('post')) {
            return $this->_redirectToHome();
        }

        $importFile = request()->input('import_file');
        $checkUpdates = request()->input('check_update');

        switch (true) {
            case (isset($importFile)):
                $html = $this->_processFileImport();
                break;
            case (isset($checkUpdates)):
                $html = $this->_processDataImport($checkUpdates == 1);
                break;
            default:
                $html = $this->_processDataImport();
        }

        $this->setData($html);
        return $this->renderJson();
    }

    public function exportConfirm()
    {
        $data = $this->_getDataIndex();
        $message = trans('messages.export_message');
        foreach ($data as $key => $value) {
            if (
                $key != 'per_page' && $key != 'sort_type' && $key != 'sort_field' && $key != 'current_page' &&
                $key != 'page' && $key != 'back_url_key' && $value != null
            ) {
                $message = trans('messages.export_message_filter');
            }
        }
        $html = [
            'message' => $message
        ];
        $this->setData($html);
        return $this->renderJson();
    }

    public function auditing($id)
    {
        if (!$this->isAuditing() || !request()->ajax()) {
            return $this->_backToStart();
        }

        $entity = $this->getRepository()->findWithRelation($id);
        $this->setEntity($entity);
        $auditing = $entity->audits()->with('user')->get();

        $this->setViewData(['auditing' => $auditing]);

        $html = [
            'content' => $this->render('layouts.backend.elements.auditing._content_auditing')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    /**
     * @return RedirectResponse
     */
    protected function _redirectToHome()
    {
        if (\Auth::check() && \Auth::user()->role == 'partner') {
            // return $this->_to(route('partner-dashboard.index'));
            return $this->_to(route('order-board.index'));
        }

        return $this->_to(getBackendAlias());
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
     * @param array $params
     * @return RedirectResponse
     */
    protected function _backToIndex($params = [])
    {
        $url = $this->getBackUrlDefault();
        return $this->_to($url, $params);
    }

    /**
     *
     */
    protected function _prepareForm()
    {
    }

    protected function _prepareFormWithID($id)
    {
    }

    /**
     * @param $prepare
     */
    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }
    }

    protected function _prepareIndex()
    {
    }

    /**
     * @return JavascriptValidator|string
     */
    protected function _getJsValidator()
    {
        $repository = $this->getRepository();

        if (empty($repository)) {
            return '';
        }

        $action = $this->getCurrentAction();

        switch ($action) {
            case 'create':
            case 'duplicate':
                $rules = $repository->getValidator()->getCreateRules();
                break;
            case 'update':
                $rules = $repository->getValidator()->getUpdateRules();
                break;
            default:
                $rules = $repository->getValidator()->getClientRules();
        }
        return JsValidator::make(
            $rules['rules'],
            isset($rules['messages']) ? $rules['messages'] : [],
            $this->_getAttributeNames()
        );
    }

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return mixed
     */
    public function render($view = null, $data = [], $mergeData = [])
    {
        $user = $this->getCurrentUser();
        $notification = null;
        $countUnread = 0;
        if (!empty($user) && !empty($user->id) && !request()->ajax()) {
            $notiObj = $this->getNotificationForUser($user->id);
            //            $notification = $notiObj['notification'];
            $countUnread = $notiObj['countUnread'];
        }

        $this->setViewData([
            'map' => $this->isMap(),
            'draft' => $this->isDraft(),
            'excel' => $this->isExcel(),
            'excelUpdate' => $this->isExcelUpdate(),
            'validator' => $this->_isValidator ? $this->_getJsValidator() : '',
            //            'notification' => $notification,
            'countUnread' => $countUnread
        ]);
        return parent::render($view, $data, $mergeData);
    }

    /**
     * @param $data
     * @param bool $force
     * @return bool
     */
    protected function _pushDataIndex($data, $force = false)
    {
        if (empty($data) && !$force) {
            return false;
        }

        $data['current_page'] = empty($data['page']) || $data['page'] == '1' ? null : $data['page'];

        $currentController = $this->getCurrentControllerName();
        $backendIndex = session(self::SESSION_INDEX, array());
        $backendIndex[$currentController] = $data;

        session([self::SESSION_INDEX => $backendIndex]);
        return true;
    }

    /**
     * @param bool $isConvertDate
     * @return array
     */
    protected function _getDataIndex($isConvertDate = true)
    {
        $currentController = $this->getCurrentControllerName();
        $backendIndex = session(self::SESSION_INDEX, array());

        if ($this->_isRefresh()) {
            $this->_pushDataIndex([], true);
            return [];
        }

        $data = isset($backendIndex[$currentController]) ? $backendIndex[$currentController] : [];
        if ($isConvertDate)
            foreach ($data as &$value) {
                if (is_array($value)) continue;

                $explodes = explode('-', $value);
                if (count($explodes) === 3 && DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
                    $value = DateTime::createFromFormat('Y-m-d', $value)->format('d-m-Y');
                }
            }

        if (isset($data['current_page'])) {
            $currentPage = $data['current_page'];
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
        }

        return $data;
    }

    /**
     * @param bool $update
     * @return array
     */
    protected function _processDataImport($update = false)
    {
        $data = json_decode(request()->get('data'));
        $data = $this->_mappingDataImport($data, $update);

        $update ? $this->getRepository()->getValidator()->validateImportUpdate($data) :
            $this->getRepository()->getValidator()->validateImport($data);
        $errors = $this->getRepository()->getValidator()->errorsBag();

        if (!empty($errors)) {
            foreach ($data as $key => &$row) {
                foreach ($errors->get($key . '.*') as $message) {
                    $row['failures'][] = Arr::get($message, 0);
                }
                if (empty($row['failures'])) continue;
                $row['importable'] = false;
            }
        }

        $currentController = $this->getCurrentControllerName();
        $backendExcel = session(self::SESSION_EXCEL, []);
        $backendExcel[$currentController] = $data;
        $backendExcel[$currentController . '_type'] = $update;
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData(['entities' => $data]);
        return [
            'content' => $this->render('backend.' . $currentController . '.import')->render(),
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
        ];
    }

    /**
     * @return array
     */
    protected function _processFileImport()
    {
        try {
            $backendExcel = session(self::SESSION_EXCEL, array());
            $currentController = $this->getCurrentControllerName();
            $dataList = $backendExcel[$currentController];
            $type = $backendExcel[$currentController . '_type'];

            DB::beginTransaction();

            $ignoreCount = 0;
            $total = count($dataList);
            foreach ($dataList as $data) {
                if (!$data['importable']) {
                    $ignoreCount++;
                    continue;
                }
                $entity = $this->getRepository()->findFirstOrNew($data);
                $this->_saveRelations($entity);
                $entity = $this->_processDataForImport($entity, $data);
                $entity->save();
            }
            DB::commit();

            $file = request()->file;
            if (!empty($file)) {
                app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $type, $this->getTitle());
            }

            unset($backendExcel[$currentController]);
            unset($backendExcel[$currentController . '_type']);
            session([self::SESSION_EXCEL => $backendExcel]);

            $this->setViewData([
                'total' => $total,
                'done' => $total - $ignoreCount,
            ]);

            $file = request()->file;
            if (!empty($file)) {
                app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $type, $this->getTitle());
            }

            return [
                'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
                'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
            ];
        } catch (\Exception $e) {
            logError($e);
            DB::rollBack();
        }

        return null;
    }

    protected function _mappingDataImport($data, $update)
    {
        return $data;
    }

    protected function _processDataForImport($entity, $data)
    {
        return $entity;
    }

    public function getNotificationForUser($userId, $unread = true)
    {
        // TODO: Hardcode
        $pageIndex = 1;
        $pageSize = 15;
        if ($unread) {
            $countUnreadQuery = DB::table('notification_logs')
                ->where([
                    ['del_flag', '=', '0'],
                    ['read_status', '=', '0']
                ])
                ->where(function ($query) use ($userId) {
                    $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                        ->orWhere('notification_logs.user_id', $userId);
                });
            $countUnread = $countUnreadQuery->count();
            return [
                'countUnread' => $countUnread,
            ];
        } else {
            $countQuery = DB::table('notification_logs')
                ->where([
                    ['del_flag', '=', '0']
                ])
                ->where(function ($query) use ($userId) {
                    $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                        ->orWhere('notification_logs.user_id', $userId);
                });
            $countUnreadQuery = DB::table('notification_logs')
                ->where([
                    ['del_flag', '=', '0'],
                    ['read_status', '=', '0']
                ])
                ->where(function ($query) use ($userId) {
                    $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                        ->orWhere('notification_logs.user_id', $userId);
                });
            $query = DB::table('notification_logs')
                ->where([
                    ['del_flag', '=', '0']
                ])
                ->where(function ($query) use ($userId) {
                    $query->where('notification_logs.type', AppConstant::NOTIFICATION_TYPE_ALL)
                        ->orWhere('notification_logs.user_id', $userId);
                })
                ->orderByDesc('ins_date');

            $count = $countQuery->count();
            $countUnread = $countUnreadQuery->count();
            $totalPage = 0;
            if (0 < $count) {
                $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
            }
            $offset = ($pageIndex - 1) * $pageSize;

            $notification = $query->skip($offset)
                ->take($pageSize)
                ->get();
            return [
                'count' => $count,
                'totalPage' => $totalPage,
                'countUnread' => $countUnread,
                'notification' => $notification
            ];
        }
    }

    public function doMakeReadAll($userId)
    {
        try {
            DB::beginTransaction();
            $update = [
                'read_status' => '1'
            ];
            DB::table('notification_logs')
                ->where([
                    ['del_flag', '=', '0'],
                    ['read_status', '=', '0'],
                    ['user_id', '=', $userId]
                ])
                ->update($update);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function generateHeadTable()
    {
        // $entities = $this->getRepository()->getListForBackend([]);
        // $this->setEntities($entities);
        $this->_prepareIndex();

        $html = [
            'content' => $this->render('backend.' . $this->getCurrentControllerName() . '._head')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    protected function _checkPermission($action = 'view')
    {
        $currentControllerName = Str::slug($this->getCurrentControllerName(), '_');
        $currentUser = $this->getCurrentUser();
        if (
            !$currentUser->can($action . ' ' . $currentControllerName)
            && $currentControllerName !== 'board'
            && $currentControllerName !== 'notification'
        ) {
            abort(403);
            // return $this->_redirectToHome()->send();
        }
        return true;
    }

    public function quickSave(Request $request)
    {
        try {
            $validation = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'Id' => 'required',
                'Field' => 'required',
                'Value' => 'required',
                'Entity' => 'required',
            ]);
            if ($validation->fails()) {
                $data = [
                    'message' => $validation->messages()
                ];
                $this->setData($data);
                return $this->renderJson();
            } else {
                $id = $request->get('Id');
                $field = $request->get('Field');
                $value = $request->get('Value');
                $e = $request->get('Entity');

                $this->_processQuickSave($id, $field, $value);

                $data = [
                    'message' => 'success'
                ];

                $this->setData($data);
                return $this->renderJson();
            }
        } catch (Exception $e) {
            logError($e);
            $data = [
                'message' => 'error'
            ];
            $this->setData($data);
            return $this->renderJson();
        }
    }

    // Khoá đơn hàng
    //CreatedBy nlhoang 29/09/2020
    protected function lock(Request $request)
    {
        try {
            $type = Request::get('type');
            $fromDate = Request::get('fromDate');
            $toDate = Request::get('toDate');

            $validation = Validator::make($request->all(), [
                'type' => 'required',
                'fromDate' => 'required',
                'toDate' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $this->getRepository()->lock($type, $fromDate, $toDate);

            $data = [
                'errorCode' => HttpCode::EC_OK,
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'errorCode' => HttpCode::EC_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    // Mở khoá đơn hàng
    //CreatedBy nlhoang 29/09/2020
    protected function unlock(Request $request)
    {
        try {
            $type = Request::get('type');
            $fromDate = Request::get('fromDate');
            $toDate = Request::get('toDate');

            $validation = Validator::make($request->all(), [
                'type' => 'required',
                'fromDate' => 'required',
                'toDate' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $this->getRepository()->unlock($type, $fromDate, $toDate);

            $data = [
                'errorCode' => HttpCode::EC_OK,
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'errorCode' => HttpCode::EC_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    // Hiển thị danh sách bản ghi gộp trùng
    //CreatedBy nlhoang 30/09/2020
    protected function deduplicate(Request $request)
    {
        try {
            $ids = Request::get('ids');

            $validation = Validator::make($request->all(), [
                'ids' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $ids = explode(',', $ids);
            $items = $this->getRepository()->getItemsByIds($ids);
            $this->setViewData([
                'items' => $items
            ]);
            $data = [
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'content' => $this->render('backend.' . $this->getCurrentControllerName() . '.deduplicate')->render(),
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'errorCode' => HttpCode::EC_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    // Xử lý gộp trùng địa điểm
    //CreatedBy nlhoang 30/09/2020
    protected function processDeduplicate(Request $request)
    {
        try {
            $sourceID = Request::get('sourceID');
            $destinationIDs = Request::get('destinationIDs');

            $validation = Validator::make($request->all(), [
                'sourceID' => 'required',
                'destinationIDs' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $this->getRepository()->processDeduplicate($sourceID, $destinationIDs);
            $data = [
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'errorCode' => HttpCode::EC_BAD_REQUEST,
                'errorMessage' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }
}
