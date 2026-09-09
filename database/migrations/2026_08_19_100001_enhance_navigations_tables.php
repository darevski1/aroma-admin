<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable()->after('slug');
            $table->string('url')->nullable()->after('page_id')->comment('Custom URL for non-page links');
            $table->boolean('published')->default(true)->after('url');
            $table->integer('position')->default(0)->after('published');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
        });

        Schema::table('sub_navigations', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable()->after('slug');
            $table->string('url')->nullable()->after('page_id')->comment('Custom URL for non-page links');
            $table->integer('position')->default(0)->after('published');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn(['page_id', 'url', 'published', 'position']);
        });

        Schema::table('sub_navigations', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn(['page_id', 'url', 'position']);
        });
    }
};
