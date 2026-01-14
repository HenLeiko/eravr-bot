<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user_states')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate()
                ->comment('ИД пользователя');
            $table->foreignId('club_id')->constrained('clubs')
                ->cascadeOnUpdate()
                ->restrictOnDelete()
                ->comment('ИД клуба');
            $table->timestamp('check_in')->comment('Время прихода');
            $table->timestamp('check_out')->nullable()
                ->comment('Время ухода');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_times');
    }
};
