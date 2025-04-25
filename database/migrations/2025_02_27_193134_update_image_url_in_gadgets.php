<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->text('image_url')->change(); // Змінюємо VARCHAR(255) на TEXT
        });
    }

    public function down()
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->string('image_url', 255)->change(); // Повертаємо назад VARCHAR(255), якщо потрібно
        });
    }
};
