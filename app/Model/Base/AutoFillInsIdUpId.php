<?php

namespace App\Model\Base;

/**
 * Trait AutoFillInsIdUpId
 * @package App\Model\Base
 */
/**
 * Trait AutoFillInsIdUpId
 * @package App\Model\Base
 */
trait AutoFillInsIdUpId
{
    /**
     * @var bool
     */
    protected $_stopFillActionAt = true;

    /**
     * @return bool
     */
    public function isHasActionBy()
    {
        return $this->_hasActionBy;
    }

    /**
     * @return bool
     */
    public function isStopFillActionAt()
    {
        return $this->_stopFillActionAt;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setStopFillActionAt($value)
    {
        $this->_stopFillActionAt = $value;
        return $this;
    }

    /**
     * @param bool $hasActionBy
     */
    public function setHasActionBy($hasActionBy)
    {
        $this->_hasActionBy = $hasActionBy;
    }

    /**
     * @var bool
     */
    protected $_allowOverride = true;

    /**
     * @return bool
     */
    public function isAllowOverride()
    {
        return $this->_allowOverride;
    }

    /**
     * @param bool $allowOverride
     */
    public function setAllowOverride($allowOverride)
    {
        $this->_allowOverride = $allowOverride;
    }

    /**
     * @return mixed
     */
    public function getCreatedAtAttribute()
    {
        return $this->attributes[getCreatedAtColumn()];
    }

    /**
     * @param $value
     */
    public function setCreatedAt($value)
    {
        $this->_allowFillActionAt() ? $this->attributes[getCreatedAtColumn()] = $value : null;
        $this->_allowFillActionBy() && getCreatedByColumn() ? $this->attributes[getCreatedByColumn()] = getCurrentUserId() : null;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return isset($this->attributes[getUpdatedAtColumn()]) ? $this->attributes[getUpdatedAtColumn()] : null;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->id && $this->_allowFillActionAt() ? $this->attributes[getUpdatedAtColumn()] = $value : null;
        $this->fillUpdatedBy();
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDeletedAt($value)
    {
        $this->attributes[getDeletedAtColumn()] = $value;
        $this->fillDeletedBy();
        return $this;
    }

    public function fillUpdatedBy($fromRelation = false)
    {
        ($this->id || $fromRelation) && $this->_allowFillActionBy() && getUpdatedByColumn() ? $this->attributes[getUpdatedByColumn()] = getCurrentUserId() : null;
        return $this;
    }

    public function fillDeletedBy()
    {
        $this->_allowFillActionBy() && getDeletedByColumn() ? $this->attributes[getDeletedByColumn()] = getCurrentUserId() : null;
        return $this;
    }


    protected function _allowFillActionBy()
    {
        return $this->isHasActionBy() && $this->isAllowOverride();
    }

    protected function _allowFillActionAt()
    {
        return $this->isStopFillActionAt();
    }
}
