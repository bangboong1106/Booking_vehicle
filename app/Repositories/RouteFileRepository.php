<?php

namespace App\Repositories;

use App\Model\Entities\RouteFile;
use App\Repositories\Base\CustomRepository;

class RouteFileRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return RouteFile::class;
    }

    public function getRouteFileWithRouteID($route_id, $type)
    {
        if ($route_id)
            return $this->search([
                'route_id_eq' => $route_id,
                'type_eq' => $type,
                'del_flag' => 0
            ])->get();
        return null;
    }

    public function getRouteFileIdWithRouteID($route_id, $type)
    {
        if ($route_id)
            return $this->search([
                'route_id_eq' => $route_id,
                'type_eq' => $type,
                'del_flag' => 0
            ])->pluck('file_id')->toArray();
        return null;
    }

    public function getRouteFileCostWithRouteID($route_id)
    {
        if ($route_id)
            return $this->search([
                'route_id_eq' => $route_id,
                'type_eq' => config('constant.ROUTE_FILE_TYPE_COST')
            ])->get();
        return null;
    }

    public function getRouteFileWithFileIdAndRouteId($fileId, $routeId, $type)
    {
        if ($fileId && $routeId)
            return $this->search([
                'file_id_eq' => $fileId,
                'type_eq' => $type,
                'route_id_eq' => $routeId
            ])->first();
        return null;
    }
}