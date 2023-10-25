<?php

namespace App\Services;

use App\Adapters\VtexAdapter;
use Carbon\Carbon;
use App\Classes\Status;
use App\Models\Order;

class OrderService
{
    protected string $orderGroup;
    protected string $orderId;
    protected string $paymentSystemKey;
    protected string $urlRedirect;

    protected array $response = [
        'found' => false,
        'needs_to_process' => false,
        'message' => 'Orden no encontrada',
        'url' => null
    ];

    public function __construct(string $orderGroup) {
        $this->orderGroup = $orderGroup;
        $this->orderId = $orderGroup . '-01';
        $this->paymentSystemKey = config('vtex.order.payment_system_key');
        $this->urlRedirect = (new DomainUrlService())->generate(request())
            . '/checkout/orderPlaced?og='
            . $orderGroup;
    }

    /**
     * @return array
     */
    public function getOrderInfo(): array
    {
        $info = app(VtexAdapter::class)->getOrder($this->orderId);

        if (!$info['success']) {
            return $this->response;
        }

        $order = $info['order'];

        if ($this->needsToBeProcessed($order)) {
            $newOrder = $this->buildNewOrder($order);

            $savedOrder = Order::query()
                ->firstOrCreate(
                    ['order' => $newOrder['order']],
                    $newOrder
                );

            $this->response['found'] = true;
            $this->response['needs_to_process'] = false;
            $this->response['message'] = 'En espera de respuesta.';

            if ($savedOrder->status === Status::NEEDS_APPROVAL && ($savedOrder->wasRecentlyCreated || is_null($savedOrder->payment_url))) {
                $this->response['needs_to_process'] = true;
                $this->response['message'] = 'Se necesita procesar pago.';
            }

            return $this->response;
        }

        $this->response['found'] = true;
        $this->response['needs_to_process'] = false;
        $this->response['message'] = 'No se necesita procesar pago.';

        return $this->response;
    }

    /**
     * @param array $order
     * @return bool
     */
    public function needsToBeProcessed(array $order): bool
    {
        $paymentSystem = (string) data_get($order, $this->paymentSystemKey);
		$vtexPaymentSystems = config('vtex.payment_system');
        return is_array($vtexPaymentSystems)
            && in_array($paymentSystem, $vtexPaymentSystems)
            && $order['status'] === Status::ACTIONABLE_VTEX_STATE;
    }

    /**
     * @return array<string,mixed>
     * @param array $order
     */
    public function buildNewOrder($order): array
    {
        $client = data_get($order, 'clientProfileData');
        $email = $this->getClientEmail($client['userProfileId']);
		$address = data_get($order, 'shippingData.address');

        return [
            "amount" => $order['value'] / 100,
            "person_type" => $client['isCorporate'] ? "1" : "0",
            "identification_type" => $client['isCorporate'] ? 6 : 4,
            "document" => $client['isCorporate'] ? $client['corporateDocument'] : $client['document'],
            "email" => $email,
            "first_name" => $client['firstName'],
            "last_name" => $client['lastName'],
            "cell_phone" => $client['isCorporate'] ? $client['corporatePhone'] : $client['phone'],
            "address" => $this->buildAddress($address),
            "order" => $order['orderId'],
            "entity_url" => config('vtex.url_og') . $order['orderGroup'],
            "ip" => request()->getClientIp(),
            'vtex_status' => $order['status'],
            'status' => Status::NEEDS_APPROVAL,
			'order_creation_at' => $order['creationDate'],
        ];
    }

    /**
     * @param array<int,mixed> $address
     * @return string
     */
    public function buildAddress(array $address): string
	{
		$addressElements = [
			data_get($address, 'street'),
			data_get($address, 'neighborhood'),
			data_get($address, 'complement'),
			data_get($address, 'city'),
			data_get($address, 'state'),
			data_get($address, 'postalCode'),
			data_get($address, 'country'),
			data_get($address, 'receiverName')
		];

		$filteredAddressElements = array_filter($addressElements, function($value) {
			return !is_null($value) && $value !== '';
		});

		$fullAddress = implode(', ', $filteredAddressElements);

		return $fullAddress ?: 'Sin dirección registrada / Recogida en tienda';
	}

    /*
     * @param string $userProfileId
     * @return string
     */
    public function getClientEmail(string $userProfileId): string
    {
        $response = app(VtexAdapter::class)->getEmailGiveUserId($userProfileId);

        return $response['success']
            ? $response['email']
            : '';
    }
}
