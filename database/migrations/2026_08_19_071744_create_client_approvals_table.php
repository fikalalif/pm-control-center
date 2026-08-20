<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected, Waiting Feedback

            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable(); // Nama orang dari pihak client
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_approvals');
    }
};
