<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_navigations', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_sub_navigation_id')->nullable()->after('navigation_id');
            $table->foreign('parent_sub_navigation_id')->references('id')->on('sub_navigations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sub_navigations', function (Blueprint $table) {
            $table->dropForeign(['parent_sub_navigation_id']);
            $table->dropColumn('parent_sub_navigation_id');
        });
    }
};
