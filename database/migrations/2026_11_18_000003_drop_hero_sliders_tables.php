<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** The Hero Slider feature was removed completely. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('hero_sliders');
        Storage::disk('public')->deleteDirectory('hero-sliders');
    }

    public function down(): void
    {
        // Feature removed; nothing to restore.
    }
};
