<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->text('image_url')->nullable()->change(); // Дозволяємо NULL перед зміною типу
        });
    }

    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->string('image_url', 255)->nullable()->change(); // Повертаємо назад VARCHAR(255)
        });
    }
};
