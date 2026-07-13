<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The status column is already a varchar(50) from previous migration.
        // We just need to ensure 'mati' is a valid value the app can store.
        // No schema change needed since the column is already a string type.
        // This migration is intentionally a no-op for schema,
        // but serves as documentation that 'mati' is now a valid status.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert any 'mati' goats back to 'not_for_sale' on rollback
        DB::table('goats')->where('status', 'mati')->update(['status' => 'not_for_sale']);
    }
};
