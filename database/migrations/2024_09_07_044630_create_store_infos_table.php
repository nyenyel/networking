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
        Schema::create('store_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('invited_by')->nullable();
            $table->float('points')->default(0);
            $table->float('points_limit')->default(0); // non-changing limit
            $table->boolean('unpaid')->default(1);
            $table->date('last_redeemed')->nullable();
            $table->boolean('status')->default(0);
            $table->integer('invited_users_count')->default(0);
            $table->string('invitation_code')->nullable(); // Add this column for invitation code
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invited_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_infos');
    }
};
