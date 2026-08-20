<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('risk_code')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Risk Matrix
            $table->string('probability')->nullable();
            $table->string('impact')->nullable();
            $table->string('risk_level')->default('Low'); // Low, Medium, High, Critical

            $table->text('mitigation')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('Open');
            $table->date('due_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('project_id');
            $table->index('status');
            $table->index('risk_level');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
