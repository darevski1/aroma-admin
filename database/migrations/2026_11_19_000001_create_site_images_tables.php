<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every image the public site renders, per page (filled by the image scanner / on first render).
        Schema::create('site_image_slots', function (Blueprint $table) {
            $table->id();
            $table->string('path', 255);          // public URL path, e.g. "/" or "/modeli/l50"
            $table->string('src_key', 500);       // normalised ORIGINAL image URL
            $table->string('kind', 10)->default('img'); // img | bg | css
            $table->string('alt', 500)->nullable();
            $table->string('context', 255)->nullable(); // nearest heading, to tell sections apart
            $table->string('page_title', 255)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['path', 'src_key'], 'site_image_slots_unique');
        });

        // Admin replacements. scope_path "*" = every page that shows this image; otherwise one page only.
        Schema::create('site_image_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('scope_path', 255)->default('*');
            $table->string('src_key', 500);
            $table->unsignedBigInteger('media_id')->nullable();
            $table->unsignedBigInteger('mobile_media_id')->nullable();
            $table->string('alt', 500)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('object_position', 40)->nullable();
            $table->string('object_fit', 20)->nullable();
            $table->boolean('hidden')->default(false);
            $table->timestamps();

            $table->unique(['scope_path', 'src_key'], 'site_image_overrides_unique');
            $table->index('media_id');
            $table->index('mobile_media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_image_overrides');
        Schema::dropIfExists('site_image_slots');
    }
};
