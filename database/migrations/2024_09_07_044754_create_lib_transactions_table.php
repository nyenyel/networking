<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lib_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('desc');
            $table->timestamps();
        });
        DB::table('lib_transactions')->upsert([
            ['desc' => 'Redeem'],
        ], ['desc']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_transactions');
    }
};
