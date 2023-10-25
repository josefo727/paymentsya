<?php

namespace App\Classes;

class Status
{
    public const NEEDS_APPROVAL = 'needs_approval';
    public const APPROVED = 'approved';
    public const CANCELED = 'canceled';
    public const ACTIONABLE_VTEX_STATE = 'payment-pending';
}
