<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hubstaff_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('project_id');
            $table->string('assignee_id')->nullable();
            $table->string('hubstaff_id')->nullable();
            $table->string('status')->default('open');
            $table->string('priority')->default('medium');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hubstaff_tickets');
    }
};