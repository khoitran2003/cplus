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
        Schema::create('criteria_industry', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criteriaId');
            $table->unsignedBigInteger('industryId');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('criteriaId')->references('id')->on('criteria')->onDelete('cascade');
            $table->foreign('industryId')->references('id')->on('industry')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['criteriaId', 'industryId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_industry');
    }
};
