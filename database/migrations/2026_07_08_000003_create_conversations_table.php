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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
            $table->unique(['user_one_id', 'user_two_id']);
            $table->timestamps();
        });

        // Add conversation_id to messages
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('cascade');
        });

        // Backfill existing messages
        $messages = DB::table('messages')->get();
        foreach ($messages as $msg) {
            $userOne = min($msg->sender_id, $msg->receiver_id);
            $userTwo = max($msg->sender_id, $msg->receiver_id);

            // Find or create conversation
            $convId = DB::table('conversations')
                ->where('user_one_id', $userOne)
                ->where('user_two_id', $userTwo)
                ->value('id');

            if (!$convId) {
                $convId = DB::table('conversations')->insertGetId([
                    'user_one_id' => $userOne,
                    'user_two_id' => $userTwo,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::table('messages')->where('id', $msg->id)->update([
                'conversation_id' => $convId
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });

        Schema::dropIfExists('conversations');
    }
};
