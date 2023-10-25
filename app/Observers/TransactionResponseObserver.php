<?php

namespace App\Observers;

use App\Jobs\PaymentNotificationJob;
use App\Models\TransactionResponse;

class TransactionResponseObserver
{
    /**
     * Handle the TransactionResponse "created" event.
     */
    public function created(TransactionResponse $transactionResponse): void
    {
        if ($transactionResponse->approved) {
            PaymentNotificationJob::dispatch($transactionResponse->order->order)
                ->onQueue('default');
        }
    }
}
