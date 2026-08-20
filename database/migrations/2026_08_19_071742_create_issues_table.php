<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_code')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('impact')->nullable();

            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('action')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('Open');
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('project_id');
            $table->index('status');
            $table->index('impact');
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
