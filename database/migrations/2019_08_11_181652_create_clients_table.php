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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('contact_person', 50);
            $table->string('email', 50)->unique();
            $table->string('phone', 20);
            $table->string('account', 30);
            $table->string('store_name', 30);
            $table->string('vtex_domain');
            $table->string('store_domain');
            $table->boolean('is_production')->default(false);
            $table->string('app_key', 30);
            $table->string('app_token', 100);
            $table->string('payment_system', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
