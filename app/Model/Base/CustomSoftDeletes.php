<?php

namespace App\Model\Base;

use App\Model\Scopes\Base\SoftDeleting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * Trait BaseTrait
 * @package App\Model\Base
 */
trait CustomSoftDeletes
{
    use SoftDeletes;

    public function initializeSoftDeletes()
    {
        $this->dates = [];
    }

    public function scopeWithActive($query)
    {
        return $query->where(getDelFlagColumn(), '=', getDelFlagColumn('active'))->orWhereNull(
            getDelFlagColumn()
        );
    }
    /**
     * @var string
     */

    /**
     * @return string
     */
    public function getDeleteFlagColumn()
    {
        return getDelFlagColumn();
    }

    /**
     *
     * @param $deleted
     * @return string
     */
    public function getDelFlagValue($deleted = false)
    {
        return $deleted ? getSystemConfig('del_flag_column.deleted', 1) : getSystemConfig('del_flag_column.active', 0);
    }

    public function getQualifiedDelFlagColumn()
    {
        return $this->getTable() . '.' . $this->getDeleteFlagColumn();
    }

    public static function bootSoftDeletes()
    {
        $object = new SoftDeleting();
        $object->setModel(with(new static));
        static::addGlobalScope($object);
    }

    public function getAllGlobalScopes()
    {
        return static::$globalScopes;
    }

    protected function runSoftDelete()
    {
        $query = $this->newQueryWithoutScopes()->where($this->getKeyName(), $this->getKey());
        if ($this->hasDelFlag()) {
            $columns = $this->getUpdateColumnsWhenHasDelFlag();
            return $query->update($columns);
        }
        method_exists($this, 'fillDeletedBy') ? $this->fillDeletedBy() : null;
        method_exists($this, 'fillUpdatedBy') ? $this->fillUpdatedBy() : null;
        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getDeletedAtColumn()} = $time;

        if ($this->timestamps) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }
        $query->update($columns);
    }

    public function getUpdateColumnsWhenHasDelFlag($fromRelation = false)
    {
        $columns = [];
        method_exists($this, 'fillDeletedBy') ? $this->fillDeletedBy() : null;
        method_exists($this, 'fillUpdatedBy') ? $this->fillUpdatedBy($fromRelation) : null;
        $columns[$this->getDeleteFlagColumn()] = $this->getDelFlagValue(true);
        $diff = array_diff_assoc($this->attributesToArray(), $this->getOriginal());
        $columns += $diff;
        return $columns;
    }

    public function hasDelFlag()
    {
        return $this->getDeleteFlagColumn() && empty($this->dates);
    }

    public function getDeletedAtColumn()
    {
        return getDeletedAtColumn();
    }
}
