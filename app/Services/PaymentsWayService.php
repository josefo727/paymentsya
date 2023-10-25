<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaymentsWayService
{
    protected $headers = [];

    protected $baseUrl = 'https://serviceregister.paymentsway.co/ClientAPI/';

    public function __construct()
    {
        $this->headers = config('client.headers');
    }

    /**
     * @return array
     */
    public function getBankList(): array
    {
        $response = Http::withHeaders(
            $this->headers
        )->get($this->baseUrl . 'ObtenerListadoBancos');

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    /**
     * @param string $orderNumber
     * @return array
     */
    public function createPSETransaction(string $order, string $bankCode, string $bankName): array
    {
        $order = Order::query()->where('order', $order)->first();

        if (!$order) {
            return ['error' => 'Order not found'];
        }

        $order->bank_code = $bankCode;
        $order->bank_name = $bankName;
        $order->order_prefix = $this->prefixIncrement($order->order_prefix);
        $order->external_order = $order->order_prefix . '-' . $order->order;

        $payload = [
            'amount' => $order->amount,
            'PersonType' => $order->person_type,
            'identification_type' => $order->identification_type,
            'Documento' => $order->document,
            'Correo' => $order->email,
            'Nombres' => $order->first_name,
            'Apellidos' => $order->last_name,
            'Celular' => $this->formatPhoneNumber($order->cell_phone),
            'Direccion' => substr($order->address, -64),
            'external_order' => $order->external_order,
            'CodigoBanco' => $order->bank_code,
            'NombreBanco' => $order->bank_name,
            'entityurl' => $order->entity_url,
            'terminal_id' => $order->terminal_id,
            'form_id' => $order->form_id,
            'ip' => $order->ip,
        ];

        $result = ['url_payment' => null];

        $response = Http::withHeaders($this->headers)
            ->post($this->baseUrl . 'CrearTransaccionPSE', $payload);

        $order->save();
        if ($response->successful()) {
            $resp = $response->json();

            if ($resp['STATUS'] === true) {
                $result['url_payment'] = $resp['DATA']['pseURL'];
                $order->payment_url = $result['url_payment'];
            }
        }

        $order->save();

        return $result;
    }

    /**
     * @return string
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = ltrim($phoneNumber, '+57');

        if (strlen($phoneNumber) > 10) {
            $phoneNumber = substr($phoneNumber, -10);
        }

        return $phoneNumber;
    }

    /**
     * @return string
     */
    public function prefixIncrement(string $prefix): string
    {
        $prefix = intval($prefix) + 1;
        $prefix = str_pad($prefix, 2, "0", STR_PAD_LEFT);
        return $prefix;
    }
}
