<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->cascadeOnDelete();
            $table->json('personal_details')->nullable();
            $table->json('employment_history')->nullable();
            $table->json('education')->nullable();
            $table->json('skills')->nullable();
            $table->longText('summary')->nullable();
            $table->json('additional_sections')->nullable();
            $table->json('customize')->nullable();
            $table->string('slug');
            $table->boolean('ready')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_v_s');
    }
};
