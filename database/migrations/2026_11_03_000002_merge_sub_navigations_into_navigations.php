<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
        });

        Schema::table('navigations', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('navigations')->cascadeOnDelete();
        });

        // Move every sub-navigation row into navigations, keeping hierarchy and order.
        $usedSlugs = DB::table('navigations')->pluck('slug')->flip()->all();
        $idMap = [];
        $subs = DB::table('sub_navigations')->orderBy('id')->get();

        foreach ($subs as $sub) {
            $slug = $sub->slug;
            $suffix = 2;
            while (isset($usedSlugs[$slug])) {
                $slug = $sub->slug . '-' . $suffix++;
            }
            $usedSlugs[$slug] = true;

            $idMap[$sub->id] = DB::table('navigations')->insertGetId([
                'parent_id' => null,
                'title' => $sub->sub_name,
                'slug' => $slug,
                'page_id' => $sub->page_id,
                'url' => $sub->url,
                'published' => $sub->published ?? true,
                'position' => $sub->position,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
            ]);
        }

        foreach ($subs as $sub) {
            $parentId = $sub->parent_sub_navigation_id
                ? ($idMap[$sub->parent_sub_navigation_id] ?? $sub->navigation_id)
                : $sub->navigation_id;
            DB::table('navigations')->where('id', $idMap[$sub->id])->update(['parent_id' => $parentId]);
        }

        Schema::dropIfExists('sub_navigations');
    }

    public function down(): void
    {
        Schema::create('sub_navigations', function (Blueprint $table) {
            $table->id();
            $table->string('sub_name');
            $table->string('slug')->unique();
            $table->boolean('published')->nullable()->default(true);
            $table->unsignedBigInteger('navigation_id');
            $table->unsignedBigInteger('parent_sub_navigation_id')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('url')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->foreign('navigation_id')->references('id')->on('navigations')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
        });

        // Only the first nesting level can be restored; deeper items are re-attached to their root menu.
        $rootOf = function ($id) {
            while (($parent = DB::table('navigations')->where('id', $id)->value('parent_id')) !== null) {
                $id = $parent;
            }

            return $id;
        };

        foreach (DB::table('navigations')->whereNotNull('parent_id')->orderBy('id')->get() as $nav) {
            DB::table('sub_navigations')->insert([
                'id' => $nav->id,
                'sub_name' => $nav->title,
                'slug' => $nav->slug,
                'published' => $nav->published,
                'navigation_id' => $rootOf($nav->id),
                'page_id' => $nav->page_id,
                'url' => $nav->url,
                'position' => $nav->position,
                'created_at' => $nav->created_at,
                'updated_at' => $nav->updated_at,
            ]);
        }

        DB::table('navigations')->whereNotNull('parent_id')->delete();

        Schema::table('navigations', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });

        Schema::table('navigations', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
        });
    }
};
