<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cenobots_clients', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name');
            $table->string('foot_text')->nullable()->after('description');
            // Optional project photo, picked from the existing Media Library.
            $table->unsignedBigInteger('photo_media_id')->nullable()->index()->after('logo_alt');
        });

        // The "Проекти" section used to be three hard-coded cards. Move them into
        // the table once so the existing projects survive and can be edited in the admin.
        if (DB::table('cenobots_clients')->count() === 0) {
            $now = now();

            foreach ((array) config('cenobots.projects.items', []) as $i => [$category, $name, $description]) {
                DB::table('cenobots_clients')->insert([
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'foot_text' => 'CenoBots автономно чистење',
                    'sort_order' => $i + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('cenobots_clients', function (Blueprint $table) {
            $table->dropColumn(['category', 'foot_text', 'photo_media_id']);
        });
    }
};
