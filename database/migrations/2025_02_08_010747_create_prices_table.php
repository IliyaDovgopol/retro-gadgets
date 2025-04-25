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
		Schema::create('prices', function (Blueprint $table) {
			$table->id();
			$table->foreignId('gadget_id')->constrained()->onDelete('cascade');
			$table->string('source'); // eBay, AliExpress, Rozetka
			$table->decimal('price', 10, 2)->nullable();
			$table->string('link')->nullable();
			$table->timestamps();
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
