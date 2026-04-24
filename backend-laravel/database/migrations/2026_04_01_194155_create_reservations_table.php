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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('property_slug');
            $table->string('property_name');
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->unsignedTinyInteger('adults_count');
            $table->unsignedTinyInteger('children_count')->default(0);
            $table->unsignedTinyInteger('guests_count');
            $table->unsignedSmallInteger('nights_count');
            $table->unsignedInteger('nightly_rate');
            $table->unsignedInteger('service_fee');
            $table->unsignedInteger('city_tax');
            $table->unsignedInteger('total_amount');
            $table->unsignedInteger('deposit_amount');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('source', 40)->default('website');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
