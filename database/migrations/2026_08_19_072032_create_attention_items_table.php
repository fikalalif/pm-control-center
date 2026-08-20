<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attention_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('problem');
            $table->string('pic')->nullable(); // PIC bisa orang luar, jadi string saja
            $table->date('deadline')->nullable();
            $table->text('recommended_action')->nullable();
            $table->string('status')->default('Open'); // Open, In Progress, Resolved, Closed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attention_items');
    }
};
