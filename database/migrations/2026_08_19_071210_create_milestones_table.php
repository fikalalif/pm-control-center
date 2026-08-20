<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->string('milestone_code')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('progress_percentage')->default(0);

            // Foreign Keys
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dependency_milestone_id')->nullable()->constrained('milestones')->nullOnDelete();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('project_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
