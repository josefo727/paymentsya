<?php

namespace App\Classes;

class Status
{
    public const NEEDS_APPROVAL = 'needs_approval';
    public const APPROVED = 'approved';
    public const CANCELED = 'canceled';
    public const ACTIONABLE_VTEX_STATE = 'payment-pending';
    public const PW_PENDING = [
        'id' => 35,
        'name' => 'Pendiente',
    ];
    public const PW_FAILED = [
        'id' => 36,
        'name' => 'Fallida',
    ];
    public const PW_REJECTED = [
        'id' => 37,
        'name' => 'Rechazada ClearSale',
    ];
    public const PW_CANCELLED = [
        'id' => 38,
        'name' => 'Cancelada'
    ];
    const PY_STATES_TRIGGERING_CANCELLATION = [
        self::PW_FAILED,
        self::PW_REJECTED,
        self::PW_CANCELLED,
    ];
}
