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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('duration'); // en minutos
            $table->decimal('price', 10, 2);
            $table->integer('max_participants');
            $table->integer('current_participants')->default(0);
            $table->string('location');
            $table->string('instructor')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('difficulty_level', ['principiante', 'intermedio', 'avanzado'])->default('principiante');
            $table->boolean('materials_included')->default(false);
            $table->text('requirements')->nullable();
            $table->enum('status', ['active', 'inactive', 'full', 'cancelled'])->default('active');
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['date', 'status']);
            $table->index(['status', 'difficulty_level']);
            $table->index('current_participants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
