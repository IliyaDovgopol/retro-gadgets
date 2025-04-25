<?php

// database/migrations/xxxx_xx_xx_add_published_at_to_gadgets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('gadgets', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};
