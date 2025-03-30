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
        Schema::create('telegram_channel_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id')->nullable()->comment('ID чата или канала');
            $table->unsignedBigInteger('user_id')->comment('ID пользователя');
            $table->string('status', 20)->comment('Статус пользователя: member|administrator|restricted|left|kicked');
            $table->string('ref_code', 8)->comment('Реферальный код');
            $table->foreignId('ref_by')
                ->nullable()
                ->constrained('telegram_channel_members')
                ->nullOnDelete();
            $table->dateTime('timeout')->nullable()->comment('Ограничение на участие в конкурсах');
            $table->boolean('is_participating')->default(false)->comment('Метка участия в конкурсе');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_channel_members');
    }
};
