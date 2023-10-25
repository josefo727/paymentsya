<?php

namespace App\Services;

use Exception;
use App\Models\Order;
use App\Classes\Status;
use App\Adapters\VtexAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransactionResponse;

class PaymentNotificationService
{
    protected string $vtexOrderId;
    protected Order $order;
    protected TransactionResponse $transaction;
    protected VtexAdapter $vtex;

    /**
     * @param mixed $vtexOrderId
     */
    public function __construct($vtexOrderId)
    {
        $this->vtexOrderId = $vtexOrderId;
        $this->order = app(Order::class)->getOrderByOrderId($vtexOrderId);
        $this->transaction = $this->order->TransactionResponseApproved;
        $this->order->client->credential->setCredentials();
        $this->vtex = new VtexAdapter();
    }

    /**
     * @return void
     */
    public function notifyApprovedPayment(): void
    {
        if ($this->order->status !== Status::NEEDS_APPROVAL) {
            return;
        }

        try {
            DB::beginTransaction();
                $response = $this->vtex->retrievePaymentTransaction($this->vtexOrderId);

                if (!$response['success']) {
                    throw new Exception("La orden de id {$this->vtexOrderId} no fue encontrada");
                }

                $paymentTransaction = $response['payment_transaction'];

                if (isset($paymentTransaction['status']) && $paymentTransaction['status'] === "Finished") {
                    throw new Exception("La transacción para ésta orden ya fue finalizada");
                }

                $paymentId = data_get($paymentTransaction,  'payments.0.id');

                if(is_null($paymentId)) {
                    throw new Exception("Para la order de id {$this->vtexOrderId} la transacción ya ha sido finalziada");
                };

                $response = $this->vtex->sendPaymentNotification($this->vtexOrderId, $paymentId);

                if(!$response['success']) {
                    throw new Exception("Ocurrió un error mintras se procesava la orden de id {$this->vtexOrderId}.");
                };

                sleep(30);

                $response = $this->vtex->startHandlingOrder($this->vtexOrderId);

                if(!$response['success']) {
                    throw new Exception("No se pudo iniciar la tramitación de la orden de id {$this->vtexOrderId}.");
                };

                $this->order->update([
                    'status' => Status::APPROVED,
                    'resolution_at' => $this->transaction->created_at
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
    }
}
