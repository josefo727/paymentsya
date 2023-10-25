<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentsWayService;

class BankController extends Controller
{
    public function __invoke(PaymentsWayService $paymentsWayService)
    {
        $banks = collect($paymentsWayService->getBankList());

        $banks = $banks->filter(function ($bank) {
            return $bank['estado'] === true;
        })->values();

        return response()->success($banks);
    }
}
