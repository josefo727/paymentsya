<?php

namespace App\Adapters;

use Vtex\Order\OrderClient;
use Vtex\Exception\VtexException;
use Illuminate\Support\Facades\Log;
use Vtex\MasterData\MasterDataClient;
use Josefo727\GeneralSettings\Models\GeneralSetting;

class VtexAdapter
{
    /**
     * @param $orderId
     * @return array
     */
    public function getOrder($orderId): array
    {
        $response = [
            'success' => false,
            'order' => null,
            'message' => ''
        ];

        $orderClient = new OrderClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $order = $orderClient->getOrder([
                'pathParams' => [
                    'orderId' => $orderId,
                ],
            ]);
            $response = [
                'success' => !!$order,
                'order' => $order,
                'message' => ''
            ];
        }
        catch (VtexException $exception) {
            $errorMessage = get_class($this) . ". VtexOrder: {$orderId}. " . $exception->getMessage();
            $response = [
                'success' => false,
                'order' => null,
                'message' => $errorMessage
            ];
            Log::error($errorMessage);
        }
        return $response;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function retrievePaymentTransaction($orderId): array
    {
        $response = [
            'success' => false,
            'payment_transaction' => null,
            'message' => ''
        ];

        $orderClient = new OrderClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $response = $orderClient->retrievePaymentTransaction([
                'pathParams' => [
                    'orderId' => $orderId,
                ],
            ]);

            $response = [
                'success' => true,
                'payment_transaction' => $response,
                'message' => ''
            ];
        }
        catch (VtexException $exception) {
            $errorMessage = get_class($this) . ". retrievePaymentTransaction, Order: {$orderId}. " . $exception->getMessage();
            $response['message'] = $errorMessage;
            Log::error($errorMessage);
        }

        return $response;
    }

    /**
     * @param $orderId
     * @param $paymentId
     * @return array
     */
    public function sendPaymentNotification($orderId, $paymentId): array
    {
        $response = [
            'success' => true,
            'message' => 'Payment successfully notified'
        ];

        $orderClient = new OrderClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $orderClient->sendPaymentNotification([
                'pathParams' => [
                    'orderId'   => $orderId,
                    'paymentId' => $paymentId,
                ],
            ]);
        } catch (VtexException $exception) {
            $errorMessage = get_class($this) . ". sendPaymentNotification, Order: {$orderId}. " . $exception->getMessage();
            Log::error($errorMessage);
            $response = [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        return $response;
    }

    /**
     * @param $orderId
     * @return array
     */
    public function startHandlingOrder($orderId): array
    {
        $response = [
            'success' => true,
            'message' => 'Order processing successfully initiated'
        ];

        $orderClient = new OrderClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $orderClient->startHandlingOrder([
                'pathParams' => [
                    'orderId' => $orderId,
                ],
            ]);
        }
        catch (VtexException $exception) {
            $errorMessage = get_class($this) . ". startHandlingOrder, Order: {$orderId}. " . $exception->getMessage();
            Log::error($errorMessage);
            $response = [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        return $response;
    }

    /**
     * @return array<string,mixed>
     * @param string $userId
     */
    public function getEmailGiveUserId(string $userId): array
    {
        $response = [
            'success' => false,
            'email' => null
        ];

        $masterDataClient = new MasterDataClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $client = $masterDataClient->searchDocuments([
                'pathParams' => [
                    'data_entity_name' => 'CL',
                ],
                'queryParams' => [
                    '_fields' => 'email',
                    '_where' => "(userId={$userId})"
                ]
            ]);

            $email = data_get($client, '0.email');

            $response = [
                'success' => !!$email,
                'email' => $email
            ];
        }
        catch (VtexException $exception) {
            Log::error($exception->getMessage());
        }

        return $response;
    }

    /**
     * @param string $orderId
     * @return array<string,mixed>
     */
    public function cancelOrder(string $orderId): array
    {
        $response = [
            'success' => false,
            'message' => null,
            'data' => []
        ];

        $orderClient = new OrderClient([
            'credentials' => $this->getApiCredentials(),
        ]);

        try {
            $data = $orderClient->cancelOrder([
                'pathParams' => [
                    'orderId' => $orderId,
                ],
            ]);

            if (!isset($data['orderId'])) {
                throw new VtexException("Error processing request", 1);
            }

            $response = [
                'success' => isset($data['orderId']),
                'message' => 'Order successfully cancelled',
                'data' => $data
            ];
        }
        catch (VtexException $exception) {
            $errorMessage = get_class($this) . ". cancelOrder, Order: {$orderId}. " . $exception->getMessage();
            $response['message'] = $errorMessage;
            Log::error($errorMessage);
        }

        return $response;
    }

    /**
     * @param string|null $environment
     * @return array
     */
    public function getApiCredentials(string $environment = null): array
    {
        return [
            'accountName' => config('vtex.account_name'),
            'environment' => $environment ?? config('vtex.environment'),
            'appKey'      => config('vtex.app_key'),
            'appToken'    => config('vtex.app_token'),
        ];
    }
}
