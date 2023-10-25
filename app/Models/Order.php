<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = ['id'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($order) {
            $order->client_id = config('client.id');
            $order->terminal_id = config('client.terminal_id');
            $order->form_id = config('client.form_id');
            $order->order_prefix = '00';
            $order->external_order = '00-' . $order->order;
        });

        static::updating(function ($order) {
        });
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany
     */
    public function transactionResponse(): HasMany
    {
        return $this->hasMany(TransactionResponse::class);
    }

    /**
     * @return HasOne
     */
    public function transactionResponseApproved(): HasOne
    {
        return $this->hasOne(TransactionResponse::class)
            ->where('approved', true);
    }

    /**
     * @param string $externalOrder
     * @return string
     */
    public static function removePrefix(string $externalOrder): string
    {
        $parts = explode('-', $externalOrder);
        $relevantParts = array_slice($parts, 1);
        return implode('-', $relevantParts);
    }

    /**
     * @return self|null
     */
    public static function getOrderByOrderId(string $order): self|null
    {
        return static::query()
            ->firstWhere('order', $order);
    }

    /**
     * @param string $externalOrder
     * @return ?Order
     */
    public static function getOrderByExternalOrder(string $externalOrder): ?Order
    {
        $orderId = static::removePrefix($externalOrder);

        return static::getOrderByOrderId($orderId);
    }

}
