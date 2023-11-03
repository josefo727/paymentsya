<?php

namespace App\Models;

use App\Classes\Status;
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
     * @return HasOne
     */
    public function transactionResponsePending(): HasOne
    {
        return $this->hasOne(TransactionResponse::class)
            ->where('status', Status::PW_PENDING['name']);
    }

    public function transactionResponseRejected(): HasMany
    {
        $statuses = collect(Status::PY_STATES_TRIGGERING_CANCELLATION)->map(fn($status) => $status['name'])->toArray();
        return $this->hasMany(TransactionResponse::class)
            ->whereIn('status', $statuses);
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

    /**
     * @return bool
     */
    public function cannotBeCancelled(): bool
    {
        return $this->status !== Status::NEEDS_APPROVAL
            || $this->transactionResponseApproved()->exists();
    }

    /**
     * @return bool
     */
    public function needsApproval(): bool
    {
        return $this->status === Status::NEEDS_APPROVAL
			&& !$this->transactionResponsePending()->exists()
            && (
                $this->wasRecentlyCreated
                || is_null($this->payment_url)
                || !$this->transactionResponseRejected()->exists()
            );
    }

}
