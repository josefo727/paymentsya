<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionResponse extends Model
{
    protected $guarded = ['id'];

    protected const APPROVED_ID = 34;

    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return void
     */
    public function registerTransaction(Request $request, int $orderId): void
    {
        $jsonString = json_encode($request->all());

        static::query()->create([
            'order_id' => $orderId,
            'status' => $request->idstatus['nombre'],
            'approved' => $request->idstatus['id'] === self::APPROVED_ID,
            'response' => $jsonString,
        ]);
    }
}
