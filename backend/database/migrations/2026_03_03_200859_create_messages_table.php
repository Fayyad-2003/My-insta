<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('reply_to_id')->constrained('messages')->cascadeOnDelete();

            $table->text('content')->nullable();
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->timestamp('deleted_for_everyone_at')->nullable();

            $table->boolean('is_system')->default(false);
            $table->json('metadata')->nullable();

            $table->softDeletes();
            $table->index(['conversation_id', 'sender_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
