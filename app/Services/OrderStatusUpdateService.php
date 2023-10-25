<?php

namespace App\Services;

use App\Adapters\VtexAdapter;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdateService
{
    /**
     * @return void
     */
    public function updateOrdersStatus(): void
    {
        $orders = Order::query()
            ->where('order_creation_at', '>=', Carbon::now('UTC')->subDay())
            ->get();

        $orders->each(fn($order) => $this->updateOrderStatus($order));
    }

    /**
     * @return void
     */
    public function updateOrderStatus(Order $order): void
    {
        $order->client->credential->setCredentials();
        $info = app(VtexAdapter::class)->getOrder($order->order);
        $success = $info['success'];
        if (!$success) return;
        try {
            $order->vtex_status = data_get($info, 'order.status');
            $order->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
