<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();

            // Foreign Keys
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('dependency_task_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status')->default('Pending');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes wajib untuk agregasi Dashboard
            $table->index('project_id');
            $table->index('status');
            $table->index('deadline');
            $table->index('assigned_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
