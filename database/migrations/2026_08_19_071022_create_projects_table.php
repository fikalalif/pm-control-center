<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('name');

            // Foreign Keys sesuai relasi database
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->foreignId('project_manager_id')->constrained('users');
            $table->foreignId('current_phase_id')->nullable()->constrained('project_phases')->nullOnDelete();

            // Kolom Data Project
            $table->date('start_date')->nullable();
            $table->date('target_completion');
            $table->integer('progress_percentage')->default(0);

            // Untuk tahap awal, Status, Health Override, dan Priority pakai string (nantinya bisa di-mapping ke Enum)
            $table->string('status')->default('Pending');
            $table->string('health_override')->nullable();
            $table->string('priority')->default('Medium');

            $table->text('description')->nullable();
            $table->timestamp('last_update_at')->nullable();
            $table->text('next_action')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Wajib ada untuk entity penting

            // Recommended Database Indexes untuk performa Dashboard
            $table->index('status');
            $table->index('priority');
            $table->index('target_completion');
            $table->index('health_override');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
