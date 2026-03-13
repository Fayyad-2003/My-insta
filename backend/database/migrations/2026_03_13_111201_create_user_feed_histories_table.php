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
        Schema::create('user_feed_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('content_id');

            $table->string('action')->default('view');
            $table->string('content_type');
            $table->boolean('is_ai_generated')->default(false);

            $table->decimal('score', 8, 2)->default(0);
            $table->index(['content_id', 'content_type']);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_feed_histories');
    }
};
