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
            $table->string('criteria_name');
            $table->unsignedBigInteger('criteriaTypeId')->nullable();
            $table->unsignedBigInteger('parentId')->nullable();
            $table->unsignedBigInteger('clientId')->nullable();
            $table->decimal('criteriaPercent', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('criteriaTypeId')->references('id')->on('criteria_type')->onDelete('set null');
            $table->foreign('parentId')->references('id')->on('criteria')->onDelete('set null');
            $table->foreign('clientId')->references('id')->on('clients')->onDelete('set null');
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
