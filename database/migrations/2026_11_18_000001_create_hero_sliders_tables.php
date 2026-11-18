<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hero Sliders: each slider is placed on the website through its shortcode
 * (e.g. [2026-1]); its slides live in their own table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // "{year}-{id}", written right after insert; used in the [....] shortcode.
            $table->string('shortcode', 32)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('autoplay')->default(true);
            $table->unsignedInteger('interval_ms')->default(6000);
            $table->string('transition', 10)->default('fade'); // fade | slide
            $table->unsignedInteger('transition_ms')->default(700);
            $table->boolean('show_arrows')->default(true);
            $table->boolean('show_dots')->default(true);
            $table->boolean('pause_on_hover')->default(true);
            // Stretch to the full screen width even when placed in a narrow column.
            $table->boolean('full_width')->default(true);
            $table->unsignedSmallInteger('height_desktop')->default(80); // vh
            $table->unsignedSmallInteger('height_mobile')->default(70);  // vh
            $table->timestamps();
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_slider_id')->constrained('hero_sliders')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_url', 2048)->nullable();
            $table->boolean('button_new_tab')->default(false);
            $table->boolean('link_whole_slide')->default(false);
            $table->string('desktop_image')->nullable();
            $table->unsignedInteger('desktop_width')->nullable();
            $table->unsignedInteger('desktop_height')->nullable();
            $table->string('mobile_image')->nullable();
            $table->unsignedInteger('mobile_width')->nullable();
            $table->unsignedInteger('mobile_height')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('image_position', 20)->default('center center');
            $table->string('text_alignment', 10)->default('left'); // left | center | right
            $table->unsignedTinyInteger('overlay_opacity')->default(45); // 0–90 %
            $table->timestamps();

            $table->index(['hero_slider_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('hero_sliders');
    }
};
