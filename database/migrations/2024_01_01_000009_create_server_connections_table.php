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
        Schema::create('server_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('server_identifier')->unique();
            $table->string('status')->default('pending_sent');
            $table->text('api_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_connections');
    }
};
