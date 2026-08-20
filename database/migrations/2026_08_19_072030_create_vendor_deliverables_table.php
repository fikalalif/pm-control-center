<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->string('status')->default('Pending');
            $table->string('quality_status')->nullable();
            $table->boolean('pending_revision')->default(false);

            $table->timestamp('last_follow_up_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes sesuai PRD
            $table->index('project_id');
            $table->index('vendor_id');
            $table->index('status');
            $table->index('deadline');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_deliverables');
    }
};
