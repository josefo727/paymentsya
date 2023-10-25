<?php

namespace App\Services;

use App\Adapters\VtexAdapter;
use App\Classes\Status;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderCancellationService
{
    /**
     * @return void
     */
    public function cancelUnpaidOrders(): void
    {
        $orders = Order::query()
            ->whereDoesntHave('transactionResponseApproved')
            ->where('status', Status::NEEDS_APPROVAL)
            ->where('order_creation_at', '<=', Carbon::now('UTC')->subDay())
            ->get();

        $orders->each(fn($order) => $this->cancelOrder($order));
    }

    /**
     * @return void
     */
    public function cancelOrder(Order $order): void
    {
        if ($order->status !== Status::NEEDS_APPROVAL || $order->transactionResponseApproved()->exists()) {
            return;
        }

        $order->client->credential->setCredentials();

        try {
            $response = app(VtexAdapter::class)->cancelOrder($order->order);
            $success = $response['success'];
            if ($response['success']) {
                $order->status = Status::CANCELED;
                $order->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
