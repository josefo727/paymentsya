<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentsWayService;

class PaymentUrlController extends Controller
{
    public function __invoke(PaymentsWayService $paymentsWayService)
    {
        $order = request()->order;
        $bankCode = request()->bank_code;
        $bankName = request()->bank_name;

        $result = $paymentsWayService->createPSETransaction($order, $bankCode, $bankName);

        return response()->success($result);
    }
}
