<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // إضافة العمود مع إمكانية أن يكون nullable في البداية لتجنب مشاكل البيانات القديمة
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            
            // إضافة المفتاح الأجنبي
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // حذف المفتاح الأجنبي
            $table->dropForeign(['product_variant_id']);
            // حذف العمود
            $table->dropColumn('product_variant_id');
        });
    }
};
