<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('owner_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });

        if (Schema::hasTable('users')) {
            $firstAdminId = DB::table('users')
                ->where('role', 'admin')
                ->orderBy('id')
                ->value('id');

            if ($firstAdminId) {
                DB::table('properties')
                    ->whereNull('owner_id')
                    ->update(['owner_id' => $firstAdminId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });
    }
};
