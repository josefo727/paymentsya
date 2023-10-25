<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->unsignedDecimal('amount', 12, 2);
            $table->string('person_type', 10);
            $table->unsignedTinyInteger('identification_type');
            $table->string('document', 20);
            $table->string('email', 64);
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('cell_phone', 20);
            $table->string('address');
            $table->string('order');
            $table->string('order_prefix', 2);
            $table->string('external_order');
            $table->string('bank_code', 20)->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->string('entity_url');
            $table->string('payment_url')->nullable();
            $table->integer('terminal_id');
            $table->integer('form_id');
            $table->ipAddress('ip');
            $table->string('vtex_status', 50)->default('payment-pending');
            $table->string('status', 50)->default('needs_approval');
            $table->timestamp('order_creation_at');
            $table->timestamp('resolution_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
