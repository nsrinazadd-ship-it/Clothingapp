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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->string('size');
            $table->text('description');
            $table->string(column: 'image');
             // إضافة السعر (نوع float أو decimal)
            $table->decimal('price', 8, 2);
             // 8 digits in total, 2 after the decimal point
$table->foreignId('subcategory_id')->constrained('sub_categories')->onDelete('cascade');
$table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

        // إضافة الكمية (عدد صحيح)
         $table->integer('quantity');

             // ← هذا السطر أضفناه


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
