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
        Schema::create('contest_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)
                ->nullable()
                ->comment('Название приза');
            $table->string('description', 255)
                ->nullable()
                ->comment('Описание приза');
            $table->integer('ref_amount')
                ->default(0)
                ->comment('Кол-во рефералов для приза');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Статус активности приза');
            $table->foreignId('contest_id')
                ->constrained('contests')
                ->nullOnDelete()
                ->comment('ID конкурса');
            $table->timestamps();

            $table->index('ref_amount');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_rewards');
    }
};
