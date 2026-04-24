<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_method', 40)->default('bank_transfer')->after('notes');
            $table->string('payment_status', 60)->default('awaiting_bank_transfer')->after('payment_method');
            $table->string('payment_reference', 80)->nullable()->after('payment_status');
            $table->dateTime('cash_meeting_at')->nullable()->after('payment_reference');
            $table->string('cash_meeting_place')->nullable()->after('cash_meeting_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_reference',
                'cash_meeting_at',
                'cash_meeting_place',
            ]);
        });
    }
};
