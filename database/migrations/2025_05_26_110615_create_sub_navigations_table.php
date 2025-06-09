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
        Schema::create('sub_navigations', function (Blueprint $table) {
            $table->id();

            $table->string("sub_name")->comment("Sub-navigation name");
            $table->string("slug")->unique()->comment("Unique slug for sub-navigation");

            $table->boolean('published')->nullable()->default(true);
            $table->unsignedBigInteger('navigation_id');
            $table->foreign('navigation_id')->references('id')->on('navigations')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_navigations');
    }
};
