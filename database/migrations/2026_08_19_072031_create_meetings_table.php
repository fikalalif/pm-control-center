<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('type'); // Meeting atau Action
            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('meeting_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Open');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
