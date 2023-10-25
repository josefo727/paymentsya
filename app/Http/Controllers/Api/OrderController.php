<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * Returns order information in Vtex
     */
    public function __invoke()
    {
		$orderInfo = (new OrderService(request()->order_group))->getOrderInfo();

        return response()->success($orderInfo);
    }

}
