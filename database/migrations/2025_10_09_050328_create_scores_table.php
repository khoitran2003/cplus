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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_location_id')->constrained('project_locations')->onDelete('cascade');
            $table->foreignId('criteria_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['project_location_id', 'criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
