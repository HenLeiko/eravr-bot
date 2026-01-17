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
        Schema::create('google_calendar_syncs', function (Blueprint $table) {
            $table->id();
            $table->string('calendar_id')->comment('id календаря');
            $table->string('sync_token')->comment('Токен синхронизации Эвентов');
            $table->string('channel_id')->comment('id канала нотиф вебхука');
            $table->string('resource_id')->comment('id ресурса от гугла');
            $table->dateTime('channel_expiration')->comment('Дата-время смерти вебхука');
            $table->dateTime('last_sync_at')->comment('Дата-время последний синхронизации эвентов');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_calendar_syncs');
    }
};
