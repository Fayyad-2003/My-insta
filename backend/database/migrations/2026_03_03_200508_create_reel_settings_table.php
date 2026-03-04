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
        Schema::create('reel_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reel_id')->constrained('reel')->onDelete('cascade');
            $table->enum('audience', ['public', 'followers', 'close_friends'])->default('public');

            $table->boolean('share_to_threads')->default(false);
            $table->boolean('share_to_facebook')->default(false);
            $table->boolean('share_to_story')->default(false);

            $table->boolean('allow_use_template')->default(false);
            $table->boolean('enable_captions')->default(false);

            $table->string('caption_color', 10)->nullable()->default('#ffffff');
            $table->integer('caption_size')->nullable()->default(22);

            $table->boolean('allow_tagging')->default(true);
            $table->boolean('allow_mentions')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reel_settings');
    }
};
