<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('gadgets', function (Blueprint $table) {
        $table->longText('article')->nullable()->after('image_url'); // Додаємо стовпець після image_url
    });
}

public function down()
{
    Schema::table('gadgets', function (Blueprint $table) {
        $table->dropColumn('article');
    });
}

};
