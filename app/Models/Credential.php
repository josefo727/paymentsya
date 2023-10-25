<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credential extends Model
{
    const OG_PATH = 'checkout/orderPlaced/?og=';

    protected $guarded = [
        'id'
    ];

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return void
     */
    public function setCredentials(): void
    {
        $this->load('client');

        config(['client' =>
            [
                'id' => $this->client->id,
                'terminal_id' => $this->terminal_id,
                'form_id' => $this->form_id,
                'headers' => [
                    'Authorization' => $this->payments_way_api_key,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json; charset=utf-8',
                ]
            ]
        ]);

        $urlStore = $this->client->is_production
            ? $this->client->store_domain
            : $this->client->vtex_domain;

        $urlOg = $urlStore . self::OG_PATH;

        $newVtexConfig = [
            'account_name' => $this->client->account,
            'app_key' => $this->vtex_api_app_key,
            'app_token' => $this->vtex_api_app_token,
            'payment_system' => array_map('intval', explode(',', $this->client->payment_system)),
            'url_og' => $urlOg,
            'master_domain' => $this->client->vtex_domain,
            'store_domain' => $this->client->store_domain,
        ];

        $existingVtexConfig = config('vtex') ?? [];

        $mergedConfig = array_merge($existingVtexConfig, $newVtexConfig);

        config(['vtex' => $mergedConfig]);
    }
}
