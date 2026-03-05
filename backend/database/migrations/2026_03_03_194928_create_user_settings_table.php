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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('profile_visibility', ['public', 'private', 'friends'])->default('public');
            $table->enum('message_privacy', ['everyone', 'followers', 'none'])->default('everyone');

            $table->boolean('show_activity_status')->default(true);
            $table->boolean('allow_tagging')->default(true);
            $table->boolean('allow_mentions')->default(true);

            $table->boolean('show_comments')->default(true);
            $table->boolean('recieve_notifications')->default(true);
            $table->boolean('share_profile')->default(true);

            $table->boolean('allow_downloads')->default(true);
            $table->boolean('allow_sharing_posts')->default(true);
            $table->boolean('personalized_ads')->default(true);
            $table->boolean('personalized_recommendations')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
