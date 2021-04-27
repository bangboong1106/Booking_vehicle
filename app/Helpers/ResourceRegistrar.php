<?php

namespace App\Helpers;

use Illuminate\Routing\ResourceRegistrar as OriginalRegistrar;
use Illuminate\Routing\Route;

/**
 * Class ResourceRegistrar
 * @package App\Helpers
 */
class ResourceRegistrar extends OriginalRegistrar
{

    /**
     * @var array
     */
    protected $resourceDefaults = array('getList',
        'exportCsv', 'massDestroy', 'confirm', 'valid', 'ajaxSearch',
        'index', 'create', 'store', 'duplicate', 'auditing', 'deleted',
        'show', 'edit', 'update', 'destroy');

    /**
     * @var array
     */
    protected static $verbs = [
        'create' => 'create',
        'edit' => 'edit',
        'duplicate' => 'duplicate',
        'confirm' => 'confirm',
        'massDestroy' => 'mass-destroy',
        'exportCsv' => 'export-csv',
        'getList' => 'get-list',
        'auditing' => 'auditing',
        'deleted' => 'deleted',
        'ajaxSearch' => 'ajaxSearch'
    ];

    protected function addResourceDuplicate($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['duplicate'];

        $action = $this->getResourceAction($name, $controller, 'duplicate', $options);

        return $this->router->any($uri, $action);
    }

    protected function addResourceDeleted($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['deleted'];

        $action = $this->getResourceAction($name, $controller, 'deleted', $options);

        return $this->router->get($uri, $action);
    }

    protected function addResourceGetList($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['getList'];

        $action = $this->getResourceAction($name, $controller, 'getList', $options);

        return $this->router->get($uri, $action);
    }

    /**
     * Add the confirm method for a resourceful route.
     *
     * @param  string $name
     * @param  string $base
     * @param  string $controller
     * @param  array $options
     * @return Route
     */
    protected function addResourceExportCsv($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['exportCsv'];

        $action = $this->getResourceAction($name, $controller, 'exportCsv', $options);

        return $this->router->get($uri, $action);
    }

    /**
     * Add the valid method for a resourceful route.
     *
     * @param  string $name
     * @param  string $base
     * @param  string $controller
     * @param array $options
     * @return Route
     */
    protected function addResourceValid($name, $base, $controller, $options = [])
    {
        $uri = $this->getResourceUri($name) . '/valid/{' . $base . '?}';

        return $this->router->post($uri, $this->getResourceAction($name, $controller, 'valid', $options));
    }

    /**
     * Add the confirm method for a resourceful route.
     *
     * @param  string $name
     * @param  string $base
     * @param  string $controller
     * @param  array $options
     * @return Route
     */
    protected function addResourceConfirm($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['confirm'];

        $action = $this->getResourceAction($name, $controller, 'confirm', $options);

        return $this->router->get($uri, $action);
    }


    /**
     * Add the confirm method for a resourceful route.
     *
     * @param  string $name
     * @param  string $base
     * @param  string $controller
     * @param  array $options
     * @return Route
     */
    protected function addResourceMassDestroy($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['massDestroy'];

        $action = $this->getResourceAction($name, $controller, 'massDestroy', $options);

        return $this->router->delete($uri, $action);
    }

    protected function addResourceAuditing($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['auditing'];
        $action = $this->getResourceAction($name, $controller, 'auditing', $options);
        return $this->router->get($uri, $action);
    }

    protected function addResourceAjaxSearch($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/' . static::$verbs['ajaxSearch'];
        $action = $this->getResourceAction($name, $controller, 'ajaxSearch', $options);
        return $this->router->get($uri, $action);
    }
}