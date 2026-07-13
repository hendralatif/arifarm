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
        // Insert Domba Dombos into categories if it doesn't exist
        $exists = DB::table('categories')->where('slug', 'domba-dombos')->exists();
        if (!$exists) {
            DB::table('categories')->insert([
                'name' => 'Domba Dombos',
                'slug' => 'domba-dombos',
                'description' => 'Domba khas Wonosobo dengan bulu wol lebat berkualitas tinggi, tubuh besar, dan sangat diminati untuk peternakan maupun qurban.',
                'image' => 'https://images.unsplash.com/photo-1484557985045-edf25e08da73?w=600&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->where('slug', 'domba-dombos')->delete();
    }
};
