<?php

namespace App\Models;

use App\Classes\Status;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionResponse extends Model
{
    protected $guarded = ['id'];

    protected const APPROVED_ID = 34;

    protected $casts = [
        'approved' => 'boolean',
        'response' => 'json',
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

    /**
     *
     * @return bool
     */
    public function shouldBeCancelledAccordingToItsStatus(): bool
    {
        $id = data_get($this->response, 'idstatus.id');
        return collect(Status::PY_STATES_TRIGGERING_CANCELLATION)
            ->map(fn($status) => $status['id'])
            ->contains($id);
    }
}
