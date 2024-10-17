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
        Schema::create('weekly_dashboard_monitorings', function (Blueprint $table) {
            $table->id();
            $table->integer('package_sold')->default(0);
            $table->decimal('product_purchased', 10, 2)->default(0);
            $table->decimal('company_revenue', 10, 2)->default(0);
            $table->decimal('members_commission', 10, 2)->default(0); //redeemable
            $table->decimal('wallet', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_dashboard_monitorings');
    }
};
