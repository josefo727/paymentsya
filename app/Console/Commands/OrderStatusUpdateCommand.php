<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderStatusUpdateService;

class OrderStatusUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update order statuses';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        app(OrderStatusUpdateService::class)->updateOrdersStatus();
    }
}
