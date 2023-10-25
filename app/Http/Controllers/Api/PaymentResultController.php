<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransactionResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Order;

class PaymentResultController extends Controller
{
    /**
     */
    public function __invoke(Request $request)
    {

        $order = Order::getOrderByExternalOrder($request->externalorder);

        if (is_null($order)) {
            return response()->error('La orden no ha sido encontrada.', Response::HTTP_NOT_FOUND);
        }

        app(TransactionResponse::class)->registerTransaction($request, $order->id);

        return response()->success(['message' => 'Transaction registrada correctamente.'], Response::HTTP_CREATED);
    }
}
