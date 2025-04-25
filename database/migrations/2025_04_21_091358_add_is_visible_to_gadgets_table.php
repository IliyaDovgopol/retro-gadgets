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
			$table->boolean('is_visible')->default(false)->after('slug');
		});
	}

	public function down()
	{
		Schema::table('gadgets', function (Blueprint $table) {
			$table->dropColumn('is_visible');
		});
	}

};
