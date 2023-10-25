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
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->string('dashboard', 128);
            $table->string('email', 64);
            $table->string('password', 64);
            $table->unsignedInteger('merchant_id');
            $table->unsignedInteger('terminal_id');
            $table->unsignedInteger('form_id');
            $table->text('payments_way_api_key');
            $table->string('vtex_api_app_key', 50);
            $table->string('vtex_api_app_token');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
