<?php

namespace App\Model\Scopes\Base;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Trait Base
 * @package App\Model\Scopes\Base
 */
Trait Base
{
    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeWithoutTrashed($query)
    {
        return $query->withoutTrashed();
    }

    /**
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function scopeSortById($query, $type = 'ASC')
    {
        return $this->sortById($query, $type);
    }

    /**
     * @return mixed
     */
    public function scopeSortByIdDesc($query)
    {
        return $this->sortById($query, 'desc');
    }

    // supporter

    /**
     * @param QueryBuilder $query
     * @param string $type
     * @return mixed
     */
    public function sortById($query, $type = 'ASC')
    {
        return $query->orderBy('id', $type);
    }

    /**
     * @param QueryBuilder $query
     * @param $status
     * @return mixed
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where($this->getField('status'), $status);
    }

    /**
     * @param QueryBuilder $query
     * @param $id
     * @return mixed
     */
    public function scopeWithId($query, $id)
    {
        return $query->where('id', '=', $id);
    }

    /**
     * @param $query
     * @param $lang
     * @return mixed
     */
    public function scopeWithLang($query, $lang)
    {
        if (is_null($lang)) {
            return $query;
        }
        return $query;
    }
}

