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
        Schema::table('applicant_forms', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('slug');
            $table->timestamp('expire_date')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
