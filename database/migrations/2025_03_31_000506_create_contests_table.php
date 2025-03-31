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
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->comment('Оглавление конкурса');
            $table->text('description')->comment('Описание конкурса');
            $table->string('image', 255)
                ->nullable()
                ->default(null)
                ->comment('Картинка/гифка к посту конкурса');
            $table->dateTime('started_at')->comment('Дата и время начала конкурса');
            $table->dateTime('ended_at')->comment('Дата и время конца конкурса');
            $table->text('tiny_description')
                ->nullable()
                ->default(null)
                ->comment('Сокращённое описание конкурса для личных сообщений');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Статус проведения конкурса');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
