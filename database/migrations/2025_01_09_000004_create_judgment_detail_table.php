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
        Schema::create('judgment_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sessionId')->nullable();
            $table->unsignedBigInteger('criteriaId');
            $table->unsignedBigInteger('projectId')->nullable();
            $table->double('criteria_point')->nullable();
            $table->integer('criteria_percentage');
            $table->unsignedBigInteger('criteria_parent_id')->nullable();
            $table->integer('criteria_type')->nullable();
            $table->string('criteria_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('criteriaId')->references('id')->on('criteria')->onDelete('cascade');
            $table->foreign('projectId')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('criteria_parent_id')->references('id')->on('criteria')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judgment_detail');
    }
};
