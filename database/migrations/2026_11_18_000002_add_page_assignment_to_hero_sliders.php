<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A hero slider can be assigned to one page from the admin (no shortcode needed). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_sliders', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable()->after('shortcode')->constrained('pages')->nullOnDelete();
            $table->string('placement', 10)->default('top')->after('page_id'); // top | bottom
        });
    }

    public function down(): void
    {
        Schema::table('hero_sliders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('page_id');
            $table->dropColumn('placement');
        });
    }
};
