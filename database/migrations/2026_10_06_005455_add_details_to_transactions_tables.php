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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('member_number')->nullable()->after('customer_name');
            $table->decimal('subtotal', 12, 2)->default(0)->after('customer_name');
            $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            $table->decimal('tax', 12, 2)->default(0)->after('discount');
            $table->decimal('other_fee', 12, 2)->default(0)->after('tax');
            $table->string('status')->default('selesai')->after('payment_method'); // selesai, ditahan, batal
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('product_name');
            $table->decimal('discount', 12, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['member_number', 'subtotal', 'discount', 'tax', 'other_fee', 'status']);
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['product_code', 'discount']);
        });
    }
};
