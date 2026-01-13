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
        Schema::create('user_states', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unsigned()->comment('ID телеграма пользователя');;
            $table->string('username')->nullable()->comment('Никнейм телеграм пользователя');
            $table->string('full_name')->nullable()->comment('Имя телеграм пользователя');
            $table->foreignId('access_level_id')->constrained('access_levels')->cascadeOnUpdate()
                ->comment('ID уровня доступа');
            $table->string('state')->nullable()->comment('Статус пользователя');
            $table->string('command')->nullable()->comment('Активная команда');
            $table->json('data')->nullable()->comment('json стейт для команд');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_states');
    }
};
