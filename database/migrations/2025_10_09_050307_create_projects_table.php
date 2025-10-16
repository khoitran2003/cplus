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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('clientId')->nullable();
            $table->unsignedBigInteger('userId');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('clientId')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
