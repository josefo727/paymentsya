<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderCancellationService;

class CancelUnpaidOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:unpaid-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel unpaid orders that need approval';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        app(OrderCancellationService::class)->cancelUnpaidOrders();
    }
}
