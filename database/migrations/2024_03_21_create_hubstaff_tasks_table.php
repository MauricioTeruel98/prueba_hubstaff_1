<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hubstaff_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('hubstaff_id')->nullable();
            $table->string('project_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->datetime('due_date')->nullable();
            $table->string('assignee_id')->nullable();
            $table->integer('lock_version')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hubstaff_tasks');
    }
}; 