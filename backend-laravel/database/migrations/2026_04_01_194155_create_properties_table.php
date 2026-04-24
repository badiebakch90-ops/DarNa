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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type', 50);
            $table->string('eyebrow')->nullable();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('location');
            $table->text('summary');
            $table->text('description');
            $table->text('story');
            $table->unsignedInteger('nightly_rate');
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedTinyInteger('max_guests')->default(1);
            $table->unsignedTinyInteger('bedrooms_count')->nullable();
            $table->unsignedTinyInteger('bathrooms_count')->nullable();
            $table->text('thumbnail_image')->nullable();
            $table->string('type_badge_color')->nullable();
            $table->text('gradient')->nullable();
            $table->json('facts')->nullable();
            $table->json('amenities')->nullable();
            $table->json('local_spots')->nullable();
            $table->json('listing_tags')->nullable();
            $table->text('map_label')->nullable();
            $table->decimal('map_lat', 10, 7)->nullable();
            $table->decimal('map_lng', 10, 7)->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
