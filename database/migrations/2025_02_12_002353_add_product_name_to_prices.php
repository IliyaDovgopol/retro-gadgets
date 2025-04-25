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
		Schema::table('prices', function (Blueprint $table) {
			$table->string('product_name')->nullable()->after('source');
		});
	}

	public function down()
	{
		Schema::table('prices', function (Blueprint $table) {
			$table->dropColumn('product_name');
		});
	}

};
