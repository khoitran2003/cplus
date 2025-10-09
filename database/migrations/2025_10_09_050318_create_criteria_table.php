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
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['cost', 'infrastructure', 'logistics', 'regulatory', 'market', 'other'])->default('other');
            $table->decimal('weight', 5, 2)->default(1.00); // Trọng số từ 0-10
            $table->enum('scoring_type', ['numeric', 'percentage', 'rating'])->default('numeric');
            $table->decimal('max_score', 10, 2)->default(100.00);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};
