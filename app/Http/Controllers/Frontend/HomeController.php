<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Base\FrontendController;
use App\Model\Entities\Order;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Input;
use Response;

class HomeController extends FrontendController
{
    protected $_orderRepository;

    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    public function __construct(OrderRepository $orderRepository)
    {
        parent::__construct();
        $this->setOrderRepository($orderRepository);

    }

    public function index()
    {
        return View('frontend.home.application');
    }

    public function main()
    {
        return View('frontend.home.dashboard');
    }

    public function about()
    {
        return View('frontend.home.about');
    }

    public function support()
    {
        return View('frontend.home.support');
    }

    public function policy()
    {
        return View('frontend.home.policy');
    }

    public function covid()
    {
        return View('frontend.home.covid');
    }
}
