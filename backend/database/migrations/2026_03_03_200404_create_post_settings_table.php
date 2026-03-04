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
        Schema::create('post_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->enum('visibility', ['public', 'private', 'friends_only', 'followers_only'])->default('public');
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_share')->default(true);
            $table->boolean('allow_reactions')->default(true);

            $table->enum('post_type', ['text', 'image', 'video', 'link', 'poll'])->default('text');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->boolean('is_pinned')->default(false);
            $table->boolean('allow_tagging')->default(true);
            $table->boolean('is_mentions')->default(true);
            $table->boolean('has_location')->default(false);

            $table->boolean('notify_on_comment')->default(true);
            $table->boolean('notify_on_like')->default(true);
            $table->boolean('notify_on_share')->default(true);

            $table->string('caption_color', 10)->nullable();
            $table->integer('caption_size')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_settings');
    }
};
