<?php

namespace App\Services\Base;

/**
 * Class BaseService
 * @package App\Services\Base
 */
class BaseService
{
    /**
     * @var null
     */
    protected $_repository = null;

    /**
     * @return null
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * @param null $repository
     */
    public function setRepository($repository)
    {
        $this->_repository = $repository;
    }
}