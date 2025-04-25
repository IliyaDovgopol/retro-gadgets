<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            if (!Schema::hasColumn('prices', 'link_hash')) {
                $table->string('link_hash')->nullable()->after('link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('link_hash');
        });
    }
};
