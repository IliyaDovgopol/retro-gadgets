<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('link'); // Додаємо колонку для зображень
        });
    }

    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};

