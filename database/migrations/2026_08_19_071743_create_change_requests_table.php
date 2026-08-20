<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('cr_code')->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->text('description');

            $table->string('requested_by')->nullable(); // Bisa diisi nama client/PM
            $table->timestamp('requested_at')->nullable();
            $table->string('impact')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected

            $table->date('decision_date')->nullable();
            $table->text('decision_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
