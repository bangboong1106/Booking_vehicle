<?php

namespace App\Events;

class OrderExcelEvent
{
    public $update;
    public $orderList;
    public $orderOldList;
    public $routeIdUpdates;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param $update
     * @param $orderList
     * @param $orderOldList
     * @param $routeIdUpdates
     * @param $userId
     */

    public function __construct($update, $orderList, $orderOldList, $routeIdUpdates, $userId)
    {
        $this->update = $update;
        $this->orderList = $orderList;
        $this->orderOldList = $orderOldList;
        $this->routeIdUpdates = $routeIdUpdates;
        $this->userId = $userId;
    }

}