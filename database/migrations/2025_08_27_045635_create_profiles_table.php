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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('cv')->nullable()->constrained('files')->cascadeOnDelete();
            $table->foreignId('avatar')->nullable()->constrained('files')->cascadeOnDelete();
            $table->longText('bio')->nullable();
            $table->string('education')->nullable();
            $table->string('college')->nullable();
            $table->string('position')->nullable();
            $table->string('last_job')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
