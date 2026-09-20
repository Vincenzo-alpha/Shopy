<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sk_deal_completion', function (Blueprint $table) {
            $table->string('completion_key', 50)->nullable()->after('deal_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sk_deal_completion', function (Blueprint $table) {
            $table->dropColumn('completion_key');
        });
    }
};
