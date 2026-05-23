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
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();           // e.g. SAVE20
        $table->enum('type', ['percent', 'flat']);  // percent=20% off, flat=₹100 off
        $table->decimal('value', 8, 2);            // 20 for 20%, 100 for ₹100
        $table->decimal('min_order', 8, 2)->default(0); // Minimum cart total to apply
        $table->integer('max_uses')->default(100);  // Max times it can be used
        $table->integer('used_count')->default(0);  // How many times used so far
        $table->boolean('is_active')->default(true);
        $table->timestamp('expires_at')->nullable(); // Expiry date
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
