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
        Schema::create('payment_histroys', function (Blueprint $table) {
            $table->id();
            $table->string('tinkoff_payment_id')->unique()->comment('ID плаётжки тбанка');
            $table->string('tinkoff_order_id')->index()->comment('ID заказа в форме тбанка');
            $table->integer('amount')->comment('Итоговая сумма к оплате');
            $table->string('status')->default('new')->index()->comment('Статус платежа');
            $table->text('payment_url')->nullable()->comment('Ссылка на форму оплаты');
            $table->json('metadata')->nullable()->comment('Мета данный о календарном событие');
            $table->timestamp('paid_at')->nullable()->comment('Дата оплаты');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_histroys');
    }
};
