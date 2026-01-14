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
        Schema::table('user_states', function (Blueprint $table) {
            $table->json('step')->nullable()->after('access_level_id')
                ->comment('Раздел маркап меню');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_states', function (Blueprint $table) {
            //
        });
    }
};
