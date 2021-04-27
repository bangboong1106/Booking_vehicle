<?php

namespace App\Observers;

use App\Model\Entities\Order;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderHistoryRepository;
use App\Services\OrderCustomerService;

class OrderObserver
{
    private $_orderCustomerService;
    private $_orderCustomerRepository;
    private $_orderHistoryRepository;

    /**
     * OrderObserver constructor.
     * @param OrderCustomerService $orderCustomerService
     * @param OrderCustomerRepository $orderCustomerRepository
     * @param OrderHistoryRepository $orderHistoryRepository
     */
    public function __construct(OrderCustomerService $orderCustomerService,
                                OrderCustomerRepository $orderCustomerRepository,
                                OrderHistoryRepository $orderHistoryRepository)
    {
        $this->_orderCustomerService = $orderCustomerService;
        $this->_orderCustomerRepository = $orderCustomerRepository;
        $this->_orderHistoryRepository = $orderHistoryRepository;
    }

    /**
     * Handle the updated event.
     *
     * @param Order $order
     * @return void
     */
    public function updated(Order $order)
    {
        //Cập nhật dhkh
        $orderCustomer = $this->_orderCustomerRepository->getItemById($order->order_customer_id);
        $this->_orderCustomerService->updateOrderCustomerInfo($orderCustomer);

        if ($order->isDirty('status')) {
            // status has changed
            //$new_status = $order->status;
            //$old_status = $order->getOriginal('status');
            $this->_orderHistoryRepository->processCreateOrderHistory($order);
        } else {
            $this->_orderHistoryRepository->processUpdateOrderHistory($order);
        }
    }

}