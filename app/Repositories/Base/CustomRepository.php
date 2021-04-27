<?php

namespace App\Repositories\Base;

use App\Model\Base\ModelSoftDelete;
use Carbon\Carbon;
use DateTime;
use DB;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Str;

/**
 * Class CustomRepository
 * @package App\Repositories\Base
 */
abstract class CustomRepository extends BaseRepository
{
    protected $_isBackend = true;
    /**
     * @var array
     */
    protected $_queryParams = [];
    /**
     * @var string
     */
    protected $_sortField = 'id';
    /**
     * @var string
     */
    protected $_sortType = 'DESC';

    protected $_oldBuilder = null;

    protected $_fieldsSearch = ['id'];

    /**
     * @return bool
     */
    public function isBackend()
    {
        return $this->_isBackend;
    }

    /**
     * @param bool $isBackend
     */
    public function setIsBackend($isBackend)
    {
        $this->_isBackend = $isBackend;
    }


    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->_queryParams;
    }

    /**
     * @param array $queryParams
     */
    public function setQueryParams($queryParams)
    {
        $this->_queryParams = $queryParams;
    }

    /**
     * CustomRepository constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->validator ? $this->validator = $this->getValidator()->setModel($this->getModel()) : null;
        $this->setQueryParams(Request::all());
        $this->setSortField($this->getTableName() . '.' . $this->getSortField());
        $this->setBuilder($this);
        $this->_oldBuilder = $this;
    }

    /**
     * @var int
     */
    protected $_limit = 20;

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->_sortField;
    }

    /**
     * @param string $sortField
     * @return $this
     */
    public function setSortField($sortField, $isSortRaw = false)
    {
        if ($isSortRaw) {
            $this->_sortField = $sortField;
            return $this;
        }
        $sortField = explode(':', $sortField);
        $sortField = isset($sortField[1]) ? $sortField[0] . '.' . $sortField[1] : $sortField[0];
        $sortField = str_replace('[', '.', str_replace(']', '', $sortField));
        $this->_sortField = $sortField;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortType()
    {
        return $this->_sortType;
    }

    /**
     * @param string $sortType
     * @return $this
     */
    public function setSortType($sortType)
    {
        if (in_array(strtoupper($sortType), array('DESC', 'ASC'))) {
            $this->_sortType = $sortType;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @var null
     */
    protected $_builder = null;

    /**
     *
     */
    const OPERATORS = array(
        'gt' => '_greaterThan',
        'gteq' => '_greaterThanOrEqual',
        'lt' => '_lessThan',
        'lteq' => '_lessThanOrEqual',
        'eq' => '_equal',
        'neq' => '_notEqual',
        'in' => '_in',
        'nin' => '_notIn',
        'consf' => '_containsFirst',
        'consl' => '_containsLast',
        'cons' => '_contains',
        'ncons' => '_notContains',
        'lteqt' => '_lessThanOrEqualWithTime',
        'gteqt' => '_greaterThanOrEqualWithTime',
        'isnull' => '_isNull',
        'notnull' => '_notNull',
        'range' => '_betweenDate',
    );

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->_builder;
    }

    /**
     * @param null $builder
     */
    public function setBuilder($builder)
    {
        $this->_builder = $builder;
    }


    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "";
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->getModel(), $name), $arguments);
    }

    /**
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    public function findFirstOrNew($data, $forUpdate = false)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        if (!$id) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        $entity = $this->find($id);
        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data, $forUpdate);
    }

    /**
     * @param $query
     * @return LengthAwarePaginator
     */
    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->paginate($limit, ['*'], 'page', 1);
    }

    /**
     * @param ModelSoftDelete $entity
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $this->processExtendData($data, $entity);
        try {
            if (!isMulti($data)) {
                return $entity;
            }
            foreach ($data as $key => $item) {
                if (!is_array($item)) {
                    continue;
                }
                try {
                    $key = toCameCase($key);
                    $foreignKey = $entity->$key()->getForeignKeyName();
                } catch (Exception $e) {
                    continue;
                }
                $collect = collect();

                if ($entity->$key() instanceof HasOne) {
                    $entity->removeAttribute($key);
                    $tmpItem = $item;
                    $item = empty($entity->$key) ? $entity->$key()->getRelated()->mergeAttributes($item) :
                        $entity->$key->mergeAttributes($item);
                    $item = $this->_prepareRelation($item, $tmpItem, $forUpdate);
                    $entity->setRelation($key, $item);
                    continue;
                }
                foreach ($item as $value) {
                    $tmpValue1 = array_filter_null((array)$value);
                    if (($forUpdate && empty($tmpValue1)) || !is_array($value)) {
                        continue;
                    }
                    $value[$foreignKey] = $entity->id;
                    $tmpValue = $value;
                    $value = $entity->$key()->getRelated()->mergeAttributes($value);
                    $value = $this->_prepareRelation($value, $tmpValue, $forUpdate);
                    $collect->push($value);
                }
                $entity->removeAttribute($key);
                $entity->setRelation($key, $collect);
            }
        } catch (Exception $e) {
            logError($e);
        }
        return $entity;
    }

    /**
     * @param $query
     * @return Collection|null
     */
    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));

        $queryBuilder = $this->search($query);
        $tableName = $this->getTableName();
        $fieldsSearch = $this->_fieldsSearch;

        if (!empty($query['keyword'])) {
            $keyword = $query['keyword'];
            $queryBuilder->where(function ($query) use ($tableName, $fieldsSearch, $keyword) {
                $count = 1;
                foreach ($fieldsSearch as $field) {
                    if ($count === 1) {
                        $query->where($tableName . '.' . $field, 'LIKE', '%' . $keyword . '%');
                    } else {
                        $query->orWhere($tableName . '.' . $field, 'LIKE', '%' . $keyword . '%');
                    }
                    $count++;
                }
            });
        }

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function getListDeletedForBackend($query)
    {
        isset($query['sort_field']) ? null : $query['sort_field'] = 'upd_date';

        $perPage = backendPaginate('per_page.' . $this->getModel()->getAlias(), config('pagination.backend.per_page.default'));
        $queryBuilder = $this->search($query)->onlyTrashed();
        $queryBuilder->with(['insUser', 'updUser']);

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    /**
     * @param $query
     * @return LengthAwarePaginator
     */
    public function getListForFrontend($query)
    {
        return $this->search($query)->paginate(frontendPaginate('per_page.' . $this->getModel()->getTable(), frontendPaginate('per_page.default', 20)));
    }

    /**
     * @param $query
     * @return LengthAwarePaginator
     */
    public function getListForApi($query)
    {
        return $this->search($query)->paginate(apiPaginate('per_page.' . $this->getModel()->getTable(), apiPaginate('per_page.default', 20)));
    }

    /**
     * @param $query
     * @return LengthAwarePaginator
     */
    public function getListForRelation($query)
    {
        return $this->search($query)->paginate();
    }

    /**
     * @param array $query
     * @return LengthAwarePaginator
     */
    public function getList($query = [])
    {
        return $this->search($query)->paginate();
    }

    /**
     * @param $id
     * @return mixed|Collection
     */
    public function findWithRelation($id)
    {
        $query = $this->withId($id);
        return $this->_withRelations($query)->first();
    }

    /**
     * @param $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query;
    }

    protected function _resetBuilder()
    {
        $this->setBuilder($this->_oldBuilder);
    }


    protected function getKeyValue()
    {
        return [];
    }

    /**
     * @param array $query
     * @param array $columns
     * @return Builder
     */
    public function search($query = array(), $columns = [])
    {
        $this->_resetBuilder();
        $this->setQueryParams($query);
        $keyValue = $this->getKeyValue();
        if (isset($query['sort_field'])) {
            $sortField = $query['sort_field'];

            if (array_key_exists($sortField, $keyValue)) {
                $item = $keyValue[$sortField];


                if (array_key_exists("sort_field", $item)) {
                    if (array_key_exists("is_sort_raw", $item)) {
                        $this->setSortField($item['sort_field'], $item["is_sort_raw"]);
                    } else {
                        $this->setSortField($item['sort_field']);
                    }
                } else {
                    $this->setSortField($item['filter_field']);
                }
            } else {
                $this->setSortField($sortField);
            }
        }
        isset($query['sort_type']) ? $this->setSortType($query['sort_type']) : null;

        foreach ($query as $key => $value) {
            $is_exists = false;
            foreach ($keyValue as $k => $v) {
                if (Str::startsWith($key, $k) && array_key_exists('filter_field', $v)) {
                    $is_exists = true;
                    $key = $this->_getKeyForQuery($key, $v["filter_field"] . '_');
                    break;
                }
            }

            if (!$is_exists) {
                $key = $this->getTableName() . '.' . $key . '_';
            }
            if (is_array($value)) {
                $this->_needWhereInOrNotIn($key, $value) ? $this->_buildInOrNotInConditions($key, $value) : $this->_buildConditions($key, $value);
                continue;
            }

            if (trim($value) !== '') {
                if (strpos($key, 'ins_date_eq') !== false) {
                    $this->_buildConditions($this->getTableName() . '.ins_date_lteqt', $value);
                    $this->_buildConditions($this->getTableName() . '.ins_date_gteqt', $value);
                } else if (strpos($key, 'upd_date_eq') !== false) {
                    $this->_buildConditions($this->getTableName() . '.upd_date_lteqt', $value);
                    $this->_buildConditions($this->getTableName() . '.upd_date_gteqt', $value);
                } else {
                    $this->_buildConditions($key, $value);
                }
            }
        }
        return $this->getQueryBuilder($columns);
    }

    // Hàm lấy ra builder query
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))->orderBy($this->getSortField(), $this->getSortType());
    }


    // Hàm lấy ra builder query để thực hiện group
    // CreatedBy nlhoang 16/09/2020
    public function getQueryGroup($query)
    {
        $groupColumn = $this->getGroupColumn();
        $tableName = $this->getTableName();
        $queryBuilder = $this->search($query, []);
        $queryBuilder->getQuery()->orders = null;
        return $queryBuilder
            ->select($tableName . '.' . $groupColumn, DB::raw('count(*) as total'))
            ->groupBy($tableName . '.' . $groupColumn)
            ->get();
    }

    // Hàm lấy ra cột thực hiện group
    // CreatedBy nlhoang 16/09/2020
    public function getGroupColumn()
    {
        return '';
    }

    protected function _needWhereInOrNotIn($fieldName, $value)
    {
        if (is_multi_array($value)) {
            return true;
        }
        return strpos($fieldName, '_in') !== false || strpos($fieldName, '_nin') !== false;
    }

    /**
     * @param $fieldName
     * @param $value
     * @return bool
     */
    protected function _buildInOrNotInConditions($fieldName, $value)
    {
        $table = '';
        if (is_multi_array($value)) {
            $table = $fieldName;
            foreach ($value as $field => $v) {
                if (!$this->_needWhereInOrNotIn($field, $v)) {
                    continue;
                }
                $this->_mapCondition($field, $v, $table);
            }
            return true;
        }
        $this->_mapCondition($fieldName, $value, $table);
        return true;
    }

    /**
     * @param $fieldName
     * @param $value
     * @param string $table
     * @return bool
     */
    protected function _buildConditions($fieldName, $value, $table = '')
    {
        if (!is_array($value) && trim($value) !== '') {
            return $this->_mapCondition($fieldName, $value, $table);
        }
        if (empty($value)) {
            return false;
        }
        foreach ($value as $field => $val) {
            $this->_buildConditions($field, $val, $fieldName);
        }
        return true;
    }

    protected function _mapCondition($fieldName, $value, $table)
    {

        $item = explode('_', $fieldName);
        if (count($item) < 2) {
            return false;
        }
        $item = array_filter($item);
        $operator = end($item);
        array_pop($item);
        $item = implode('_', $item);

        if (strpos($item, '|') !== false) {
            $tmp = explode('|', $item);
            if (count($tmp) === 2) {
                $item = $tmp[1];
                $table = $tmp[0];
            }
        }

        $field = $table ? $table . '.' . $item : $item;

        array_key_exists($operator, self::OPERATORS) ? $this->{self::OPERATORS[$operator]}($field, $value) : null;
        return true;
    }

    /**
     * @param $columns
     * @return array
     */
    protected function _buildColumn($columns)
    {
        $tableName = $this->getTableName();
        empty($columns) ? $columns = [$tableName . '.*'] : null;
        foreach ($columns as &$column) {
            $column = strpos($column, '.') === false ? $tableName . '.' . $column : $column;
        }
        return $columns;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _equal($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _notEqual($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, '!=', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _greaterThan($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, '>', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _greaterThanOrEqual($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, '>=', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _greaterThanOrEqualWithTime($field, $value)
    {
        $value .= ' 00:00:00';
        $this->setBuilder($this->getBuilder()->where($field, '>=', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _lessThan($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, '<', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _lessThanOrEqual($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, '<=', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _lessThanOrEqualWithTime($field, $value)
    {
        $value .= ' 23:59:59';
        $this->setBuilder($this->getBuilder()->where($field, '<=', $value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _in($field, $value)
    {
        $this->setBuilder($this->getBuilder()->whereIn($field, (array)$value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _notIn($field, $value)
    {
        $this->setBuilder($this->getBuilder()->whereNotIn($field, (array)$value));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _contains($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, 'LIKE', '%' . $value . '%'));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _containsFirst($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, 'LIKE', $value . '%'));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _containsLast($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, 'LIKE', '%' . $value));
        return $this;
    }

    protected function _notContains($field, $value)
    {
        $this->setBuilder($this->getBuilder()->where($field, 'NOT LIKE', '%' . $value . '%'));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _isNull($field, $value)
    {
        $this->setBuilder($this->getBuilder()->whereNull($field));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _notNull($field, $value)
    {
        $this->setBuilder($this->getBuilder()->whereNotNull($field));
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    protected function _betweenDate($field, $value)
    {
        $date = explode('~', $value);
        if (count($date) !== 2) {
            return $this;
        }

        $from = Carbon::createFromFormat('d/m/Y', $date[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date[1])->format('Y-m-d');

        $this->setBuilder($this->getBuilder()->whereBetween($field, [$from, $to]));
        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasParam($key)
    {
        return Arr::has($this->_queryParams, $key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function notEmpty($key)
    {
        return Arr::get($this->_queryParams, $key, false);
    }

    /**
     * @param $field
     * @return bool
     */
    public function hasSortField($field)
    {
        if ($this->getSortField() == $field) {
            return true;
        }
        return $this->_getKey($this->getSortField(), '.');
    }

    /**
     * @param $key
     * @param string $prefix
     * @return bool
     */
    protected function _getKey($key, $prefix = ':')
    {
        $keys = explode($prefix, $key);
        return count($keys) > 1 ? $keys[1] == $key : $keys[0] == $key;
    }

    /**
     * @param $data
     * @param $entity
     * @return mixed
     */
    public function processExtendData($data, $entity)
    {
        return $entity;
    }

    protected function _getKeyForQuery($key = '', $prefix = '')
    {
        if (empty($key)) {
            return $key;
        }
        $item = explode('_', $key);
        $operator = end($item);

        return $prefix . $operator;
    }


    public function _isUsed($id)
    {
        return false;
    }

    public function _hasDelete($id)
    {
        return true;
    }

    function isNullOrEmpty($value)
    {
        if (empty($value))
            return false;

        return true;
    }

    // API lưu thông tin đối tượng vào DB
    // CreatedBy nlhoang 27/05/2020
    public function saveEntity($userID, $parameters)
    {
        $model = $this->getModel();
        $now = new DateTime();
        DB::beginTransaction();
        try {
            if (isset($parameters['id'])) {
                $entity = $model::find($parameters['id']);
                $entity->fill($parameters);
            } else {
                $entity = $model::make();
                $param = array_filter($parameters, array(__CLASS__, 'isNullOrEmpty'));
                $entity->fill($param);
                $entity->ins_id = $userID;
                $entity->ins_date = $now;
            }
            $entity->upd_id = $userID;
            $entity->upd_date = $now;
            if (
                method_exists($this, 'preSaveEntity')
                && is_callable(array($this, 'preSaveEntity'))
            ) {
                $entity = call_user_func_array([$this, 'preSaveEntity'], array($entity, $parameters));
            }
            $entity->save();
            if (
                method_exists($this, 'saveRelationEntity')
                && is_callable(array($this, 'saveRelationEntity'))
            ) {
                call_user_func_array([$this, 'saveRelationEntity'], array($entity, $parameters));
            }
            DB::commit();
            if (
                method_exists($this, 'afterSaveSuccess')
                && is_callable(array($this, 'afterSaveSuccess'))
            ) {
                call_user_func_array([$this, 'afterSaveSuccess'], array($entity, $parameters));
            }
            return $entity;
        } catch (\Exception $e) {
            DB::rollBack();
            logError($e);
            throw new Exception($e);
        }
    }

    public function existSystemCode($table, $attribute, $code)
    {
        $entity = DB::table($table)->where([[$attribute, '=', $code]])->first();
        if ($entity != null)
            return true;
        return false;
    }

    // Lấy danh sách đối tượng theo IDs
    // CreatedBy nlhoang 04/09/2020
    public function getItemsByIds($ids,  $columns = null, $withRelation = null)
    {
        if (empty($ids)) {
            return [];
        }
        $query = $this->search(['id_in' => $ids]);
        if ($withRelation != null) {
            $query->with($withRelation);
        }
        if ($columns != null) {
            $query->select($columns);
        }
        return $query->get();
    }

    // Lấy danh sách đối tượng theo ID
    // CreatedBy nlhoang 04/09/2020
    public function getItemById($id)
    {
        if (!$id) {
            return null;
        }
        try {
            return $this->findFirstOrNew(['id' => $id]);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Lấy danh sách đối tượng theo max
    // CreatedBy nlhoang 04/09/2020
    public function getItemByCode($code, $columns = [])
    {
        if (!$code) {
            return null;
        }
        return $this->search([$this->getCode() . '_eq' => $code], $columns)->first();
    }

    public function getCode()
    {
        return 'code';
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        return [
            $table_name . '.*',
            $table_name . '.id as key',
        ];
    }

    protected function getClientBuilder($clientID,  $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->leftJoin('goods_group as gp' ,$table_name.'.goods_group_id' ,'=' ,'gp.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ])->select($columns,'gp.name as name_of_goods_group');
    }

    protected function getIgnoreClientID()
    {
        return false;
    }

    protected function getClientItems($items)
    {
        return $items;
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        return $query;
    }

    protected function getCustomClientBuilder($query, $clientID, $customerID, $table_name, $columns, $request)
    {
        return $query;
    }

    // API lấy danh sách
    // CreatedBy nlhoang 10/11/2020
    public function getDataList($clientID, $customerID, $request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $filters = $request['filters'];
        $textSearch = $request['textSearch'];

        $subFilters = isset($request['subFilters']) ? $request['filters'] : [];

        $ids = $request['ids'];
        $sorts = $request['sort'];
        $table_name = $this->getTableName();
        $columns = $this->getClientColumns($clientID, $customerID, $table_name);
        $query = $this->getClientBuilder($clientID, $customerID, $table_name, $columns, $request);
        $query = $this->getWhereTextSearch($query, $table_name, $textSearch);
        $query = $this->getCustomClientBuilder($query, $clientID, $customerID, $table_name, $columns, $request);

        $ignoreClientID = $this->getIgnoreClientID();
        if (!$ignoreClientID) {
            $query->where($table_name . '.customer_id', $customerID);
        }

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $value = isset($filter['value']) ? $filter['value'] : '';
                $field = '';
                if (Str::contains($filter['field'], '.')) {
                    $field =    $filter['field'];
                } else {
                    $field =  $table_name . '.' . $filter['field'];
                }

                if (!array_key_exists('operator', $filter)) {
                    $query->where($field, 'like', '%' .  $value . '%');
                } else {
                    switch ($filter['operator']) {
                        case 'in':
                            $query->whereIn($field,  $value);
                            break;
                        case 'equal':
                            $query->where($field, '=',  $value);
                            break;
                        case 'like':
                            $query->where($field, 'like', '%' .  $value . '%');
                            break;
                        default:
                            $query->where($field, 'like', '%' . $value . '%');
                            break;
                    }
                }
            }
        }
        if (!empty($subFilters)) {
            foreach ($filters as $subFilters) {
                $value = isset($filter['value']) ? $filter['value'] : '';
                if (Str::contains($filter['field'], '.')) {
                    $field =    $filter['field'];
                } else {
                    $field =  $table_name . '.' . $filter['field'];
                }
                $query->where($field, 'like', '%' . $value . '%');
            }
        }
        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        } else {
            $query->orderBy($table_name . '.upd_date', 'desc')->orderBy($table_name . '.id', 'desc');;
        }

        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $items = $query->get();

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get($columns);
            if ($items) {
                if ($itemSelected && 0 < sizeof($itemSelected)) {
                    foreach ($itemSelected as $obj) {
                        $items->prepend($obj);
                    }
                }
            } else {
                $items = $itemSelected;
            }
        }
        $items = $this->getClientItems($items);
        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
    }

    public function getDataForClientByID($customerID, $id)
    {
        $table_name = $this->getTableName();
        $columns = $this->getClientColumns(null, $customerID, $table_name);
        $query = $this->getClientBuilder(null, $customerID, $table_name, $columns, null);
        $item = $query->where($table_name . '.id', '=', $id)->first();
        $item = $this->getClientItems($item);
        return $item;
    }
}
