<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User who owns the code
            $table->string('code')->unique(); // Unique invitation code
            $table->integer('used_count')->default(0); // Number of times the code has been used
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_codes');
    }
};
