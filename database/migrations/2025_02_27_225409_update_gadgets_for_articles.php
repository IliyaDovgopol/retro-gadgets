<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->text('intro')->nullable()->after('description'); // Вступ
            $table->text('history')->nullable()->after('intro'); // Історія
            $table->text('unique_features')->nullable()->after('history'); // Унікальні особливості
            $table->text('competition')->nullable()->after('unique_features'); // Конкуренти
            $table->text('legacy')->nullable()->after('competition'); // Чи вплинув на ринок
            $table->text('fun_facts')->nullable()->after('legacy'); // Цікаві факти
            $table->text('price_history')->nullable()->after('fun_facts'); // Як змінювалася ціна
        });
    }

    public function down() {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->dropColumn(['intro', 'history', 'unique_features', 'competition', 'legacy', 'fun_facts', 'price_history']);
        });
    }
};
