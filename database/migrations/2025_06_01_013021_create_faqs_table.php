<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('general'); // general, productos, talleres, envios
            $table->integer('order')->default(1);
            $table->boolean('featured')->default(false); // para destacar FAQs importantes
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('views_count')->default(0); // contador de visualizaciones
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index(['featured', 'status']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
