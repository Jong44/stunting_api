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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('recipe_id');
            $table->text('ingredient');
            $table->timestamps();
        });

        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->foreign('recipe_id')->references('id')->on('reseps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
