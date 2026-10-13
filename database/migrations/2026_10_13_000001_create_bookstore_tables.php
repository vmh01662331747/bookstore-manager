<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 30)->default('indigo');
            $table->string('icon', 50)->default('bi-journal-bookmark');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nationality', 100)->nullable();
            $table->string('birth_year', 20)->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 30)->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->unsignedSmallInteger('pages')->nullable();
            $table->decimal('cost_price', 12, 0)->default(0);
            $table->decimal('price', 12, 0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5);
            $table->unsignedInteger('sold_count')->default(0);
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->string('cover_color', 30)->default('#4f46e5');
            $table->string('cover_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->unique();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('tier', 20)->default('bronze'); // bronze, silver, gold, diamond
            $table->unsignedInteger('points')->default(0);
            $table->decimal('total_spent', 14, 0)->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 20)->default('pos'); // pos, online
            $table->decimal('subtotal', 14, 0)->default(0);
            $table->decimal('discount_amount', 14, 0)->default(0);
            $table->decimal('total_amount', 14, 0)->default(0);
            $table->string('payment_method', 30)->default('cash'); // cash, qr_transfer, card
            $table->string('status', 20)->default('completed'); // pending, completed, cancelled
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 0);
            $table->decimal('cost_price', 12, 0)->default(0);
            $table->decimal('line_total', 14, 0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
    }
};
